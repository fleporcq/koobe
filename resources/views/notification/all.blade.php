@extends('layouts.master')

@section('content')
    @foreach ($notifications as $notification)
        <div class="alert alert-danger" role="alert">
            {{ $notification->pushed_at }}
            {{ $notification->message }}
        </div>
    @endforeach
@stop