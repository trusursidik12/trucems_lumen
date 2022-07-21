@extends('layouts.theme')
@section('title', 'Configuration')
@section('css')
    <link rel="stylesheet" href="{{ url('js/kioskboard/kioskboard-2.2.0.min.css') }}">
    <link rel="stylesheet" href="{{ url('sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">
        <div class="bg-gray-300 h-[83vh] rounded-tl-3xl rounded-br-3xl">
            <div class="flex justify-between">
                <a href="{{ url('/configurations') }}" role="button" class="rounded-tl-3xl rounded-br-3xl px-5 py-4 bg-red-500 text-white">
                    Back
                </a>
                <div class="py-4">
                    <span class="bg-indigo-700 py-4 px-3 text-white">
                    </span>
                </div>
           </div>
            <div class="p-4">
                <table class="table w-full text-left rounded">
                    <thead class="text-center">
                        <th>Action</th>
                        <th>Sensor</th>
                        <th>Code</th>
                        <th>Read Command</th>
                        <th>Write Command</th>
                        <th>Quality Standard</th>
                    </thead>
                    <tbody id="tbody-logs" class="text-center">
                        @foreach ($sensors as $sensor)
                            <tr>
                                <td class="w-[9rem]">
                                    <a href="{{ url("sensor/edit/{$sensor->id}") }}" class="inline-block p-2 bg-indigo-500 text-white">Edit</a>
                                </td>
                                <td>{!! $sensor->name !!}</td>
                                <td>{{  $sensor->code  }}</td>
                                <td>{{  $sensor->read_formula  }}</td>
                                <td>{{  $sensor->write_formula  }}</td>
                                <td>{{  $sensor->quality_standard  }} <span class="text-sm">(m/g)</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
@endsection
@section('js')
@endsection
