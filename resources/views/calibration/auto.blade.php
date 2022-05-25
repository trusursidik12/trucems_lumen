@extends('layouts.theme')
@section('title','Auto Calibration')
@section('content')
<div class="px-6 py-3">
    <div class="flex justify-start mb-3">
        <a href="{{ url("/") }}" role="button" class="px-4 py-2 bg-gray-500 text-white">
            Back
        </a>
    </div>
    <form action="">
        <div class="flex justify-between space-x-3">
            <div class="w-1/2 px-6 py-3 border-r-2 border-gray-300">
                <div class="flex my-2 justify-between items-center">
                    <span class="w-2/2">
                        <span class="uppercase font-semibold">Default Zero Loop</span>
                    </span>
                    <span class="w-1/3">
                        <input type="text" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="1" class="js-virtual-keyboard px-3 py-1 outline-none w-full">
                    </span>
                </div>
                <div class="flex my-2 justify-between items-center">
                    <span class="w-2/2">
                        <span class="uppercase font-semibold">Time Zero Loop <small class="font-thin text-xs lowercase">(sec)</small></span>
                    </span>
                    <span class="w-1/3">
                        <input type="text" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="200" class="js-virtual-keyboard px-3 py-1 outline-none w-full">
                    </span>
                </div>
            </div>
            <div class="w-1/2 px-6 py-3">
                <div class="flex my-2 justify-between items-center">
                    <span class="w-2/2">
                        <span class="uppercase font-semibold">Default Span Loop</span>
                    </span>
                    <span class="w-1/3">
                        <input type="text" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="1" class="js-virtual-keyboard px-3 py-1 outline-none w-full">
                    </span>
                </div>
                <div class="flex my-2 justify-between items-center">
                    <span class="w-2/2">
                        <span class="uppercase font-semibold">Time Span Loop <small class="font-thin text-xs lowercase">(sec)</small></span>
                    </span>
                    <span class="w-1/3">
                        <input type="text" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="200" class="js-virtual-keyboard px-3 py-1 outline-none w-full">
                    </span>
                </div>
                <div class="flex my-2 justify-between items-center">
                    <span class="w-2/2">
                        <span class="uppercase font-semibold">Max Span PPM</span>
                    </span>
                    <span class="w-1/3">
                        <input type="text" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="200" class="js-virtual-keyboard px-3 py-1 outline-none w-full">
                    </span>
                </div>
                <button class="w-full px-3 py-2 bg-indigo-500 text-white">Start Manual Calibration</button>
            </div>
        </div>
    </form>
</div>
@endsection