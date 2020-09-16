@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    {{ $register_mode == "create" ? 'マーカー新規登録' : 'マーカー編集' }}
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">
        <a href="{{ route('admin/marker') }}">マーカー一覧</a>
    </li>
    <li class="breadcrumb-item">{{ $register_mode == "create" ? 'マーカー新規登録' : 'マーカー編集' }}</li>
@endsection

@section('app_content')
    {{-- メイン --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{ $register_mode == "create" ? 'マーカー新規登録　' : 'マーカー編集　' }}<span class="text-danger">※は必須入力</span>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('admin/marker/save') }}" method="post" id="main_form" enctype='multipart/form-data'>
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">マーカータイプ<span class="text-danger">※</span></label>
                                    <div class="col-md-9 form-inline" id="type_checked">
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                            <input type="radio" class="custom-control-input" id="type1" name="type" value="{{ config('const.marker_type_register') }}" data-type="{{ config('const.marker_type_register_name') }}" 
                                            {{ !$data->type || $data->type == config('const.marker_type_register') || old('type') == config('const.marker_type_register') ? 'checked' : '' }}>

                                            <label class="custom-control-label cursor-pointer" for="type1">{{ config('const.marker_type_register_name') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                            <input type="radio" class="custom-control-input" id="type2" name="type" value="{{ config('const.marker_type_function') }}" data-type="{{ config('const.marker_type_function_name') }}" 
                                            {{ $data->type == config('const.marker_type_function') || old('type') == config('const.marker_type_function') ? 'checked' : '' }}>

                                            <label class="custom-control-label cursor-pointer" for="type2">{{ config('const.marker_type_function_name') }}</label>
                                        </div>
                                        <div class="custom-control custom-radio cursor-pointer mr-3">
                                            <input type="radio" class="custom-control-input" id="type3" name="type" value="{{ config('const.marker_type_search') }}" data-type="{{ config('const.marker_type_search_name') }}" 
                                            {{ $data->type == config('const.marker_type_search') || old('type') == config('const.marker_type_search') ? 'checked' : '' }}>

                                            <label class="custom-control-label cursor-pointer" for="type3">{{ config('const.marker_type_search_name') }}</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="name">マーカー名<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        <input class="form-control required-text" type="text" id="name" name="name" maxlength="50" placeholder="名前" value="{{ $data->name ? $data->name : old('name') }}" data-title="名前">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="description">マーカー概要</label>
                                    <div class="col-md-9">
                                        <textarea class="form-control" name="description" id="description" maxlength="500" rows="10" placeholder="備考">{{ $data->memo ? $data->memo : old('memo') }}</textarea>
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
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="marker_image">イメージ画像</label>
                                    <span class="col-md-6 col-form-label" style="color: red">※画像の設定は必須です</span>   
                                    <div class="col-md-9 offset-md-3 user-icon-dnd-wrapper">
                                        <div id="drop_area" class="drop_area">
                                            <div class="preview">
                                                <img id="preview" 
                                                     src="{{ $data->image_file ? Storage::url("images/".$data->image_file) : (old('upload_image') ? old('upload_image') : asset('images/noImage/no_image.png')) }}" 
                                                     width="350" 
                                                     height="250"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <input type="file" id="image" name="upload_image" class="form-control-file" style="display: none">
                                </div>
                                <div class="form-group row">
                                    <div id="image_delete" class="offset-md-3 col-md-9">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'upload_image'])
                                        <input type="button" id="cancel" class="btn btn-danger" value="画像を消去">
                                        <input type="hidden" id="img_delete" name="img_delete" value=0>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">ポイントフラグ</label>
                                    <span class="col-md-9 col-form-label" style="color: red">※無償で提供する場合は"デフォルト"に✓を入れてください</span>
                                    <div class="offset-md-3 col-md-9 form-inline">
                                        <input type="checkbox" id="charge_flg" data-toggle="toggle" data-on="{{ __('有料ポイント') }}" data-off="{{ __('無料ポイント') }}" data-onstyle="danger" data-offstyle="primary"
                                        {{ $data->charge_flg === config('const.charge_flg_on') || old('charge_flg') == config('const.charge_flg_on') ? 'checked' : '' }}>
                                        
                                        <input type="checkbox" id="charge_flg_default" name="charge_flg_default" {{ $data->charge_flg === config('const.charge_flg_default') || old('charge_flg_default') == 'on' ? 'checked' : '' }}>デフォルト
                                        <input type="hidden" name="charge_flg" value="{{ $data->charge_flg ? $data->charge_flg : (old('charge_flg') ? old('charge_flg') : 1) }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="name">価格(ポイント)<span class="text-danger">※</span></label>
                                    <div class="col-md-4">
                                        {{-- エラーメッセージあれば表示 --}}
                                        @include('admin.layouts.components.error_message', ['title' => 'price'])
                                        <input class="form-control required-text" type="text" id="price" name="price" maxlength="5" placeholder="価格" value="{{ $data->price ? $data->price : old('price') }}" data-title="価格">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="id" value="{{ $data->id }}" />
                                <input type="hidden" id="register_mode" name="register_mode" value="{{ $register_mode }}" />
                                @include('admin.layouts.components.button.register', ['register_mode' => $register_mode])
                                @include('admin.layouts.components.button.cancel', ['url' => "/marker"])
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
    <script src="{{ asset('js/app/marker.js') }}"></script>
@endsection
