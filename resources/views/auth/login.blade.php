<?php $appName = Config::get('app.name'); ?>
<!doctype html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title>{{ $appName }}</title>
	{!! HTML::style('assets/stylesheets/main.css') !!}
	{!! HTML::style('assets/stylesheets/login.css') !!}
</head>
<body>
<div class="container">
	{!! Form::open(array('url' => URL::action('Auth\AuthController@login'), 'class' => 'form-signin')) !!}
	<h2 class="form-signin-heading">{{ $appName }}</h2>
	@if(Session::has('error'))
		<div class="alert alert-danger">{{ Session::get('error') }}</div>
	@endif
	{!! Form::label('email',Lang::get('messages.authentication.email'), array('class' => 'sr-only')) !!}
	{!! Form::email('email', old('email'), array('class' => 'form-control', 'placeholder' => Lang::get('messages.authentication.email'))) !!}
	{!! Form::label('password',Lang::get('messages.authentication.password'), array('class' => 'sr-only')) !!}
	{!! Form::password('password', array('class' => 'form-control', 'placeholder' => Lang::get('messages.authentication.password'))) !!}
	{!! Form::submit(Lang::get('messages.authentication.signIn'), array('class'=> 'btn btn-lg btn-primary btn-block')) !!}
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	{!! Form::close() !!}
</div>
</body>
</html>