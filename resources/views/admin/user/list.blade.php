@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    ユーザ一覧
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">ユーザ一覧</li>
@endsection

@section('app_content')
    {{-- メイン --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3 d-flex">
                        <div class="col-lg-2">
                            <input type="text" class="form-control search-text" value="" name="id" id="id" placeholder="ID">
                        </div>
                        <div class="col-lg-2">
                            <input type="text" class="form-control search-text" value="" name="name" id="name" placeholder="ユーザ名">
                        </div>
                        <div class="col-lg-2">
                            <input type="text" class="form-control search-text" value="" name="user_unique_id" id="user_unique_id" placeholder="ユーザー固有ID">
                        </div>
                        <div class="col-lg-2">
                            @include('admin.layouts.components.select_option', [
                                'label'         => 'ステータス',
                                'list'          => $status_list,
                                'name'          => 'status',
                                'selected_id'   => null,
                                'class'         => 'search-select',
                                'blank'         => true
                            ])
                        </div>
                        <div class="col-lg-4">
                            @include('admin.layouts.components.button.search')
                            @include('admin.layouts.components.button.clear')
                        </div>
                    </div>
                    <hr>
                    <table class="table table-striped table-bordered datatable table-sm" id="main_list">
                        <thead>
                            <tr role="row">
                                <th>ID</th>
                                <th>ユーザ名</th>
                                <th>ユーザー固有キー</th>
                                <th>電話登録</th>
                                <th>登録日時</th>
                                <th>最終ログイン日時</th>
                                <th>アカウント状態</th>
                                <th>所有ポイント<br>(有料)</th>
                                <th>所有ポイント<br>(無料)</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- メッセージModal読み込み --}}
    @include('admin.layouts.components.message', ['title' => 'メッセージ'])

    {{-- 詳細Modal読み込み --}}
    @include('admin.user.detail')

@endsection

@section('app_js')
    <script src="{{ asset('js/app/user.js') }}"></script>
@endsection
