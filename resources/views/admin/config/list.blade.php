@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    共通設定管理一覧
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">共通設定管理一覧</li>
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
                            <input type="text" class="form-control search-text" value="" name="key" id="key" placeholder="キー">
                        </div>
                        <div class="col-lg-2">
                            <input type="text" class="form-control search-text" value="" name="key" id="update_user_name" placeholder="最終更新者">
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
                                <th>キー</th>
                                <th>値</th>
                                <th>備考</th>
                                <th>最終更新者</th>
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

    {{-- 削除確認Modal読み込み --}}
    @include('admin.layouts.components.confirm', [
        'id' => 'confirm_modal', 'title' => '削除確認', 'message' => '対象データを削除します。よろしいですか？', 'btn_id' => 'btn_remove'
    ])

    {{-- 削除Form読み込み --}}
    @include('admin.layouts.components.remove_form', ['url' => url('config/remove')])

@endsection

@section('app_js')
    <script src="{{ asset('js/app/config.js') }}"></script>
@endsection