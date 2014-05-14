<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {background: url(/assets/img/subtlenet2.png) repeat;}
        .thumbnail {display: inline-block; margin: 0 10px 10px 0; background: white; padding: 10px;}
        .thumbnail .caption {text-align: center; font-weight: bold;}
        .thumbnail img {width: 180px;}
        .group-span-filestyle {margin-left: 0 !important;}
    </style>
    <link href="/assets/css/bootstrap-responsive.min.css" rel="stylesheet">
    <!--[if lt IE 9]><script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>

<body>
    <div class="container">
        <h1>@yield('title')</h1>
        @if ($success !== null)
        <div class="alert alert-{{ $success ? 'success' : 'error' }}">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>{{ $success ? 'SUCCESS' : 'ERROR' }}!</strong>
            {{ $success ? Lang::get('reminders.ff-success') : Lang::get('reminders.ff-error') }}
        </div>
        @endif
        @yield('content')
    </div>
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.min.js"></script>
    <script src="/assets/js/bootstrap-filestyle.min.js"></script>
</body>

</html>
