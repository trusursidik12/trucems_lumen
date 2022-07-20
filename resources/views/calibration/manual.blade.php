@extends('layouts.theme')
@section('title', 'Manual Calibration')
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">
        <div class="flex justify-between mb-3">
            <a href="{{ url('/') }}" role="button" class="rounded px-4 py-2 bg-gray-500 text-white">
                Back
            </a>
            <div id="blowback-form" class="hidden flex-row space-x-3 items-center">
                <div class="text-red-500"></div>
                <button type="button" id="btn-start-blowback"
                    class="rounded disabled:bg-gray-500 px-4 py-2 bg-indigo-700 text-white">
                    Start Blow Back
                </button>
                <button type="button" id="btn-cancel-blowback"
                    class="rounded disabled:bg-gray-500 px-4 py-2 bg-red-500 text-white">
                    Close
                </button>
            </div>
            <button id="btn-show-blowback" type="button"
                class="rounded disabled:bg-gray-500 px-4 py-2 bg-indigo-700 text-white">
                Blow Back
            </button>
        </div>
        <div id="error-msg">

        </div>
        <form action="" class="bg-gray-300 h-[83vh] rounded" id="form">
            <input type="hidden" name="type">
            <div class="flex justify-between space-x-3 items-center pt-[13vh]" id="section-form">
                <div class="w-1/2 px-6 py-3 border-r-2 border-gray-400">
                    <button id="btn_zero" type="button"
                        class="btn-start disabled:bg-gray-500  rounded w-full py-4 h-56 text-xl font-bold bg-indigo-500 text-white">
                        Zero Calibration
                    </button>
                </div>
                <div class="w-1/2 px-6 py-3">
                    <button data-type="span" id="btn_span" type="button"
                        class="btn-start disabled:bg-gray-500  rounded w-full py-4 h-56 text-xl font-bold bg-indigo-500 text-white">
                        SPAN Calibration
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#btn_zero').click(function() {
                $.ajax({
                    url: `{{ url('api/calibration-start') }}`,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            window.location.href =
                                `{{ url('calibration/manual/zero/process') }}`
                        }
                    }
                })
            })
            $('#btn_span').click(function() {
                $.ajax({
                    url: `{{ url('api/calibration-start') }}`,
                    type: 'POST',
                    dataType: 'json',
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
            $('#btn-show-blowback').click(function() {
                $(this).addClass('hidden')
                $('#blowback-form').removeClass('hidden').addClass('flex')
            })
            $('#btn-cancel-blowback').click(function() {
                $('#btn-show-blowback').removeClass('hidden')
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
                            // $('#btn-start-blowback').addClass('hidden')
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
