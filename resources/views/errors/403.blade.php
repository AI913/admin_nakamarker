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

    <title>403 権限エラー | {{ env('APP_NAME') }}</title>
</head>
<body class="app flex-row align-items-center">
<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="clearfix">
                <h1 class="float-left display-3 mr-4">403</h1>
                <h4 class="pt-3">このページの閲覧権限がありません</h4>
                <p class="text-muted">Forbidden</p>
            </div>
            <div class="input-prepend">
                <a href="/admin" class="btn btn-primary">HOMEへ戻る</a>
            </div>
        </div>
    </div>
</div>
<!-- CoreUI and necessary plugins-->
<script src="http://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
{{--<script src="https://unpkg.com/@coreui/coreui/dist/js/coreui.min.js"></script>--}}
<script src="{{ asset('js/coreui.min.js') }}" ></script>

</body>
</html>
