@extends('main')
@section('app_contents')

<body>
<div id="page" data-linkscroll='y'>
    <div>
        <div class="pop-header align-c pd-b-0" data-rgen-sm="pd-20">
            <p class="sq90 inline-flex flex-cc fs80 mr-0 txt-color1"><i class="pe-7s-mail"></i></p>
            <h2 class="title mr-0" data-rgen-sm="small">ナカマーカーお問い合わせ</h2>
        </div>
        <div class="pop-body" data-rgen-sm="pd-20">
            <!-- form-block -->
            <div class="form-block">
                <form action="{{ route('contact/send') }}" class="form-widget" method="post">
                    @csrf
                    <div class="field-wrp">
                        <div class="form-group">
                            <input class="form-control" data-label="user_id" required data-msg="ユーザーIDを入力して下さい" type="text" name="user_id" placeholder="ユーザーIDを入力して下さい">
                        </div>
                        <div class="form-group">
                            <input class="form-control" data-label="user_name" required data-msg="ユーザーネームを入力して下さい" type="text" name="user_name" placeholder="ユーザーネームを入力して下さい">
                        </div>

                        <div class="form-group">
                            <input class="form-control" data-label="email" required data-msg="メールアドレスを入力して下さい" type="text" name="email" placeholder="メールアドレスを入力して下さい">
                        </div>

                        <div class="form-group">
                            <textarea class="form-control" data-label="Message" required data-msg="お問い合わせ内容を入力して下さい" name="contact_body" placeholder="お問い合わせ内容を入力して下さい" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn solid btn-default block"><i class="fa fa-envelope-o"></i> 送信する</button>
                </form>
            </div>
        </div>
    </div><!-- /#popup-content -->

    @include('_footer')

</div>
@endsection
