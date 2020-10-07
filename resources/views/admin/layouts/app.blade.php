<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="{{ asset('css/coreui.min.css')}}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/style.css')}}?v={{ config('const.app_version') }}">

    <link rel="stylesheet" href="{{ asset('css/datetimepicker/jquery.datetimepicker.min.css') }}">

    @yield('app_css')

    <title>@yield('app_title') | {{ env('APP_SYSTEM_NAME') }}</title>
</head>
<body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
<header class="app-header navbar">
    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a class="navbar-brand" href="#">
        <img class="navbar-brand-full" src="{{ asset('images/logo.png') }}" height="80%" alt="logo">
    </a>
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
        <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="nav navbar-nav ml-auto">
        <li class="nav-item dropdown mr-3">
            <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user"></i> {{ \Auth::user()->name }} 様
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="{{ route('admin/logout') }}">
                    <i class="fa fa-lock"></i> ログアウト</a>
            </div>
        </li>
    </ul>
</header>

<div class="app-body">
    <div class="sidebar">
        <nav class="sidebar-nav">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin') }}">
                        <i class="fas fa-home fa-lg"></i> HOME
                    </a>
                </li>
                
                <li class="nav-item nav-dropdown {{ \App\Services\View\ViewService::isMenuOpen('user') }}">
                    <a class="nav-link nav-dropdown-toggle {{ \App\Services\View\ViewService::isMenuActive('user') }}" href="#">
                        <i class="fas fa-user fa-fw"></i> ユーザ管理
                    </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Services\View\ViewService::isMenuActive('user') }}" href="{{ route('admin/user') }}">
                                <i class="fas fa-list-alt fa-fw ml-3"></i> ユーザ一覧
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Services\View\ViewService::isMenuActive('user') }}" href="{{ route('admin/user/create') }}">
                                <i class="fas fa-plus fa-fw ml-3"></i> ユーザ新規登録
                            </a>
                        </li>
                    </ul>
                </li>
            
                <li class="nav-item nav-dropdown {{ \App\Services\View\ViewService::isMenuOpen('marker') }}">
                    <a class="nav-link nav-dropdown-toggle {{ \App\Services\View\ViewService::isMenuActive('marker') }}" href="#">
                        <i class="fas fa-fw fa-map-marker-alt"></i> マーカー管理
                    </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Services\View\ViewService::isMenuActive('marker') }}" href="{{ route('admin/marker') }}">
                                <i class="fas fa-list-alt fa-fw ml-3"></i> マーカー一覧
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Services\View\ViewService::isMenuActive('marker') }}" href="{{ route('admin/marker/create') }}">
                                <i class="fas fa-plus fa-fw ml-3"></i> マーカー新規登録
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item nav-dropdown {{ \App\Services\View\ViewService::isMenuOpen('community') }}">
                    <a class="nav-link nav-dropdown-toggle {{ \App\Services\View\ViewService::isMenuActive('community') }}" href="#">
                        <i class="fas fa-users"></i> コミュニティ管理
                    </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                           <a class="nav-link {{ \App\Services\View\ViewService::isMenuActive('community') }}" href="{{ route('admin/community') }}">
                                <i class="fas fa-list-alt fa-fw ml-3"></i> コミュニティ一覧
                            </a>
                        </li>
                        <li class="nav-item">
                           <a class="nav-link {{ \App\Services\View\ViewService::isMenuActive('community') }}" href="{{ route('admin/community/create') }}">
                                <i class="fas fa-plus fa-fw ml-3"></i> コミュニティ新規登録
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown {{ \App\Services\View\ViewService::isMenuOpen('news') }}">
                    <a class="nav-link nav-dropdown-toggle {{ \App\Services\View\ViewService::isMenuActive('news') }}" href="#">
                        <i class="fas fa-info fa-fw"></i> お知らせ管理
                    </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Services\View\ViewService::isMenuActive('news') }}" href="{{ route('admin/news') }}">
                                <i class="fas fa-list-alt fa-fw ml-3"></i> お知らせ一覧
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Services\View\ViewService::isMenuActive('news') }}" href="{{ route('admin/news/create') }}">
                                <i class="fas fa-plus fa-fw ml-3"></i> お知らせ新規登録
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item nav-dropdown {{ \App\Services\View\ViewService::isMenuOpen('push') }}">
                    <a class="nav-link nav-dropdown-toggle {{ \App\Services\View\ViewService::isMenuActive('push') }}" href="#">
                        <i class="far fa-bell"></i> プッシュ通知管理
                    </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Services\View\ViewService::isMenuActive('push') }}" href="{{ route('admin/push') }}">
                                <i class="fas fa-list-alt fa-fw ml-3"></i> プッシュ通知一覧
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Services\View\ViewService::isMenuActive('push') }}" href="{{ route('admin/push/create') }}">
                                <i class="fas fa-plus fa-fw ml-3"></i> プッシュ通知新規登録
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item nav-dropdown {{ \App\Services\View\ViewService::isMenuOpen('config') }}">
                    <a class="nav-link nav-dropdown-toggle {{ \App\Services\View\ViewService::isMenuActive('config') }}" href="#">
                        <i class="fas fa-cog fa-fw"></i> 共通設定管理
                    </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Services\View\ViewService::isMenuActive('config') }}" href="{{ route('admin/config') }}">
                                <i class="fas fa-list-alt fa-fw ml-3"></i> 共通設定一覧
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ \App\Services\View\ViewService::isMenuActive('config') }}" href="{{ route('admin/config/create') }}">
                                <i class="fas fa-plus fa-fw ml-3"></i> 共通設定新規登録
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
    <main class="main">
        <!-- Breadcrumb-->
        <ol class="breadcrumb">
            @yield('app_bread')
        </ol>
        <div class="container-fluid">
            <div class="animated fadeIn">
                @yield('app_content')
            </div>
        </div>
    </main>
</div>

<footer class="app-footer">
    <div>
{{--        <a href="https://coreui.io/pro/">CoreUI Pro</a>--}}
{{--        <span>© 2018 creativeLabs.</span>--}}
    </div>
</footer>


<!-- CoreUI and necessary plugins-->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
{{--<script src="https://unpkg.com/@coreui/coreui/dist/js/coreui.min.js"></script>--}}
<script src="{{ asset('js/coreui.min.js') }}" ></script>
<script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('js/datetimepicker/jquery.datetimepicker.full.min.js') }}"></script>
<script src="{{ asset('js/vendor/short-and-sweet.min.js') }}" ></script>
{{-- 日付フォーマットのライブラリ --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/locale/ja.js"></script>

<script src="{{ asset('js/common.js') }}?v={{ config('const.app_version') }}" ></script>

@yield('app_js')

</body>
</html>
