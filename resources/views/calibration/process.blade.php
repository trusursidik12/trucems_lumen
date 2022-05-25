@extends('layouts.theme')
@section('title',"Process {$mode} {$type} Calibration")
@section('css')
@endsection
@section('content')
<div class="px-6 py-3 bg-gray-200 rounded">
    <div class="flex justify-start mb-3">
        <a href="#" role="button" disabled class="btn-back px-4 py-2 bg-gray-500 text-white">
            Back
        </a>
    </div>
    <div class="flex justify-content-betwen bg-gray-300 px-4 py-3">
        <div class="w-1/2 border-r border-gray-400 block">
            <p class="block font-semibold text-sm text-indigo-700">Realtime Value : </p>
            <span class="block ml-3">
                <p class="block text-xs">1 PPM 02:00:01</p>
                <p class="block text-xs">1 PPM 02:00:02</p>
                <p class="block text-xs">1 PPM 02:00:03</p>
            </span>
            <p class="block font-semibold text-sm text-indigo-700">Last Avg.: 1 PPM</p>
            @foreach ($sensorValues as $value)
            <div class="flex justify-between items-center px-3 section-value">
                <span class="text-xl sensor-name">{!! $value->sensor->name !!}</span>
                <span class="text-8xl font-bold text-indigo-700 sensor-value">4</span>
                <span class="text-xl sensor-unit">{{ $value->sensor->unit->name }}</span>
            </div>
            @endforeach
        </div>
        <div class="w-1/2">
            <div class="wrapper mx-auto">
                <div class="clock"></div>
                <div class="clock"></div>
            </div>
            <div class="text-center">
                
                <p class="text-2xl mb-3 uppercase font-semibold font-sans">Calibrating</p>
                <p class="text-2xl mb-3 uppercase font-semibold font-sans">{{ $type }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@endsection