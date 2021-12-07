@extends('main')
@section('app_contents')

<body>
<div id="page" data-linkscroll='y'>
    <div>
        <div class="pop-header align-c pd-b-0" data-rgen-sm="pd-20">
            <p class="sq90 inline-flex flex-cc fs80 mr-0 txt-color1"><i class="pe-7s-mail"></i></p>
            <h2 class="title mr-0" data-rgen-sm="small">お問い合わせ完了</h2>
        </div>
        <div class="pop-body" data-rgen-sm="pd-20">
            <div style="text-align: center; font-size: 1.2em;">
                <p>お問い合わせ承りました</p>
                <p>内容確認の上、ご連絡させていただきます。</p>
            </div>
        </div>
    </div><!-- /#popup-content -->

    @include('_footer')

</div>
@endsection
