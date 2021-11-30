@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    {{ $register_mode == "create" ? 'お知らせ新規登録' : 'お知らせ編集' }}
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">
        <a href="{{ route('admin/news') }}">お知らせ一覧</a>
    </li>
    <li class="breadcrumb-item">{{ $register_mode == "create" ? 'お知らせ新規登録' : 'お知らせ編集' }}</li>
@endsection

@section('app_content')
    {{-- メイン --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{ $register_mode == "create" ? 'お知らせ新規登録　' : 'お知らせ編集　' }}<span class="text-danger">※は必須入力</span>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('admin/news/save') }}" method="post" id="main_form" enctype='multipart/form-data'>
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="title">タイトル<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'title'])
                                        <input class="form-control required-text" type="text" id="title" name="title" maxlength="50" placeholder="タイトル" value="{{ $data->title ? $data->title : old('title') }}" data-title="タイトル">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="body">内容<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'body'])
                                        <textarea class="form-control" name="body" id="body" maxlength="2000" rows="10" placeholder="内容">{{ old('body') ? old('body') : ($data->body ? $data->body : '') }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">公開ステータス<span class="text-danger">※</span></label>
                                    <div class="col-md-9 form-inline" id="status_checked">
                                        <input type="checkbox" id="open_flg" data-toggle="toggle" data-on="{{ config('const.open_name') }}" data-off="{{ config('const.private_name') }}" {{ $data->status === config('const.open') || old('status') == config('const.open') ? 'checked' : '' }}>
                                        <input type="hidden" id="status" name="status" value="{{ $data->status ? $data->status : 0 }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                {{-- エラーメッセージあれば表示 --}}
                                @include('admin.layouts.components.error_message', ['title' => 'upload_image'])
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="marker_image">イメージ画像</label>
                                    <div class="col-md-9 user-icon-dnd-wrapper">
                                        <div id="drop_area" class="drop_area">
                                            <div class="preview">
                                                @if ($data->image_file && $data->image_file === config('const.out_image'))
                                                    <img id="preview"
                                                        src="{{ session('file_path') ? session('file_path') : asset('images/noImage/out_images.png') }}"
                                                        width="350"
                                                        height="250"
                                                    >
                                                @else
                                                    <img id="preview"
                                                        src="{{ session('file_path') ? session('file_path') : ($data->image_file ? Storage::url("images/".$folder."/".$data->image_file) : asset('images/noImage/no_image.png')) }}"
                                                        width="350"
                                                        height="250"
                                                    >
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <input type="file" id="image" name="upload_image" class="form-control-file" style="display: none">
                                </div>
                                <div class="form-group row">
                                    <div id="image_delete" class="offset-md-3 col-md-9">
                                        <input type="button" id="cancel" class="btn btn-danger" value="画像を消去">
                                        <input type="hidden" id="img_delete" name="img_delete" value=0>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="open_date" id="open_date_label" >公開開始日時{!! ($data->status == 1) ? '<span class="text-danger">※</span>' : ''  !!}</label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'condition_start_time'])
                                        <input class="form-control datetimepicker width-200 " type="text" id="condition_start_time" name="condition_start_time" placeholder="公開開始日時" data-title="公開開始日時"
                                         value="{{ $data->condition_start_time ? $data->condition_start_time : old('condition_start_time') }}" {{ ($data->status && $data->status == 0) ? 'disabled' : '' }}>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="open_date" id="open_date_label" >公開終了日時</label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'condition_end_time'])
                                        <input class="form-control datetimepicker width-200 " type="text" id="condition_end_time" name="condition_end_time" placeholder="公開終了日時" data-title="公開終了日時"
                                         value="{{ $data->condition_end_time ? $data->condition_end_time : old('condition_end_time') }}" {{ ($data->status && $data->status == 0) ? 'disabled' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="id" value="{{ $data->id }}" />
                                <input type="hidden" id="image_file" name="image_file" value="{{ $data->image_file ? $data->image_file : '' }}" />
                                <input type="hidden" id="register_mode" name="register_mode" value="{{ $register_mode }}" />
                                @include('admin.layouts.components.button.register', ['register_mode' => $register_mode])
                                @include('admin.layouts.components.button.detail')
                                @include('admin.layouts.components.button.cancel', ['url' => "admin/news"])
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- 詳細Modal読み込み --}}
    @include('admin.news.detail')
@endsection

@section('app_js')
    {{-- アプリ機能用Js --}}
    <script>
        let register_mode = "{{ $register_mode == "create" ? 'create' : 'edit' }}";
    </script>
    <script src="{{ asset('js/app/news.js') }}"></script>

    {{-- CKエディター & KCファインダー --}}
    <script type="text/javascript" src="/zf/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="/zf/js/zf/ckeditor.toolbar.js"></script>
    <script>
        $(function(){
            jQuery(function() {
                var editor = CKEDITOR.replace(
                    'body',
                    {
                        format_tags: 'p;h2;h3;h4;pre',
                        contentsCss: '/css/news.css',
                        filebrowserUploadUrl:      "/js/kcfinder/upload.php?type=files&mypath=news",
                        filebrowserImageUploadUrl: "/js/kcfinder/upload.php?type=images&mypath=news",
                        filebrowserBrowseUrl:      "/js/kcfinder/browse.php?type=files&mypath=news",
                        filebrowserImageBrowseUrl: "/js/kcfinder/browse.php?type=images&mypath=news",
                        scayt_autoStartup : false,
                        toolbar:standardCKToolbar
                    });
            });
        });
    </script>
@endsection
