@extends('layouts.theme')
@section('title','Manual Calibration')
@section('content')
<div class="px-6 py-3 bg-gray-200 rounded">
    <div class="flex justify-start mb-3">
        <a href="{{ url("/") }}" role="button" class="px-4 py-2 bg-gray-500 text-white">
            Back
        </a>
    </div>
    <div class="flex bg-gray-300 px-2 py-1">
        <table class="table w-full text-left">
            <thead>
                <th>Date Time</th>
                <th>Concentrate</th>
                <th>Unit</th>
            </thead>
            <tbody>
                @foreach ($calibrationAvgLogs as $log)
                    <tr>
                        <td>{{ $log->created_at->format("j F Y - H:i:s") }}</td>
                        <td>{{ $log->value }}</td>
                        <td>{{ $log->sensor->unit->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection