<!doctype html>
<html lang="fr" {!!isset($htmlTagAttrs) ? HTML::attributes($htmlTagAttrs) : null!!}>
    <head>
        <meta charset="UTF-8">
        <title>{{$title or $appName}}</title>
        {!! HTML::style('assets/stylesheets/main.css') !!}
        @section('styles')
        @show
        {!! HTML::script('assets/javascript/main.js') !!}
    </head>
    <body {!!isset($bodyTagAttrs) ? HTML::attributes($bodyTagAttrs) : null!!}>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{ URL::action('HomeController@index') }}">{{ $appName }}</a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li{!! $currentAction == "HomeController@index" ? ' class="active"' : '' !!}><a href="{{ URL::action('HomeController@index') }}">@lang('messages.navbar.home')</a></li>
                        <li{!! $currentAction == "BookController@upload" ? ' class="active"' : '' !!}><a href="{{ URL::action('BookController@upload') }}">@lang('messages.navbar.upload')</a></li>
                        <li{!! $currentAction == "NotificationController@all" ? ' class="active"' : '' !!}><a href="{{ URL::action('NotificationController@all') }}">@lang('messages.navbar.notifications')</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="{{ URL::action('Auth\AuthController@logout') }}"><span class="glyphicon glyphicon-off"></span>&nbsp;@lang('messages.navbar.logout')</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            @yield('content')
        </div>
        @section('scripts')
        @show
    </body>
</html>
