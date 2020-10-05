@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    コミュニティロケーション一覧
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">
        <a href="{{ route('admin/community') }}">コミュニティ一覧</a>
    </li>
    <li class="breadcrumb-item">{{ $community_name }}</li>
    <li class="breadcrumb-item">コミュニティロケーション一覧</li>
@endsection

@section('app_content')
    {{-- メイン --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="community_location_communityName">
                        コミュニティ名：<span style="font-weight: bold">{{ $community_name }}</span>
                    </div>
                    <div class="row mb-3 d-flex">
                        <div class="col-lg-2">
                            <input type="text" class="form-control search-text" value="" name="id" id="id" placeholder="ID">
                        </div>
                        <div class="col-lg-2">
                            <input type="text" class="form-control search-text" value="" name="marker_name" id="marker_name" placeholder="マーカー名">
                        </div>
                        <div class="col-lg-2">
                            <input type="text" class="form-control search-text" value="" name="name" id="name" placeholder="場所の名前">
                        </div>
                        <div class="col-lg-4">
                            @include('admin.layouts.components.button.search')
                            @include('admin.layouts.components.button.clear')
                        </div>
                    </div>
                    <hr>
                    <button class="btn btn-primary btn-location_create_link" width="100">新規登録</button>
                    <table class="table table-striped table-bordered datatable table-sm" id="main_list">
                        <thead>
                            <tr role="row">
                                <th>ID</th>
                                <th>マーカー名</th>
                                <th>場所の名前</th>
                                <th>ロケーション画像</th>
                                <th>ユーザ名</th>
                                <th>登録日時</th>
                                <th>位置情報</th>
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
    @include('admin.layouts.components.remove_form', ['url' => null])

    {{-- 詳細Modal読み込み --}}
    @include('admin.community.community-location.detail')

@endsection

@section('app_js')
    <script src="{{ asset('js/app/community_location.js') }}"></script>
@endsection
