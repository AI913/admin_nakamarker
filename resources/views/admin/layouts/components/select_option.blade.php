{{--
"list" => $list                 Selectに表示するリスト
"name" => 'type_id'             id および name名
"selected" => $data->id         選択するデータID
"class" => ' check'             その他class適用あれば
"blank" => true                 空白あり、なし
--}}
<select class="form-control select-placeholder {{ $class }}" id="{{ $name }}" name="{{ $name }}" {{ isset($other) ? $other : ''}}>
    @if (isset($blank) && $blank)
        @if (isset($label))
            <option value="" disabled selected>{{ $label }}</option>
        @else
            <option value="" disabled selected>選択してください</option>
        @endif
    @endif
    @foreach($list as $key => $value)
        <option value="{{ $value['id'] }}" {{ $selected_id == $value['id'] ? ' selected' : '' }}>{{ $value['name'] }}</option>
    @endforeach
</select>
