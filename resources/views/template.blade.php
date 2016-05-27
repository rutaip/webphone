<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Rutaip WebRTC</title>

    <!-- Bootstrap core CSS -->
    {!! Html::style('assets/css/bootstrap.min.css') !!}


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    {!! Html::style('assets/css/ie10-viewport-bug-workaround.css') !!}

    <!-- Custom styles for this template -->
    {!! Html::style('assets/css/navbar-fixed-top.css') !!}


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9] >
    {!! Html::script('https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js') !!}
    {!! Html::script('https://oss.maxcdn.com/respond/1.4.2/respond.min.js') !!}

    <![endif]-->
</head>
<body>
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Rutaip WebRTC</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active">{!! Html::link('home', 'Home') !!}</li>
                <!--<li><a href="#about">About</a></li>
                <li><a href="#contact">Contact</a></li>-->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin menu <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>{!! Html::link('extensions', 'Extensions') !!}</li>
                        <li>{!! Html::link('instances', 'Instances') !!}</li>
                        <!--<li role="separator" class="divider"></li>
                        <li class="dropdown-header">Nav header</li>
                        <li><a href="#">Separated link</a></li>
                        <li><a href="#">One more separated link</a></li>-->
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                @if (Auth::check())
                <li class="navbar-text">Hello, {{ Auth::user()->name }}</li>
                @endif
                <li>{!! Html::link('logout', 'Logout') !!}</li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container">

    @include('flash::message')

    @yield('content')

</div> <!-- /container -->

<script type="text/javascript">
    var LHCChatOptions = {};
    LHCChatOptions.opt = {widget_height:340,widget_width:300,popup_height:520,popup_width:500,domain:'phone.rutaip.net'};
    (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        var referrer = (document.referrer) ? encodeURIComponent(document.referrer.substr(document.referrer.indexOf('://')+1)) : '';
        var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : '';
        po.src = 'http://chatsoporte.rutaip.net/index.php/esp/chat/getstatus/(click)/internal/(position)/bottom_right/(ma)/br/(top)/350/(units)/pixels/(leaveamessage)/true/(department)/1?r='+referrer+'&l='+location;
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
</script>


<footer class="footer">
    <div class="container">
        <p>
            &copy; Rutaip 2016 <br />
            <i>Soluciones integrales VoIP</i>
        </p>
    </div>
</footer>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
{!! Html::script('https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js') !!}
<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
{!! Html::script('assets/js/bootstrap.js') !!}
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
{!! Html::script('assets/js/ie10-viewport-bug-workaround.js') !!}
        <!-- Custom scripts -->
{!! Html::script('assets/js/custom.js') !!}
</body>
</html>