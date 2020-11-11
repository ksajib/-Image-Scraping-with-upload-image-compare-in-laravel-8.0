@extends('layouts.web')
@section('content')
<form method="GET" action="{{ route('fortend.index') }}" class="form-inline">
    <div class="box-header text-right">
        <div class="row">

            <div class="form-group">
                <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}" placeholder="Hash Tag Search">
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-flat">Search</button>
            </div>
        </div>
    </div>
</form>
<span class="text-muted">Showing {{$records->currentPage()*$records->perPage()-$records->perPage()+1}} to {{ ($records->currentPage()*$records->perPage()>$records->total())?$records->total():$records->currentPage()*$records->perPage()}} of {{$records->total()}} data(s)</span>
<table class="table table-bordered">
    <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col">Web URL</th>
        <th scope="col">Upload Image</th>
        <th scope="col">Web Image</th>
        <th scope="col">Hash Tag</th>
        <th scope="col">Status</th>
        <th scope="col">Date</th>
      </tr>
    </thead>
    <tbody>
        @foreach($records as $key => $val)
            <tr>
                <td>{{$serial++}}</td>
                <td>{{$val->web_url}}</td>
                <td><img src="{{ asset('images/'.$val->image) }}" alt="Upload Image" style="width: 50%"></td>
                <td><img src="{{ asset('images/'.$val->url_image) }}" alt="Upload Image" style="width: 50%"></td>
                <td>{{$val->hash_tag}}</td>
                <td>{{$val->status == 1 ? 'Matched Images' : ''}}</td>
                <td>{{$val->created_at}}</td>
            </tr>
        @endforeach
    </tbody>
  </table>
  <div class="text-right">
    {{ $records->appends(Request::except('page'))->links() }}
</div>

@endsection
