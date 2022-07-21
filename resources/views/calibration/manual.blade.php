@extends('layouts.theme')
@section('title', 'Manual Calibration')
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">

        <div id="error-msg">

        </div>
        <form action="" class="bg-gray-300 h-[88vh]  rounded-tl-3xl rounded-br-3xl" id="form">
            <div class="flex justify-between items-start">
                <a href="{{ url('/') }}" role="button"
                    class="rounded-tl-3xl rounded-br-3xl px-5 py-4 bg-red-500 text-white">
                    Back
                </a>
                <div>
                    <button id="btn-show-cga" type="button" class="disabled:bg-gray-500 px-5 py-4 bg-indigo-700 text-white">
                        CGA
                    </button>
                    <button id="btn-show-blowback" type="button"
                        class="disabled:bg-gray-500 px-5 py-4 bg-indigo-700 text-white">
                        Blow Back
                    </button>
                    <div class="flex justify-between mb-3">
                        <div id="blowback-form" class="hidden flex-row space-x-3 items-center">
                            <div class="text-red-500"></div>
                            <button type="button" id="btn-start-blowback"
                                class="disabled:bg-gray-500 px-5 py-4 bg-indigo-700 text-white">
                                Start Blow Back
                            </button>
                            <button type="button" id="btn-cancel-blowback"
                                class="disabled:bg-gray-500 px-5 py-4 bg-red-500 text-white">
                                Close
                            </button>
                        </div>
                        <div id="cga-form" class="hidden flex-row space-x-3 items-center">
                            <div class="text-red-500"></div>
                            <button type="button" id="btn-start-cga"
                                class="disabled:bg-gray-500 px-5 py-4 bg-indigo-700 text-white">
                                Start CGA
                            </button>
                            <button type="button" id="btn-cancel-cga"
                                class="disabled:bg-gray-500 px-5 py-4 bg-red-500 text-white">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($sensors as $sensor)
                <div class="flex justify-between space-x-3 items-center" id="section-form">
                    <div class="w-1/2 px-6 py-3 border-r-2 border-gray-400">
                        <button data-sensorId="{{ $sensor->id }}" type="button"
                            class="btn-start btn_zero disabled:bg-gray-500 w-full py-4 h-[7rem] text-xl font-bold bg-indigo-500 text-white">
                            {!! $sensor->name !!} ZERO Calibration
                        </button>
                    </div>
                    <div class="w-1/2 px-6 py-3">
                        <button data-sensorId="{{ $sensor->id }}" data-type="span" type="button"
                            class="btn-start btn_span disabled:bg-gray-500 w-full py-4 h-[7rem] text-xl font-bold bg-indigo-500 text-white">
                            {!! $sensor->name !!} SPAN Calibration
                        </button>
                    </div>
                </div>
            @endforeach
        </form>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('.btn_zero').click(function() {
                let sensorId = $(this).attr("data-sensorId")
                $.ajax({
                    url: `{{ url('api/calibration-start') }}/${sensorId}`,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        calibration_type: 1
                    },
                    success: function(data) {
                        if (data.success) {
                            window.location.href =
                                `{{ url('calibration/manual/zero/process') }}`
                        }
                    }
                })
            })
            $('.btn_span').click(function() {
                let sensorId = $(this).attr("data-sensorId")
                $.ajax({
                    url: `{{ url('api/calibration-start') }}/${sensorId}`,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        calibration_type: 2
                    },
                    success: function(data) {
                        if (data.success) {
                            window.location.href =
                                `{{ url('calibration/manual/span/process') }}`
                        }
                    }
                })
            })
        })
    </script>
    <script>
        // Blowback function
        $(document).ready(function() {
            // Manipulate element HTML
            $('#btn-show-cga').click(function() {
                $('#btn-show-blowback').addClass('hidden')
                $(this).addClass('hidden')
                $('#cga-form').removeClass('hidden').addClass('flex')
            })
            $('#btn-cancel-cga').click(function() {
                $('#btn-show-blowback').removeClass('hidden')
                $('#btn-show-cga').removeClass('hidden')
                $('#cga-form').addClass('hidden').removeClass('flex')
            })
            //
            $('#btn-start-cga').click(function() {
                $(this).html(`CGA...`)
                $.ajax({
                    url: `{{ url('api/cga') }}`,
                    type: 'PATCH',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            setTimeout(() => {
                                window.location.href =
                                    `{{ url('cga/process') }}`
                            }, 5000);
                        }
                    }
                })
            })

            $('#btn-show-blowback').click(function() {
                $(this).addClass('hidden')
                $('#btn-show-cga').addClass('hidden')
                $('#blowback-form').removeClass('hidden').addClass('flex')
            })
            $('#btn-cancel-blowback').click(function() {
                $('#btn-show-blowback').removeClass('hidden')
                $('#btn-show-cga').removeClass('hidden')
                $('#blowback-form').addClass('hidden').removeClass('flex')
            })
            //
            var intervalBlowback
            $('#btn-start-blowback').click(function() {
                $(this).html(`Blowback...`)
                $('button').prop('disabled', true)
                $.ajax({
                    url: `{{ url('api/blowback') }}`,
                    type: 'PATCH',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            $('#btn-cancel-blowback').addClass('hidden')
                            intervalBlowback = setInterval(checkBlowback, 1000);
                        }
                    }
                })
            })

            function checkBlowback() {
                $.ajax({
                    url: `{{ url('api/blowback') }}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.is_blowback == 0) {
                            clearInterval(intervalBlowback)
                            $('button').prop('disabled', false)
                            $('#btn-start-blowback').html('Start Blowback')
                            $('#btn-start-blowback').removeClass('hidden')
                            $('#btn-cancel-blowback').removeClass('hidden')
                            $('#btn-cancel-blowback').trigger('click')
                        }
                    }
                })
            }
        })
    </script>
    <script>
        $(document).ready(function() {
            @if ($config->is_blowback == 1)
                $('#btn-show-blowback').trigger('click')
                $('#btn-start-blowback').trigger('click')
            @endif
        })
    </script>
@endsection
