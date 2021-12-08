@extends('main')
@section('app_contents')

<div id="page" data-linkscroll='y'>
    <div>
        <div class="pop-header align-c pd-b-0" data-rgen-sm="pd-20">
            <p class="sq90 inline-flex flex-cc fs80 mr-0 txt-color1"><i class="pe-7s-mail"></i></p>
            <h2 class="title mr-0" data-rgen-sm="small">ナカマーカーお問い合わせ</h2>
        </div>
        <div class="pop-body" data-rgen-sm="pd-20">
            <!-- form-block -->
            <div class="form-block">
                <form class="form-widget" method="post" id="form_contact">
                    @csrf
                    <div class="field-wrp">
                        <div class="form-group">
                            <input class="form-control text-required" data-msg="ユーザーIDを入力して下さい" type="text" name="user_id" id="user_id" placeholder="ユーザーIDを入力して下さい">
                        </div>
                        <div class="form-group">
                            <input class="form-control text-required" data-msg="ユーザーネームを入力して下さい" type="text" name="user_name" id="user_name" placeholder="ユーザーネームを入力して下さい">
                        </div>

                        <div class="form-group">
                            <input class="form-control text-required" data-msg="メールアドレスを入力して下さい" type="text" name="email" id="email" placeholder="メールアドレスを入力して下さい">
                        </div>

                        <div class="form-group">
                            <textarea class="form-control text-required" data-msg="お問い合わせ内容を入力して下さい" name="contact_body" id="contact_body" placeholder="お問い合わせ内容を入力して下さい" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <a href="#" class="btn solid btn-default block" id="send"><i class="fa fa-envelope-o"></i> 送信する</a>
                </form>
            </div>
        </div>
    </div><!-- /#popup-content -->

    @include('_footer')

</div>
@endsection
@section('app_js')
<script>
    $('#send').on('click', function(){
        let check = true;
        $('.text-required').each(function(idx, elm){
            if (!isInputValue(elm)) {
                $(elm).focus();
                $(elm).after("<p class='error'>"+$(elm).attr("data-msg")+"</p>");
                check = false;
            }
        });
        if (check == false) {
            return false;
        }

        $('#send').prop("disabled", true);

        $.ajax({
            type: "POST",
            url: "contact/send",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                "user_name" : $('#user_name').val() ,
                "user_id" : $('#user_id').val() ,
                "email" : $('#email').val() ,
                "contact_body" : $('#contact_body').val() ,
            },
            dataType : "json"
        }).done(function(data) {
            location.href = "/contact/result";
        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
            $('#send').prop("disabled", false);
        });
        return false;
    });
    /**
     * 指定Textが入力されているかどうか
     * @param elm
     * @returns {boolean}
     */
    function isInputValue(elm) {
        if ($(elm).val() == "" || $(elm).val() == null || $(elm).val() == undefined) {
            return false;
        }
        return true;
    }
</script>

@endsection
