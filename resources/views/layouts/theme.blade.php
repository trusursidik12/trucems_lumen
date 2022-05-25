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
    <div class="max-w-3xl mx-auto bg-gray-200 rounded min-h-[100vh] relative">
        @yield('content')
        <div class="absolute bottom-0 w-full flex justify-end px-5 py-2">
            <span class="text-gray-800">&copy; {{ date('Y') }} Developed by PT. Trusur Unggul Teknusa</span> 
        </div>
    </div>
   
<script src="{{ url("js/jquery.min.js") }}"></script>
@yield('js')
</body>
</html>