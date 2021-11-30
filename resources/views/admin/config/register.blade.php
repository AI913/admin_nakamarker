@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    {{ $register_mode == "create" ? '共通設定新規登録' : '共通設定編集' }}
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">
        <a href="{{ route('admin/config') }}">共通設定管理一覧</a>
    </li>
    <li class="breadcrumb-item">{{ $register_mode == "create" ? '共通設定新規登録' : '共通設定編集' }}</li>
@endsection

@section('app_content')
    {{-- メイン --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{ $register_mode == "create" ? '共通設定新規登録　' : '共通設定編集　' }}<span class="text-danger">※は必須入力</span>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('admin/config/save') }}" method="post" id="main_form">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="name">キー<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'key'])
                                        <input class="form-control required-text" type="text" id="key" name="key" maxlength="255" placeholder="キー" value="{{ $data->key ? $data->key : old('key') }}" data-title="キー" {{ $register_mode == "edit" ? 'disabled' : '' }}>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="email">値<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        {{-- @include('admin.layouts.components.error_message', ['title' => 'value']) --}}
                                        <input class="form-control required-text" type="text" id="value" name="value" maxlength="255" placeholder="値" value="{{ $data->value ? $data->value : old('value') }}" data-id="{{ $data->id }}" data-title="値">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="name">備考</label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'memo'])
                                        <textarea class="form-control" name="memo" id="memo" maxlength="500" rows="5" placeholder="備考">{{ $data->memo ? $data->memo : old('memo') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="id" value="{{ $data->id }}" />
                                <input type="hidden" id="register_mode" name="register_mode" value="{{ $register_mode }}" />
                                @include('admin.layouts.components.button.register', ['register_mode' => $register_mode])
                                @include('admin.layouts.components.button.cancel', ['url' => "admin/config"])
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
    <script src="{{ asset('js/app/config.js') }}"></script>
@endsection
