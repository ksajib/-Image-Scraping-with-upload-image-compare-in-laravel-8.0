
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Assignment</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/select2.css') }}" rel="stylesheet">

  </head>

  <body>

    <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
      <h5 class="my-0 mr-md-auto font-weight-normal">Assignment</h5>
      <nav class="my-2 my-md-0 mr-md-3">
        <a class="p-2 text-dark" href="/">Add Image</a>
        <a class="p-2 text-dark" href="{{url('fortend')}}">Matching Image List</a>
      </nav>
    </div>


    <div class="container">

        @yield('content')

    </div>

    <script src="{{ asset('assets/js/jquery-1.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>

    <script>
        $("#e20").select2({
            tags:[],
            tokenSeparators: [",", " "]
        });
    </script>
  </body>
</html>
