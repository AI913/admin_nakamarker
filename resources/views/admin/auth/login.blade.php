<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
{{--    <link rel="stylesheet" href="https://unpkg.com/@coreui/coreui/dist/css/coreui.min.css">--}}
    <link rel="stylesheet" href="{{ asset('css/coreui.min.css')}}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <link rel="stylesheet" href="{{ asset('css/style.css')}}?v={{ config('const.app_version') }}">

    <title>ログイン | {{ env('APP_SYSTEM_NAME') }}</title>
</head>
<body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
<header class="app-header navbar">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#">
        <img class="navbar-brand-full" src="{{ asset('images/logo.png') }}" height="80%" alt="logo">
    </a>
    <ul class="nav navbar-nav ml-auto"></ul>
</header>
<div class="container">
    @php
    // dd($errors);
@endphp
    <div class="row justify-content-center" style="margin-top: 15%">
        <div class="col-md-6">
            <div class="card-group">
                <div class="card p-4">
                    <div class="card-body">
                        <form action="{{ route('admin/login/submit') }}" method="post">
                            {{ csrf_field() }}
                            <h1 class="text-center"><img src="{{ asset('images/logo.png') }}" width="200" alt="logo" /></h1>
                            <p class="text-muted">サインイン</p>
                            @if ($errors->has('email'))
                                @if (session('admins'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ Session::get('admins') }}</strong>
                                    </span>
                                @else
                                    <span class="help-block">
                                        <strong class="text-danger">メールアドレスまたはパスワードが間違っています</strong>
                                    </span>
                                @endif
                            @endif
                            @php
                                Session::forget('admins')
                            @endphp
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </div>
                                <input class="form-control required-text" type="email" name="email" placeholder="メールアドレス" data-error-msg="メールアドレスを入力してください">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-key"></i>
                                    </span>
                                </div>
                                <input class="form-control required-text" type="password" name="password" placeholder="パスワード" data-error-msg="パスワードを入力してください">
                            </div>
                            <div class="input-group mb-4">
                                <div class="custom-control custom-checkbox cursor-pointer">
                                    <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                    <label class="custom-control-label cursor-pointer" for="remember">ログイン状態を保存</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <button class="btn btn-primary px-4" type="submit" id="btn_login">ログイン</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- CoreUI and necessary plugins-->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
{{--<script src="https://unpkg.com/@coreui/coreui/dist/js/coreui.min.js"></script>--}}
<script src="{{ asset('js/coreui.min.js') }}" ></script>
<script src="{{ asset('js/common.js') }}?v={{ config('const.app_version') }}" ></script>

<script>
    $(function(){
        $('#btn_login').on('click', function(){
            // エラーメッセージの削除
            if($('.text-danger')) {
                $('.text-danger').remove();
            }
            $('.error-area').remove();

            // エラーチェック開始
            let check = true;
            $($('.required-text').get().reverse()).each(function(index, elm){
                if (!isInputValue(elm)) {
                    $(elm).focus();
                    $(elm).parent().after("<p class='error-area text-danger'>"+$(elm).attr("data-error-msg")+"</p>");
                    check = false;
                }
            });
            // エラーがある場合は処理しない
            if (!check) {
                return false;
            }
            return true;
        });
    });
</script>

</body>
</html>
