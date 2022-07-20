@extends('layouts.theme')
@section('title', 'Configuration')
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">
        <div class="h-[88vh] bg-gray-300 rounded-tl-3xl rounded-br-3xl">
            <div class="flex justify-between">
                <a href="{{ url('/') }}" role="button" class="rounded-tl-3xl rounded-br-3xl px-5 py-4 bg-red-500 text-white">
                    Back
                </a>
                <span class="bg-indigo-700 px-5 py-4"></span>
           </div>
            <div id="error-msg">
            </div>
            <div class="flex justify-center mt-[3vh]">
                    <div>
                        @for ($i = 0; $i <= 7; $i++)
                        <div class="flex items-start justify-start w-full mb-3">
                            <label for="toggle{{ $i }}" class="flex items-center cursor-pointer">
                                <div class="relative">
                                    @php
                                        $d = "d$i";
                                    @endphp
                                    <input type="checkbox" id="toggle{{ $i }}" class="sr-only checkbox-switch"
                                        data-id="{{ $i }}" {{ $plc->$d == 1 ? 'checked' : '' }}>
                                    <div class="block bg-gray-600 w-[5vw] h-10 rounded-full"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-8 h-8 rounded-full transition"></div>
                                </div>
                                <div class="ml-3 text-gray-700 font-medium text-2xl ">
                                    @switch($i)
                                        @case(0)
                                            Sampling
                                            @break
                                        @case(1)
                                            Back Blowing
                                            @break
                                        @case(2)
                                            Run
                                            @break
                                        @case(3)
                                            Peristaltic Pump
                                            @break
                                        @case(4)
                                            System Failure
                                            @break
                                        @case(5)
                                            Back Blowing Inside The Probe
                                            @break
                                        @case(6)
                                            Back Blowing Outside The Probe
                                            @break
                                        @case(7)
                                            All Process Calibration
                                            @break
                                        @default
                                            
                                    @endswitch
                                </div>
                            </label>
            
                        </div>
                    @endfor
                </div>
           </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('.checkbox-switch').change(function() {
                $.ajax({
                    url: `{{ url('api/relay') }}`,
                    type: 'PATCH',
                    dataType: 'json',
                    data: {
                        relay_d: $(this).data('id')
                    },
                    success: function(data) {}
                })
            })
        })
    </script>
@endsection
