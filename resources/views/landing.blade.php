@extends('layouts.web')
@section('content')

@php
$mess = Session::get('message');
if ($mess) :
@endphp
  <strong>{{ $mess['text'] }}</strong>
@php
endif;
@endphp

<form method="POST" action="{{ url('fortend') }}"  class="form-horizontal" enctype="multipart/form-data">
  @csrf
  <div class="form-group">
    @if ($errors->has('web_url'))
        <strong>{{ $errors->first('web_url') }}</strong><br>
    @endif
    <label for="webUrl">Web URL</label>
    <input type="text" class="form-control" name="web_url" id="webUrl"  placeholder="Web URL" value="{{ old('web_url') }}">
  </div>

  <div class="form-group">
    @if ($errors->has('image'))
        <strong>{{ $errors->first('image') }}</strong><br>
    @endif
    <label for="image">Image</label>
    <input type="file" class="form-control-file" id="image" name="image">
  </div>

  <div class="form-group">
    @if ($errors->has('hashTag'))
        <strong>{{ $errors->first('hashTag') }}</strong><br>
    @endif
    <label for="image">Hash Tag</label>
    <input type="text" class="form-control-file" id="e20" name="hashTag" value="{{ old('hashTag') }}">
  </div>

  <button type="submit" class="btn btn-primary">Submit</button>
</form>


@if (Session::has('message'))
<div class="col-md-12 pt-4">
  <ul class="list-group">
    
      @php
        $mess = Session::get('message');
        if ($mess['status'] == true) :
          foreach ($mess['data'] as $value) :
            if ($value['match_percent'] == 0) :
            @endphp
            <li class="list-group-item">
              Matched Images
            </li>
            <li class="list-group-item">
              <img src="{{ asset('images/'.$value['image']) }}" alt="Match Image" style="width: 20%">
            </li>
            <li class="list-group-item">
              Upload Images
            </li>
            <li class="list-group-item">
              <img src="{{ asset('images/'.$mess['upload_image']) }}" alt="Upload Image" style="width: 20%">
            </li>    
            @php
            endif;

            if ($value['match_percent'] > 0) :
            @endphp
            <li class="list-group-item">
              Possible Matches
            </li>
            <li class="list-group-item">
              <img src="{{ asset('images/'.$value['image']) }}" alt="Match Image" style="width: 20%">
            </li>
            <li class="list-group-item">
              Upload Images
            </li>
            <li class="list-group-item">
              <img src="{{ asset('images/'.$mess['upload_image']) }}" alt="Upload Image" style="width: 20%">
            </li>
            @php
            endif;
          endforeach;
        endif;
      @endphp
    
  </ul>
</div>
@endif
@endsection
