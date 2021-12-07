<!DOCTYPE html>
<html lang="ja" data-demomenu="y">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ナカマーカー</title>

    <!--pageMeta-->

    <!-- Lib CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&display=swap" rel="stylesheet">
    <link href="/web/minify/rgen_min.css" rel="stylesheet">
    <link href="/web/css/custom.css" rel="stylesheet">

    <!-- Favicons -->
    <link rel="icon" href="/web/images/favicons/favicon.ico">
    <link rel="apple-touch-icon" href="/web/images/favicons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/web/images/favicons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/web/images/favicons/apple-touch-icon-114x114.png">



    <!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
    <!--[if lt IE 9]>
    <script src="/web/js/html5shiv.js"></script>
    <script src="/web/js/respond.min.js"></script>
    <![endif]-->
    <!--[if IE 9 ]><script src="/web/js/ie-matchmedia.js"></script><![endif]-->

</head>
<body>
<div id="page" data-linkscroll='y'>
    @yield('app_contents')
</div>
<!-- JavaScript -->
<script>
    /* Use fonts with class name in sequence => f-1, f-2, f-3 .... */
    var fgroup = [
        'Open Sans:400,300,300italic,400italic,600,700,600italic,700italic,800,800italic',
        'Montserrat:400,700'
    ];
</script>
<script data-pace-options='{ "ajax": false }' src="/web/lib/pace/pace.min.js"></script>
<script src="/web/minify/rgen_min.js"></script>
<script async src="/web/js/rgen.js"></script>
@yield('app_js')
</body>
</html>
