<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ url('css/app.css') }}">
    @yield('css')

</head>
<body>
    <div class="max-w-3xl mx-auto bg-gray-200 rounded min-h-[100vh]">
        @yield('content')
    </div>
<script src="{{ url("js/jquery.min.js") }}"></script>
@yield('js')
</body>
</html>