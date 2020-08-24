{{--
"list" => $list                 Checkboxのラベルに表示するリスト
"name" => 'type_id'             id および name名
"checked" => $data->id          選択するデータID
--}}


@if (\Auth::user()->isDivisionOver())
    <div class="store_checkbox">
    <div class="custom-control custom-checkbox mb-1">
        <input type="checkbox" class="custom-control-input" id="check_all">
        <label class="custom-control-label cursor-pointer" for="check_all">
            全選択
        </label>
    </div>
    {{-- 事業部長以上の権限時 --}}
    @foreach($list as $key => $value)
        <div class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input target-check-all" id="{{ $name }}_{{ $value['id'] }}" name="{{ $name }}[]" value="{{ $value['id'] }}" {{ isset($selected[$value['id']]) ? ' checked' : '' }}>
            <label class="custom-control-label cursor-pointer mr-3" for="{{ $name }}_{{ $value['id'] }}">
                {{ $value['name'] }}
            </label>
        </div>
    @endforeach
    </div>
@else
    {{-- 店舗責任者以下はチェックボックスの表示がないため、表示データの指定をする --}}
    <label>{{ \Auth::user()->store->name }}</label>
    <input type="hidden" name="{{ $name }}[]" value="{{ \Auth::user()->store_id }}">
@endif
