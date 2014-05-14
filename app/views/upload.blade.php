@extends('layout')

@section('title')
Upload Your Funny Face!
@stop

@section('content')
{{ Form::open(array('url' => 'upload', 'files' => true, 'class' => 'form-horizontal')) }}
    {{ Form::token() }}
    <div class="control-group">
        <label class="control-label" for="ffPhoto">Funny Face</label>
        <div class="controls">
            <input type="file" name="photo" id="ffPhoto" class="filestyle">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="faceCaption">Caption</label>
        <div class="controls">
            <input type="text" name="caption" id="faceCaption" placeholder="e.g., Selfie!!!" class="input-xxlarge" maxlength="75">
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn btn-primary"><b class="icon-ok icon-white"></b> Add Funny Face</button>
            <a href="{{ URL::to('/') }}" class="btn"><b class="icon-chevron-left"></b> Go Back</a>
        </div>
    </div>
{{ Form::close() }}
@stop