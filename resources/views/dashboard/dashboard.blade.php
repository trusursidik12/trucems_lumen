@extends('layouts.theme')
@section('title', 'Dashboard')
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">
        <div class="flex justify-start mb-3">
            <div>
                <span class="text-gray-700">Runtime : </span>
                <span id="runtime" class="text-indigo-900 text-bold"></span>
            </div>
        </div>
        <div>
            <p class="px-3 py-2 bg-red-500 text-white rounded hidden" id="error-msg">

            </p>
        </div>
        <div class="flex justify-between pt-[14vh] space-x-3">

            <div class="w-full">
                <nav class="sidebar grid grid-rows justify-center gap-3 h-full">
                    <button type="button" id="btn-start-cems" data-status="{{ $plc->is_maintenance == 1 ? 0 : 1 }}"
                        class="{{ $plc->is_maintenance == 0 ? 'deactive' : 'active' }}">
                        {{ $plc->is_maintenance == 1 ? ' Start CEMS' : 'Stop CEMS' }}
                    </button>
                    <button type="button" id="btn-start-cal" class="{{ $plc->is_calibration == 1 ? 'deactive' : '' }}"
                        data-status="{{ $plc->is_calibration == 1 ? 0 : 1 }}">
                        {{ $plc->is_calibration == 1 ? 'Stop Calibration' : 'Start Calibration' }}
                    </button>
                    <button type="button" id="btn-start-mt" data-status="{{ $plc->is_maintenance == 1 ? 0 : 1 }}"
                        class="{{ $plc->is_maintenance == 1 ? 'deactive' : '' }}">
                        {{ $plc->is_maintenance == 1 ? 'Stop Maintenance' : 'Start Maintenance' }}
                    </button>
                    <button onclick="return window.location.href=`{{ url('api/relay') }}`" id="btn-relay-test-menu"
                        class="{{ $plc->is_maintenance == 1 ? '' : 'hide' }} active">
                        Relay Test
                    </button>
                    <button onclick="return window.location.href=`{{ url('configurations') }}`">Configurations</button>
                </nav>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            if (localStorage.getItem("unit") === undefined) {
                localStorage.setItem("unit", "ppm")
            }

            function switchUnit(isBtn = false) {
                let unit = localStorage.getItem("unit")
                if (unit === "ppm") {
                    if (isBtn) {
                        $('.sensor-unit').html("mg/m<sup>3</sup>")
                        localStorage.setItem("unit", "mg/m3")
                        $('#btn-switch').html(unit)
                    } else {
                        $('#btn-switch').html("mg/m<sup>3</sup>")
                        $('.sensor-unit').html(unit)
                    }
                } else {
                    if (isBtn) {
                        $('.sensor-unit').html("ppm")
                        localStorage.setItem("unit", "ppm")
                        $('#btn-switch').html(unit)
                    } else {
                        $('#btn-switch').html("ppm")
                        $('.sensor-unit').html(unit)
                    }
                }
            }
            switchUnit()
            $('#btn-switch').click(function() {
                switchUnit(true)
            })

            function getValues() {
                let random = Math.floor(Math.random() * 100)
                $.ajax({
                    url: `{{ url('api/sensor-value-logs') }}?t=${random}`,
                    type: 'get',
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data) {
                        let concentrate = 0
                        // 0.0409 * konsentrasi * 34.08
                        let unit = localStorage.getItem("unit")
                        let section = $('#section-values')
                        if (data.success) {
                            let sensorValues = data.data
                            sensorValues.map(function(value) {
                                if (unit === "mg/m3") {
                                    concentrate = Math.round((0.0409 * value.value * 34.08) *
                                        1000) / 1000
                                    // Formula is (0.0409 * concentrate * 34.08)
                                    // * 1000 and / 1000 is for rounding 3 decimal places
                                } else {
                                    concentrate = value.value
                                }
                                let div = section.find(`.sensor_id[value=${value.sensor_id}]`)
                                    .parent()
                                div.find('.sensor-value').html(`${concentrate}`)
                            })
                        }
                    }
                })
                setTimeout(getValues, 1000);
            }
            getValues()
        })
    </script>
    <script>
        $(document).ready(function() {
            function loadRuntime() {
                $.ajax({
                    url: `{{ url('api/runtime') }}`,
                    type: 'get',
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data) {
                        if (data.success) {
                            let runtime = data.data
                            $('#runtime').html(`${runtime.days}:${runtime.hours}:${runtime.minutes}`)
                        }
                    }
                })
                setTimeout(loadRuntime, (1000 * 60)); //load every  mins
            }
            loadRuntime()
        })
    </script>
    <script>
        $(document).ready(function() {
            $('#btn-start-cems').click(function(e) {
                $(this).html('Loading...')
                $('button').prop('disabled', true)
                e.preventDefault()
                $.ajax({
                    url: `{{ url('api/start-plc') }}`,
                    type: 'PATCH',
                    dataType: 'json',
                    data: {
                        status: $('#btn-start-cems').attr('data-status')
                    },
                    success: function(data) {

                        if (data.success) {
                            setTimeout(() => {
                                $('button').prop('disabled', false)
                                if (data.data.is_maintenance == 0) {
                                    $('#btn-start-cems').attr('data-status', "1")
                                    $('#btn-start-cems').removeClass('active').addClass(
                                        'deactive')
                                    $('#btn-start-cems').html('Stop CEMS')

                                    $('#btn-start-mt').attr('data-status', "1")
                                    $('#btn-start-mt').removeClass('deactive')
                                    $('#btn-start-mt').html('Start Maintenance')
                                } else {
                                    $('#btn-start-cems').attr('data-status', "0")
                                    $('#btn-start-cems').removeClass('deactive')
                                        .addClass('active')
                                    $('#btn-start-cems').html('Start CEMS')
                                }
                            }, 5000);
                        } else {
                            $('button').prop('disabled', false)
                            $('#btn-start-cems').attr('data-status', "1")
                            $('#btn-start-cems').removeClass('active')
                            $('#btn-start-cems').addClass('deactive')
                            $('#btn-start-cems').html('Stop CEMS')
                            $('#error-msg').removeClass('hidden')
                            $('#error-msg').html(data.message)
                            setTimeout(() => {
                                $('#error-msg').addClass('hidden')
                            }, 3000);
                        }
                    }
                })
            })
            $('#btn-start-cal').click(function(e) {
                e.preventDefault()
                $(this).html('Loading...')
                $('button').prop('disabled', true)
                $.ajax({
                    url: `{{ url('api/start-cal') }}`,
                    type: 'PATCH',
                    dataType: 'json',
                    data: {
                        status: $('#btn-start-cal').attr('data-status')
                    },
                    success: function(data) {
                        if (data.success) {
                            setTimeout(() => {
                                $('button').prop('disabled', false)
                                if (data.data.is_calibration == 0 || data.data.is_calibration == 2) {
                                    $('#btn-start-cal').attr('data-status', "1")
                                    $('#btn-start-cal').removeClass('deactive')
                                    $('#btn-start-cal').html('Start Calibration')
                                    $('#btn-cal-menu').addClass('hide')
                                } else {
                                    $('#btn-start-cal').removeClass('deactive')
                                    $('#btn-start-cal').attr('data-status', "0")
                                    $('#btn-start-cal').addClass('deactive')
                                    $('#btn-start-cal').html('Stop Calibration')
                                    $('#btn-cal-menu').removeClass('hide')
                                }
                            }, 10000);
                        } else {
                            $('button').prop('disabled', false)
                            $('#btn-start-cal').attr('data-status', "1")
                            $('#btn-start-cal').removeClass('deactive')
                            $('#btn-start-cal').html('Start CEMS')
                            $('#btn-cal-menu').addClass('hide')
                            $('#error-msg').removeClass('hidden')
                            $('#error-msg').html(data.message)
                            setTimeout(() => {
                                $('#error-msg').addClass('hidden')
                            }, 3000);
                        }
                    }
                })
            })
            $('#btn-start-mt').click(function(e) {
                e.preventDefault()
                $(this).html('Loading...')
                $('button').prop('disabled', true)
                $.ajax({
                    url: `{{ url('api/start-plc') }}`,
                    type: 'PATCH',
                    dataType: 'json',
                    data: {
                        status: $('#btn-start-mt').attr('data-status')
                    },
                    success: function(data) {
                        if (data.success) {
                            setTimeout(() => {
                                $('button').prop('disabled', false)
                                if (data.data.is_maintenance == 0) {
                                    $('#btn-start-mt').attr('data-status', "1")
                                    $('#btn-start-mt').removeClass('deactive')
                                    $('#btn-start-mt').html('Start Maintenance')

                                    $('#btn-start-cems').attr('data-status', "1")
                                    $('#btn-start-cems').removeClass('active').addClass(
                                        'deactive')
                                    $('#btn-start-cems').html('Stop CEMS')
                                    $('#btn-relay-test-menu').addClass('hide')
                                } else {
                                    $('#btn-start-mt').attr('data-status', "0")
                                    $('#btn-start-mt').addClass('deactive')
                                    $('#btn-start-mt').html('Stop Maintenance')

                                    $('#btn-start-cems').attr('data-status', "0")
                                    $('#btn-start-cems').removeClass('deactive')
                                        .addClass('active')
                                    $('#btn-start-cems').html('Start CEMS')
                                    $('#btn-relay-test-menu').removeClass('hide')
                                }
                            }, 10000);
                        } else {
                            $('button').prop('disabled', false)
                            $('#btn-start-mt').attr('data-status', "1")
                            $('#btn-start-mt').removeClass('deactive')
                            $('#btn-start-mt').html('Start Maintenance')
                            $('#error-msg').removeClass('hidden')
                            $('#error-msg').html(data.message)
                            $('#btn-relay-test-menu').addClass('hide')
                            setTimeout(() => {
                                $('#error-msg').addClass('hidden')
                            }, 3000);
                        }
                    }
                })
            })
        })
    </script>
@endsection
