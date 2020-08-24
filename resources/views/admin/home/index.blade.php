@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    Home
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">Home</li>
@endsection

@section('app_content')
    
    {{-- メッセージModal読み込み --}}
    @include('admin.layouts.components.message', ['title' => 'メッセージ'])

    {{-- 削除確認Modal読み込み --}}
    @include('admin.layouts.components.confirm', [
        'id' => 'confirm_modal', 'title' => '削除確認', 'message' => '対象データを削除します。よろしいですか？', 'btn_id' => 'btn_remove'
    ])

    {{-- 削除Form読み込み --}}
    @include('admin.layouts.components.remove_form', ['url' => url('admin/user/remove')])

    {{-- 詳細Modal読み込み --}}
    {{-- @include('admin.user.detail') --}}

@endsection

@section('app_js')
    {{-- アプリ機能用Js --}}
    {{-- <script>
        // let LIMIT_DATE = "{{ $login_reset_time }}";
    </script>
    <script src="{{ asset('js/app/user.js') }}?v={{ config('const.app_version') }}"></script> --}}
@endsection
