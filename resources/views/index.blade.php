@extends('main')
@section('app_contents')

    <!--
        ************************************************************
        * Navigation
        ************************************************************ -->
    <!-- <nav class="nav-wrp nav-1 light" data-glass="y" data-sticky="y">
        <div class="container pd-0 bdr-b min-px-h100 flex-cc" data-rgen-sm="pd-lr-20 h-reset">
            <div class="flex-row gt0 middle-md">


                <div class="flex-col-md-3">
                    <div class="nav-header">
                        <a class="navbar-brand" href="#"> -->
    <!-- <img src="images/logo1.png" alt="Brand logo"> -->
    <!-- <h3 style="color: #ffffff;">ナカマーカー</h3>

</a>
<a class="nav-handle" data-nav=".nav" data-navopen="pe-7s-more" data-navclose="pe-7s-close"><i class="pe-7s-more"></i></a>
</div>
</div>

<div class="flex-col-md-9 align-r">
<div class="nav">

<ul class="nav-links sf-menu">
    <li><a href="#features">メニュー1</a></li>
    <li><a href="#gallery">メニュー2</a></li>
    <li><a href="#testimonials">メニュー3</a></li>
</ul>

<div class="nav-other">
    <a href="#popup-content" class="btn btn-default light mini set-popup"><i class="fa fa-envelope-o mr-r-5"></i> お問い合わせ</a>
</div>
</div>
</div>
</div>
</div>
</nav> -->
    <!-- ************** END : Navigation **************  -->


    <!--
    ************************************************************
    * Intro section
    ************************************************************ -->
    <section class="pd-0 pos-rel bdr-b bdr-op-1 bg-color1" data-rgen-sm="h-reset">

        <!--=================================
        = Top content
        ==================================-->
        <div class="pos-rel bg-dark1 -mr-b-400 z2 pd-tb-large overflow-hidden" data-rgen-sm="mr-0 pd-tb-small" data-rgen-md="mr-b-0">
            <div class="container pos-rel z2">
                <div class="w75 mr-auto align-c mr-b-200 typo-light" data-rgen-sm="mr-b-0" data-rgen-md="mr-b-0 mr-t-50">
                    <h1 style="font-family: 'Kosugi Maru', sans-serif;" class="title large" data-rgen-sm="medium" data-rgen-md="large">かんたん！置くだけ地図アプリ<br>ナカマーカー</h1>
                    <p class="title-sub small" data-rgen-sm="small">ちずにマーカーを置くだけで、場所の登録や周辺地域の情報もスグさまゲット！友達に教えたい場所もコミュニティ機能でカンタンシェア！</p>

                    <a href="#" class="btn btn-white small bdr-2 bold-n mr-6 round" data-rgen-sm="medium">
                        <i class="fa fa-apple fs26 btn-icon"></i> <span class="btn-txt txt-upper">只今、配信準備中です..</span>
                    </a>

                    <!-- <a href="#" class="btn btn-white small bdr-2 bold-n mr-6 round" data-rgen-sm="medium">
                        <i class="fa fa-android fs26 btn-icon"></i> <span class="btn-txt txt-upper">Google Play</span>
                    </a> -->


                </div>
            </div>
            <b class="full-wh bg-dark1 op-06 z1"></b>
            <b class="full-vh bg-cc bg-cover" data-stellar="y" data-stellar-ratio="0.8" data-bg="/web/images/intro-bg07.jpg"></b>
        </div>

        <!--=================================
        = Screen shots and features
        ==================================-->
        <div class="pos-rel z2 pd-tb-small" data-rgen-sm="pd-tb-small">
            <div class="container pos-rel z2">

                <div class="flex-row gt60 mb20">
                    <div class="flex-col-md-4">
                        <div class="mr-auto mr-b-10"><img src="/web/images/app-img-10.jpg" class="shadow-5 rd-5" alt="app-image"></div>

                        <div class="w80 bdr-l-2 bdr-b-2 bdr-r-2 bdr-op-4 light min-px-h20 mr-auto"></div>
                        <div class="w0 bdr-l-2 bdr-op-4 light min-px-h40 mr-auto mr-b-20"></div>

                        <div class="info-obj img-t small g20 align-c">
                            <div class="img"><span class="iconwrp rd bg-dark1 txt-white"><i class="pe-7s-map"></i></span></div>
                            <div class="info typo-light">
                                <h3 class="title mini bold-n">使いやすいマップ</h3>
                                <p>タイルで区割りされた地図はタイルをボタンの様にタップする事ができ、そこから様々な操作を行う事が出来ます！</p>
                            </div>
                        </div><!-- info box -->
                    </div><!-- // END : column //  -->

                    <div class="flex-col-md-4">
                        <div class="mr-auto mr-b-10"><img src="/web/images/app-img-11.jpg" class="shadow-5 rd-5" alt="app-image"></div>

                        <div class="w80 bdr-l-2 bdr-b-2 bdr-r-2 bdr-op-4 light min-px-h20 mr-auto"></div>
                        <div class="w0 bdr-l-2 bdr-op-4 light min-px-h40 mr-auto mr-b-20"></div>

                        <div class="info-obj img-t small g20 align-c">
                            <div class="img"><span class="iconwrp rd bg-dark1 txt-white"><i class="pe-7s-search"></i></span></div>
                            <div class="info typo-light">
                                <h3 class="title mini bold-n">かんたん検索</h3>
                                <p>「駅」や「カフェ」などカテゴリ別に分かれている検索マーカーを地図上にドラッグ・アンド・ドロップするだけで周辺施設の情報を手軽にゲット！</p>
                            </div>
                        </div><!-- info box -->
                    </div><!-- // END : column //  -->

                    <div class="flex-col-md-4">
                        <div class="mr-auto mr-b-10"><img src="/web/images/app-img-13.jpg" class="shadow-5 rd-5" alt="app-image"></div>

                        <div class="w80 bdr-l-2 bdr-b-2 bdr-r-2 bdr-op-4 light min-px-h20 mr-auto"></div>
                        <div class="w0 bdr-l-2 bdr-op-4 light min-px-h40 mr-auto mr-b-20"></div>

                        <div class="info-obj img-t small g20 align-c">
                            <div class="img"><span class="iconwrp rd bg-dark1 txt-white"><i class="pe-7s-share"></i></span></div>
                            <div class="info typo-light">
                                <h3 class="title mini bold-n">わかりやすいARナビゲーション</h3>
                                <p>周りにかざすと方向がすぐに分かるARナビゲーションを搭載。地図をみるのが苦手な人でもこれなら進行方向を間違えない！</p>
                            </div>
                        </div><!-- info box -->
                    </div><!-- // END : column //  -->
                </div><!-- // END : row //  -->
            </div>
        </div><!-- // END : Screen shots and features //  -->




    </section>
    <!-- ************** END : Intro section **************  -->



    <!--
    ************************************************************
    * Features section
    ************************************************************ -->
    <section class="pos-rel pd-tb-medium" data-rgen-sm="pd-tb-small">
        <div class="container pos-rel z1">
            <div class="w75 mr-auto mr-b-100 align-c" data-rgen-sm="mr-b-20">
                <h2 style="color: #364e96;/*文字色*/
				padding: 0.5em 0;/*上下の余白*/
				border-top: solid 3px #364e96;/*上線*/
				border-bottom: solid 3px #364e96;/*下線*/" class="title" data-rgen-sm="medium">今までに無い地図アプリを</h2><br>
                <p class="title-sub" data-rgen-sm="small">FRIENDLY＆SIMPLEをコンセプトに、地図上にマーカーを置くだけで場所の登録・周辺施設の検索・ナビゲーションと様々な機能が使える操作のシンプルさやSNSでフォローする感覚で楽しく位置情報の共有が出来るコミュニティ機能を実装し、使いやすさと親しみやすさを追求しました。</p>
            </div>


        </div><!-- // END : container //  -->
    </section>
    <!-- ************** END : Features section **************  -->


    <!--
    ************************************************************
    * About us section
    ************************************************************ -->
    <section class="pos-rel pd-tb-large pos-rel" data-rgen-sm="pd-tb-small">
        <div class="container pos-rel z2 align-c">
            <div class="w75 mr-auto typo-light">
                <p class="title-sub fs30" data-rgen-sm="fs20">ナカマーカーではBtoB向けの情報発信のご相談も承っております。イベントマップやロケーションを紹介しているメディアとの連動マップ等、O2Oを実現する為のお手伝いをさせていただきます。</p>
                <a href="http://www.frontarc.co.jp/contact.html" class="btn btn-white bold-n" target="_blank">お問い合わせはこちら</a>
            </div>

            <!-- <hr class="light mr-tb-80" data-rgen-sm="mr-tb-20"> -->

            <!--=================================
            = Logo list
            ==================================-->
            <!-- <ul class="logo-list gt-medium size-medium reset op-05">
                <li><img src="images/brand-logo1_light.png" alt="brand"></li>
                <li><img src="images/brand-logo2_light.png" alt="brand"></li>
                <li><img src="images/brand-logo3_light.png" alt="brand"></li>
                <li><img src="images/brand-logo4_light.png" alt="brand"></li>
                <li><img src="images/brand-logo5_light.png" alt="brand"></li>
            </ul>	 -->
        </div>

        <b class="full-wh bg-dark1 op-06 z1"></b>
        <b class="full-vh bg-cc bg-cover" data-rgen-sm="h100" data-stellar="y" data-stellar-ratio="0.8" data-bg="/web/images/bg2.jpg"></b>
    </section>
    <!-- ************** END : Features section **************  -->

    @include('_footer')



    <!--
    ************************************************************
    * Popup form block
    ************************************************************ -->

    <!-- form : "mfp-hide" Add this class before using -->
    <div id="popup-content" class="white-popup-block popup-content mfp-hide">
        <div class="pop-header align-c pd-b-0" data-rgen-sm="pd-20">
            <p class="sq90 inline-flex flex-cc fs80 mr-0 txt-color1"><i class="pe-7s-mail"></i></p>
            <h2 class="title mr-0" data-rgen-sm="small">お問い合わせフォーム</h2>
        </div>
        <div class="pop-body" data-rgen-sm="pd-20">
            <!-- form-block -->
            <div class="form-block">
                <form action="form-data/formdata.php" class="form-widget">
                    <div class="field-wrp">
                        <input type="hidden" name="to" value="r.genesis.art@gmail.com">
                        <div class="form-group">
                            <input class="form-control" data-label="user_id" required data-msg="ユーザーIDを入力して下さい" type="text" name="user_id" placeholder="ユーザーIDを入力して下さい">
                        </div>
                        <div class="form-group">
                            <input class="form-control" data-label="user_name" required data-msg="ユーザーネームを入力して下さい" type="text" name="subject" placeholder="ユーザーネームを入力して下さい">
                        </div>

                        <div class="form-group">
                            <input class="form-control" data-label="email" required data-msg="メールアドレスを入力して下さい" type="text" name="email" placeholder="メールアドレスを入力して下さい">
                        </div>

                        <div class="form-group">
                            <textarea class="form-control" data-label="Message" required data-msg="お問い合わせ内容を入力して下さい" name="message" placeholder="お問い合わせ内容を入力して下さい" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn solid btn-default block"><i class="fa fa-envelope-o"></i> 送信する</button>
                </form><!-- / form -->
            </div><!-- / form block -->
        </div>
    </div><!-- /#popup-content -->
    <!-- ************** END : Popup form block **************  -->


    <!-- /#page -->

@endsection













