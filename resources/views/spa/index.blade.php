<!doctype html>
<html lang="fr" ng-app="koobeApp">
<head>
    <meta charset="UTF-8">
    <title>{{$appName}}</title>
    {!! HTML::style('assets/stylesheets/main.css') !!}
    {!! HTML::script('assets/javascript/main.js') !!}
    {!! HTML::script('assets/javascript/angular.js') !!}
    {!! HTML::script('assets/javascript/app.js') !!}
    {!! HTML::script('assets/javascript/controllers.js') !!}
    {!! HTML::script('assets/javascript/masonry.js') !!}
</head>
<body>


<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand">{{ $appName }}</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="#/books">@lang('messages.navbar.home')</a></li>
                <li><a href="#/upload">@lang('messages.navbar.upload')</a></li>
                <li><a href="#/notifications">@lang('messages.navbar.notifications')</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="{{ URL::action('Auth\AuthController@logout') }}"><span class="glyphicon glyphicon-off"></span>&nbsp;@lang('messages.navbar.logout')</a></li>
            </ul>
        </div>
    </div>
</nav>

<div ng-view></div>

</body>
</html>