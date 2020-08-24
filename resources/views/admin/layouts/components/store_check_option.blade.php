{{--
"list" => $list                 Checkboxのラベルに表示するリスト
"name" => 'type_id'             id および name名
"checked" => $data->id          選択するデータID
"class_main" => ' check'        その他class適用あれば
"class_input" => ' check'       input内にその他class適用あれば
--}}

@php
    $id_and_name = [];
    if($title == 'divisions') {
        $id_and_name = [
            'label_main'=> '事業部：',
            'label_all' => '全事業部',
            'all'       => 'divisions_all',
            'multiple'  => 'divisions',
            'display'   => ''
        ];
    };
    if($title == 'stores') {
        $id_and_name = [
            'label_main'=> '店舗　：',
            'label_all' => '全店舗',
            'all'       => 'stores_all',
            'multiple'  => 'stores',
            'display'   => ''
        ];
    };
@endphp

<div class="row mb-1 py-2 {{ $class_main }} {{ \Auth::user()->isDivisionOver() ? $id_and_name['display'] : '' }}">
    @if (\Auth::user()->isDivisionOver())
        <label class="col-md-2 col-form-label py-0">
            {{ $id_and_name['label_main'] }}
        </label>
    @endif
    <div class="col-md-10">
        @if (\Auth::user()->isDivisionOver())
            {{-- 全事業部または全店舗のチェックボックスの表示 --}}
            <div class="row custom-control custom-checkbox mb-1">
                <input type="checkbox" class="custom-control-input" id="{{ $id_and_name['all'] }}" name="{{ $id_and_name['all'] }}" value="0">
                <label class="custom-control-label cursor-pointer" for="{{ $id_and_name['all'] }}">
                    {{ $id_and_name['label_all'] }}
                </label>
            </div>

            {{-- 各事業部または各店舗のチェックボックスの表示 --}}
            <div id="{{ $id_and_name['multiple'] }}">
                @foreach($list as $key => $value)
                    <div class="row custom-control custom-checkbox custom-control-inline {{ $id_and_name['display'] }}">
                        <input type="checkbox" class="custom-control-input {{ $class_input }}" id="{{ $name }}_{{ $value['id'] }}" name="{{ $name }}[]" value="{{ $value['id'] }}" {{ $checked_id == $value['id'] ? ' checked' : '' }} data-division_id="{{ ($title == 'divisions') ? $value['id'] : $value['division_id']  }}">
                        <label class="custom-control-label cursor-pointer mr-3" for="{{ $name }}_{{ $value['id'] }}">
                            {{ $value['name'] }}
                        </label>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
