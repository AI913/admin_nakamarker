<form action="{{ $url }}" id="remove_form" method="post">
    {{ csrf_field() }}
    <input type="hidden" name="id" id="remove_id" value="">
</form>
