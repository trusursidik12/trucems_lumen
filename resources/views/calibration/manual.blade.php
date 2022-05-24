<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ url('css/app.css') }}">
</head>
<body>
    <div class="max-w-3xl mx-auto min-h-full">
        <div class="px-6 py-3 bg-gray-200 rounded">
            <div class="flex justify-start mb-3">
                <a href="{{ url("/") }}" role="button" class="px-4 py-2 bg-gray-500 text-white">
                    Back
                </a>
            </div>
            <form action="">
                <div class="flex justify-between space-x-3">
                    <div class="w-1/2 px-6 py-3 border-r-2 border-gray-300">
                        <div class="flex justify-between items-center">
                            <span class="w-2/2">
                                <span class="uppercase font-semibold">Default Zero Loop</span>
                            </span>
                            <span class="w-1/3">
                                <input type="text" class="px-3 py-1 outline-none w-full">
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script src="{{ url("js/jquery.min.js") }}"></script>
</body>
</html>