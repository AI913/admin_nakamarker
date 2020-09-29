@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    {{ $register_mode == "create" ? 'コミュニティ新規登録' : 'コミュニティ編集' }}
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">
        <a href="{{ route('admin/community') }}">コミュニティ一覧</a>
    </li>
    <li class="breadcrumb-item">{{ $register_mode == "create" ? 'コミュニティ新規登録' : 'コミュニティ編集' }}</li>
@endsection

@section('app_content')
    {{-- メイン --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{ $register_mode == "create" ? 'コミュニティ新規登録　' : 'コミュニティ編集　' }}<span class="text-danger">※は必須入力</span>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('admin/community/save') }}" method="post" id="main_form" enctype='multipart/form-data'>
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">コミュニティタイプ<span class="text-danger">※</span></label>
                                    <div class="col-md-9 form-inline" id="type_checked">
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                            <input type="radio" class="custom-control-input" id="type1" name="type" value="{{ config('const.community_official') }}" data-type="{{ config('const.community_official_name') }}" 
                                            {{ !$data->type || $data->type == config('const.community_official') || old('type') == config('const.community_official') ? 'checked' : '' }}>

                                            <label class="custom-control-label cursor-pointer" for="type1">{{ config('const.community_official_name') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                            <input type="radio" class="custom-control-input" id="type2" name="type" value="{{ config('const.community_official_free') }}" data-type="{{ config('const.community_official_free_name') }}" 
                                            {{ $data->type == config('const.community_official_free') || old('type') == config('const.community_official_free') ? 'checked' : '' }}>

                                            <label class="custom-control-label cursor-pointer" for="type2">{{ config('const.community_official_free_name') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                            <input type="radio" class="custom-control-input" id="type3" name="type" value="{{ config('const.community_personal') }}" data-type="{{ config('const.community_personal_name') }}" 
                                            {{ $data->type == config('const.community_personal') || old('type') == config('const.community_personal') ? 'checked' : '' }}>

                                            <label class="custom-control-label cursor-pointer" for="type3">{{ config('const.community_personal_name') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                            <input type="radio" class="custom-control-input" id="type4" name="type" value="{{ config('const.community_personal_open') }}" data-type="{{ config('const.community_personal_open_name') }}" 
                                            {{ $data->type == config('const.community_personal_open') || old('type') == config('const.community_personal_open') ? 'checked' : '' }}>

                                            <label class="custom-control-label cursor-pointer" for="type4">{{ config('const.community_personal_open_name') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="name">コミュニティ名<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'name'])
                                        <input class="form-control required-text" type="text" id="name" name="name" maxlength="50" placeholder="名前" value="{{ $data->name ? $data->name : old('name') }}" data-title="名前">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="description">コミュニティ概要</label>
                                    <div class="col-md-9">
                                        <textarea class="form-control" name="description" id="description" maxlength="500" rows="10" placeholder="備考">{{ $data->description ? $data->description : old('description') }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">公開ステータス<span class="text-danger">※</span></label>
                                    <div class="col-md-9 form-inline" id="status_checked">
                                        <input type="checkbox" id="open_flg" data-toggle="toggle" data-on="{{ config('const.open_name') }}" data-off="{{ config('const.private_name') }}" {{ $data->status === config('const.open') || old('status') == config('const.open') ? 'checked' : '' }}>
                                        <input type="hidden" id="status" name="status" value="{{ $data->status ? $data->status : (old('status') ? old('status') : 0) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                {{-- エラーメッセージあれば表示 --}}
                                @include('admin.layouts.components.error_message', ['title' => 'upload_image'])
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="community_image">イメージ画像</label>
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
                                                        {{-- src="{{ $data->image_file ? Storage::url("images/".$folder."/".$data->image_file) : (session('file_path') ? session('file_path') : asset('images/noImage/no_image.png')) }}" --}}
                                                        src="{{ session('file_path') ? session('file_path') : ($data->image_file ? Storage::url("images/".$folder."/".$data->image_file) : asset('images/noImage/no_image.png')) }}"
                                                        width="350" 
                                                        height="250"
                                                    >
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">強制BANフラグ</label>
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
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="id" value="{{ $data->id }}" />
                                <input type="hidden" id="register_mode" name="register_mode" value="{{ $register_mode }}" />
                                <input type="hidden" id="image_file" name="image_file" value="{{ $data->image_file ? $data->image_file : '' }}" />
                                @include('admin.layouts.components.button.register', ['register_mode' => $register_mode])
                                @include('admin.layouts.components.button.cancel', ['url' => "/community"])
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
    <script src="{{ asset('js/app/community.js') }}"></script>
@endsection
