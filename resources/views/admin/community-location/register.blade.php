@extends('admin.layouts.app')

@section('app_title')
    {{-- タイトル --}}
    {{ $register_mode == "create" ? 'ロケーション新規登録' : 'ロケーション編集' }}
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">
        <a href="{{ route('admin/community-location') }}">(コミュニティ)ロケーション一覧</a>
    </li>
    <li class="breadcrumb-item">{{ $register_mode == "create" ? 'ロケーション新規登録' : 'ロケーション編集' }}</li>
@endsection

@section('app_content')
    {{-- メイン --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    {{ $register_mode == "create" ? 'ロケーション新規登録　' : 'ロケーション編集　' }}<span class="text-danger">※は必須入力</span>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('admin/community-location/save') }}" method="post" id="main_form" enctype='multipart/form-data'>
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="name">ロケーション名<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        <input class="form-control required-text" type="text" id="name" name="name" maxlength="50" placeholder="ロケーション名" value="{{ $data->name }}" data-title="名前">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="name">緯度/経度<span class="text-danger">※</span></label>
                                    <div class="col-md-4">
                                        <input class="form-control required-text" type="text" id="latitude" name="latitude" maxlength="50" placeholder="緯度" value="{{ $data->latitude }}" data-title="緯度">
                                    </div>
                                    <div class="col-md-4">
                                        <input class="form-control required-text" type="text" id="longitude" name="longitude" maxlength="50" placeholder="経度" value="{{ $data->longitude }}" data-title="経度">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="description">ロケーション概要</label>
                                    <div class="col-md-9">
                                        <textarea class="form-control" name="description" id="description" maxlength="500" rows="10" placeholder="備考">{{ $data->memo }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="name">マーカー<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        <input class="form-control required-text" list="marker" id="marker_name" name="marker_name" type="text" maxlength="50" placeholder="マーカーを選択" value="{{ $data->marker_name }}" data-title="マーカー" autocomplete="off">
                                        <datalist id="marker">
                                            @foreach ($marker_list as $value)
                                                <option value="{{ $value->name }}"></option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label" for="name">コミュニティ<span class="text-danger">※</span></label>
                                    <div class="col-md-9">
                                        <input class="form-control required-text" list="community" id="community_name" name="community_name" type="text" maxlength="50" placeholder="コミュニティを選択" value="{{ $data->community_name }}" data-title="コミュニティ" autocomplete="off">
                                        <datalist id="community">
                                            @foreach ($community_list as $value)
                                                <option value="{{ $value->name }}"></option>
                                            @endforeach
                                        </datalist>
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
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" name="id" id="id" value="{{ $data->id }}" />
                                <input type="hidden" id="user_id" name="user_id" value="{{ $data->user_id ? $data->user_id : \Auth::user()->id }}" />
                                <input type="hidden" id="register_mode" name="register_mode" value="{{ $register_mode }}" />
                                @include('admin.layouts.components.button.register', ['register_mode' => $register_mode])
                                @include('admin.layouts.components.button.detail')
                                @include('admin.layouts.components.button.cancel', ['url' => "/community-location"])
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
    <script src="{{ asset('js/app/community_location.js') }}?v={{ config('const.app_version') }}"></script>
@endsection
