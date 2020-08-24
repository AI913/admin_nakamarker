@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    コミュニティロケーション一覧
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">コミュニティロケーション一覧</li>
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
                            <input type="text" class="form-control search-text" value="" name="name" id="name" placeholder="名前">
                        </div>
                        <div class="col-lg-2">
                            <input type="text" class="form-control search-text" value="" name="community_id" id="community_id" placeholder="保有コミュニティ">
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
                                <th>ロケーションイメージ</th>
                                <th>ロケーション名</th>
                                <th>緯度</th>
                                <th>経度</th>
                                <th>保有コミュニティ</th>
                                <th>登録ユーザ</th>
                                <th>マーカー</th>
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
    {{-- @include('admin.layouts.components.confirm', [
        'id' => 'confirm_modal', 'title' => '削除確認', 'message' => '対象データを削除します。よろしいですか？', 'btn_id' => 'btn_remove'
    ]) --}}

    {{-- 削除Form読み込み --}}
    {{-- @include('admin.layouts.components.remove_form', ['url' => url('admin/user/remove')]) --}}

    {{-- 詳細Modal読み込み --}}
    {{-- @include('admin.user.detail') --}}

@endsection

@section('app_js')
    <script src="{{ asset('js/app/community_location.js') }}?v={{ config('const.app_version') }}"></script>
@endsection
