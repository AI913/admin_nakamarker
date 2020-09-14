@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    {{ $register_mode == "create" ? '顧客新規登録' : '顧客編集' }}
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">
        <a href="{{ route('admin/user') }}">顧客一覧</a>
    </li>
    <li class="breadcrumb-item">{{ $register_mode == "create" ? '顧客新規登録' : '顧客編集' }}</li>
@endsection

@section('app_content')
    {{-- メイン --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{ $register_mode == "create" ? '顧客新規登録　' : '顧客編集　' }}<span class="text-danger">※は必須入力</span>
                    @if ($register_mode == "edit")
                        @if ($data->status == config('const.user_app_account_stop'))
                            <button type="button" class="btn btn-dark text-white width-150 float-right" id="btn_account_stop">アカウント停止中</button>
                        @else
                            <button type="button" class="btn btn-danger width-150 float-right" id="btn_account_stop" {{ $data->id === \Auth::user()->id ? 'disabled' : ''}}>アカウントの停止</button>
                        @endif
                    @endif
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('admin/user/save') }}" method="post" id="main_form">
                        {{ csrf_field() }}
                        {{-- アカウント停止ボタン押下時に発動 --}}
                        <input type="hidden" id="status4" name="status" value="{{ $data->status === config('const.user_app_account_stop') ? $data->status : null }}" />
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="name">名前<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'name'])
                                        <input class="form-control required-text" type="text" id="name" name="name" maxlength="50" placeholder="名前" value="{{ $data->name ? $data->name : old('name') }}" data-title="名前" {{ $data->id === \Auth::user()->id ? 'disabled' : ''}}>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="email">メールアドレス<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'email'])
                                        <input class="form-control required-text duplicate-email" type="email" id="email" name="email" maxlength="50" placeholder="メールアドレス" value="{{ $data->email ? $data->email : old('email') }}" data-id="{{ $data->id }}" data-title="メールアドレス" {{ $data->id === \Auth::user()->id ? 'disabled' : ''}}>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="password">パスワード<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        @if($data->id)
                                        {{-- 編集時 --}}
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'password'])
                                        <input class="form-control char-count-text" type="password" id="password" name="password" maxlength="20" placeholder="パスワード" value="" data-count-error-msg="パスワードは６文字以上入力してください" {{ $data->id === \Auth::user()->id ? 'disabled' : ''}}>
                                        <span class="text-primary">変更する場合のみ入力</span>
                                        @else
                                        {{-- 新規登録時 --}}
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'password'])
                                        <input class="form-control required-text char-count-text" type="password" id="password" name="password" maxlength="20" placeholder="パスワード" value="" data-title="パスワード" data-count-error-msg="パスワードは６文字以上入力してください">
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">ステータス<span class="text-danger">※</span></label>
                                    <div class="col-md-9 form-inline" id="status_checked">
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                            {{-- エラーメッセージあれば表示 --}}
                                            @include('admin.layouts.components.error_message', ['title' => 'status'])
                                            <input type="radio" class="custom-control-input" id="status1" name="status" value="{{ config('const.user_app_member') }}" data-status="{{ config('const.user_app_member_name') }}" {{ !$data->status || $data->status == config('const.user_app_member') ? 'checked' : '' }} {{ $data->id === \Auth::user()->id ? 'disabled' : ''}}>
                                            <label class="custom-control-label cursor-pointer" for="status1">{{ config('const.user_app_member_name') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                            <input type="radio" class="custom-control-input" id="status2" name="status" value="{{ config('const.user_app_unsubscribe') }}" data-status="{{ config('const.user_app_unsubscribe_name') }}" {{ $data->status == config('const.user_app_unsubscribe') ? 'checked' : '' }} {{ $data->id === \Auth::user()->id ? 'disabled' : ''}}>
                                            <label class="custom-control-label cursor-pointer" for="status2">{{ config('const.user_app_unsubscribe_name') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                        <input type="radio" class="custom-control-input" id="status3" name="status" value="{{ config('const.user_admin_system') }}" data-status="{{ config('const.user_admin_system_name') }}" {{ $data->status == config('const.user_admin_system') ? 'checked' : '' }} {{ $data->id === \Auth::user()->id ? 'disabled' : ''}}>
                                            <label class="custom-control-label cursor-pointer" for="status3">{{ config('const.user_admin_system_name') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="name">備考</label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'memo'])
                                        <textarea class="form-control" name="memo" id="memo" maxlength="500" rows="10" placeholder="備考" {{ $data->id === \Auth::user()->id ? 'disabled' : ''}}>{{ $data->memo ? $data->memo : old('memo') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="id" value="{{ $data->id }}" />
                                <input type="hidden" id="register_mode" name="register_mode" value="{{ $register_mode }}" />
                                {{-- 選択ユーザが自身の場合は更新ボタンを表示しない --}}
                                @if ($data->id !== \Auth::user()->id)
                                    @include('admin.layouts.components.button.register', ['register_mode' => $register_mode])
                                @endif
                                @include('admin.layouts.components.button.cancel', ['url' => "/user"])
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
    <script src="{{ asset('js/app/user.js') }}"></script>
@endsection
