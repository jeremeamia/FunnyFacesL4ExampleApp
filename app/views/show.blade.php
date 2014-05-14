@extends('layout')

@section('title')
Show Me Your Funny Faces!
@stop

@section('content')
    <p><a href="{{ URL::to('upload') }}" class="btn"><b class="icon-plus-sign"></b> Add a Funny Face</a></p>
        @foreach ($faces as $face)
        <div class="thumbnail">
            {{ HTML::image($face['src'], $face['caption'], ['class' => 'img-thumbnail']) }}
            <div class="caption"><p>{{ $face['caption'] }}</p></div>
        </div>
        @endforeach
    </div>
@stop
