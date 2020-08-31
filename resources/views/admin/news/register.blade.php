@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    {{ $register_mode == "create" ? 'ニュースの新規登録' : 'ニュースの編集' }}
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">
        <a href="{{ route('admin/news') }}">マーカー一覧</a>
    </li>
    <li class="breadcrumb-item">{{ $register_mode == "create" ? 'ニュースの新規登録' : 'ニュースの編集' }}</li>
@endsection

@section('app_content')
    {{-- メイン --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{ $register_mode == "create" ? 'ニュースの新規登録　' : 'ニュースの編集　' }}<span class="text-danger">※は必須入力</span>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('admin/news/save') }}" method="post" id="main_form" enctype='multipart/form-data'>
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">情報の種別<span class="text-danger">※</span></label>
                                    <div class="col-md-9 form-inline" id="type_checked">
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                            <input type="radio" class="custom-control-input" id="type1" name="type" value="{{ config('const.official_type') }}" data-type="{{ config('const.official_type_name') }}" {{ !$data->type || $data->type == config('const.official_type') ? 'checked' : '' }}>
                                            <label class="custom-control-label cursor-pointer" for="type1">{{ config('const.official_type_name') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                            <input type="radio" class="custom-control-input" id="type2" name="type" value="{{ config('const.community_type') }}" data-type="{{ config('const.community_type_name') }}" {{ $data->type == config('const.community_type') ? 'checked' : '' }}>
                                            <label class="custom-control-label cursor-pointer" for="type2">{{ config('const.community_type_name') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="title">タイトル<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        <input class="form-control required-text" type="text" id="title" name="title" maxlength="50" placeholder="タイトル" value="{{ $data->title }}" data-title="名前">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="body">内容<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        <textarea class="form-control" name="body" id="body" maxlength="2000" rows="10" placeholder="内容">{{ $data->body }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">公開ステータス<span class="text-danger">※</span></label>
                                    <div class="col-md-9 form-inline" id="status_checked">
                                        <input type="checkbox" id="open_flg" data-toggle="toggle" data-on="{{ config('const.open_name') }}" data-off="{{ config('const.private_name') }}" {{ $data->status ? 'checked' : '' }}>
                                        <input type="hidden" id="status" name="status" value="{{ $data->status ? $data->status : 0}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="marker_image">イメージ画像</label>
                                    <div class="col-md-9 user-icon-dnd-wrapper">
                                        <div id="drop_area" class="drop_area">
                                            <div class="preview">
                                                <img id="preview" 
                                                     src="{{ $data->image_file ? Storage::url("images/".$data->image_file) : asset('images/noImage/no_image.png') }}" 
                                                     width="350" 
                                                     height="250"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">強制削除フラグ</label>
                                    <div class="col-md-9 form-inline">
                                        <input type="file" id="image" name="upload_image" class="form-control-file" style="display: none">
                                        <input type="checkbox" id="delete_flg" data-toggle="toggle" data-on="{{ __('ON') }}" data-off="{{ __('OFF') }}" data-onstyle="danger" {{ $data->image_file === config('const.out_image') ? 'checked' : '' }}>
                                        <input type="hidden" id="delete_flg_on" name="delete_flg_on">
                                        <input type="hidden" id="image_flg" name="image_flg" value="{{ $data->image_file ? $data->image_file : '' }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div id="image_delete" class="offset-md-3 col-md-9">
                                        <input type="button" id="cancel" class="btn btn-danger" value="画像を消去">
                                        <input type="hidden" id="img_delete" name="img_delete" value=0>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="open_date" id="open_date_label" >公開日時{!! ($data->status == 1) ? '<span class="text-danger">※</span>' : ''  !!}</label>
                                    <div class="col-md-9">
                                        <input class="form-control datetimepicker width-200 {{ ($data->status == 1) ? 'required-text' : '' }}" type="text" id="condition_start_time" name="condition_start_time" placeholder="公開日時" data-title="公開日時" value="{{ $data->condition_start_time }}" {{ ($data->status && $data->status == 0 ) ? 'disabled' : '' }}>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="open_date" id="open_date_label" >公開終了日時</label>
                                    <div class="col-md-9">
                                        <input class="form-control datetimepicker width-200 {{ ($data->status == 1) ? 'required-text' : '' }}" type="text" id="condition_end_time" name="condition_end_time" placeholder="公開終了日時" data-title="公開終了日時" value="{{ $data->condition_end_time }}" {{ ($data->status && $data->status == 0 ) ? 'disabled' : '' }}>
                                    </div>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="id" value="{{ $data->id }}" />
                                <input type="hidden" id="register_mode" name="register_mode" value="{{ $register_mode }}" />
                                @include('admin.layouts.components.button.register', ['register_mode' => $register_mode])
                                @include('admin.layouts.components.button.cancel', ['url' => "/news"])
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

@endsection

@section('app_js')
    {{-- アプリ機能用Js --}}
    <script>
        let register_mode = "{{ $register_mode == "create" ? 'create' : 'edit' }}";
    </script>
    <script src="{{ asset('js/app/news.js') }}"></script>
@endsection
