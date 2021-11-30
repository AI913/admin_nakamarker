@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    {{ $register_mode == "create" ? 'プッシュ通知新規登録' : 'プッシュ通知編集' }}
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">
        <a href="{{ route('admin/push') }}">プッシュ通知一覧</a>
    </li>
    <li class="breadcrumb-item">{{ $register_mode == "create" ? 'プッシュ通知新規登録' : 'プッシュ通知編集' }}</li>
@endsection

@section('app_content')
    {{-- メイン --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{ $register_mode == "create" ? 'プッシュ通知新規登録　' : 'プッシュ通知編集　' }}<span class="text-danger">※は必須入力</span>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('admin/push/save') }}" method="post" id="main_form">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="name">タイトル<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'title'])
                                        <input class="form-control required-text" type="text" id="title" name="title" maxlength="50" placeholder="タイトル" value="{{ $data->title ? $data->title : old('title') }}" data-title="タイトル">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="content">本文<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'content'])
                                        <textarea class="form-control required-text" name="content" id="content" maxlength="1000" rows="10" placeholder="本文" data-title="本文">{{ $data->content ? $data->content : old('content') }}</textarea>
                                    </div>
                                </div>
                                {{-- <div class="form-group row">
                                    <label class="col-md-3 col-form-label">送信種別<span class="text-danger">※</span></label>
                                    <div class="col-md-9 form-inline" id="type_checked">
                                        <div class="custom-control custom-radio cursor-pointer mr-3"> --}}
                                            {{-- エラーメッセージあれば表示 --}}
                                            {{-- @include('admin.layouts.components.error_message', ['title' => 'type'])
                                            <input type="radio" class="custom-control-input" id="type1" name="type" value="{{ config('const.push_all') }}" data-status="{{ config('const.push_all_name') }}" {{ !$data->type || $data->type == config('const.push_all') || old('type') == config('const.push_all') ? 'checked' : '' }}>
                                            <label class="custom-control-label cursor-pointer" for="type1">{{ config('const.push_all_name') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                            <input type="radio" class="custom-control-input" id="type2" name="type" value="{{ config('const.push_condition') }}" data-status="{{ config('const.push_condition_name') }}" {{ $data->type == config('const.push_condition') || old('type') == config('const.push_condition') ? 'checked' : '' }}>
                                            <label class="custom-control-label cursor-pointer" for="type2">{{ config('const.push_condition_name') }}</label>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="reservation_date" id="reservation_date_label" >送信予約日時<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'reservation_date'])
                                        <input class="form-control datetimepicker width-200 required-text" type="text" id="reservation_date" name="reservation_date" placeholder="送信予約日時" data-title="送信予約日時"
                                         value="{{ $data->reservation_date ? $data->reservation_date : old('reservation_date') }}">
                                    </div>
                                </div>
                                <br>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="memo">備考</label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'memo'])
                                        <textarea class="form-control" name="memo" id="memo" maxlength="500" rows="10" placeholder="備考">{{ $data->memo ? $data->memo : old('memo') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="id" value="{{ $data->id }}" />
                                <input type="hidden" id="register_mode" name="register_mode" value="{{ $register_mode }}" />
                                @include('admin.layouts.components.button.register', ['register_mode' => $register_mode])
                                @include('admin.layouts.components.button.cancel', ['url' => "/admin/push"])
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('app_js')
    {{-- アプリ機能用Js --}}
    <script>
        let register_mode = "{{ $register_mode == "create" ? 'create' : 'edit' }}";
    </script>
    <script src="{{ asset('js/app/push_history.js') }}"></script>
@endsection
