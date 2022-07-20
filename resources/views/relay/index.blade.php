@extends('layouts.theme')
@section('title', 'Configuration')
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">
        <div class="flex justify-between mb-3">
            <a href="{{ url('/') }}" role="button" class="rounded px-4 py-2 bg-gray-500 text-white">
                Back
            </a>

        </div>
        <div id="error-msg">
        </div>
        @for ($i = 0; $i <= 7; $i++)
            <div class="flex items-center justify-center w-full mb-3">

                <label for="toggle{{ $i }}" class="flex items-center cursor-pointer">
                    <div class="relative">
                        @php
                            $d = "d$i";
                        @endphp
                        <input type="checkbox" id="toggle{{ $i }}" class="sr-only checkbox-switch"
                            data-id="{{ $i }}" {{ $plc->$d == 1 ? 'checked' : '' }}>
                        <div class="block bg-gray-600 w-14 h-8 rounded-full"></div>
                        <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition"></div>
                    </div>
                    <div class="ml-3 text-gray-700 font-medium">
                        Relay {{ $i }}
                    </div>
                </label>

            </div>
        @endfor
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
