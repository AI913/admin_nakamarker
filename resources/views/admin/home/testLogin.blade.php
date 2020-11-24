@extends('admin.layouts.app')

@section('app_css')
    {{-- Header --}}
    <link type="text/css" rel="stylesheet" href="https://cdn.firebase.com/libs/firebaseui/3.5.2/firebaseui.css" />
    <style>h1{text-align: center;}</style>
@endsection

@section('app_title')
    {{-- タイトル --}}
    Home
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">Home Firebase</li>
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

    {{-- Firebaseログインページデザイン --}}
    <h1>Firebase Auth for Phonenumber</h1>
    <div id="firebaseui-auth-container"></div>

@endsection

@section('app_js')
    {{-- Firebaseログインページ用Js --}}
    <!-- 以下、Firebase電話番号認証の設定 -->
    <script src="https://www.gstatic.com/firebasejs/5.8.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.8.1/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/ui/3.5.2/firebase-ui-auth__ja.js"></script>
    <script src="{{ asset('js/firebase/config.js') }}"></script>
    <script>
    //----------------------------------------------
    // Firebase UIの設定
    //----------------------------------------------
    var uiConfig = {
        // ログイン完了時のリダイレクト先
        signInSuccessUrl: '/test/done',

        // 利用する認証機能
        signInOptions: [{
            provider: firebase.auth.PhoneAuthProvider.PROVIDER_ID,
            defaultCountry: 'JP',
            //whitelistedCountries: ['JP', '+81']
        }],

        // 利用規約のURL(任意で設定)
        tosUrl: 'http://example.com/kiyaku/',
        // プライバシーポリシーのURL(任意で設定)
        privacyPolicyUrl: 'https://miku3.net/privacy.html'
        };

        var ui = new firebaseui.auth.AuthUI(firebase.auth());
        ui.start('#firebaseui-auth-container', uiConfig);
    </script>
@endsection
