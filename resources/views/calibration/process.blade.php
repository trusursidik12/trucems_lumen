@extends('layouts.theme')
@section('title', "Process {$mode} {$type} Calibration")
@section('css')
    <link rel="stylesheet" href="{{ url('js/kioskboard/kioskboard-2.2.0.min.css') }}">
    <link rel="stylesheet" href="{{ url('sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">

        <div class="h-[88vh] bg-gray-300 rounded-xl">
            <button id="btn_close"
                class="px-5 py-4 bg-red-500 rounded-tl-xl rounded-br-xl text-white disabled:bg-gray-500"{{ @$calibrationLog->result_value == null ? 'disabled' : '' }}>Close</button>
            <div class="flex justify-content-betwen items-center px-4 py-24">
                <div class="w-1/2 border-r border-gray-400 block items-center" id="section-left">
                    <p class="block font-semibold text-sm text-indigo-700">Realtime Value : </p>
                    <span class="block ml-3" id="section-logs">
                    </span>
                    <p class="block font-semibold text-sm text-indigo-700 last-avg">Last AVG. Value :</p>
                    <span
                        class="block font-semibold text-sm text-gray-700 ml-3">{{ @$lastAvg->value ? $lastAvg->value : 0 }}
                        PPM</span>
                    <p class="block font-semibold text-sm text-indigo-700 last-avg">Current Value :</p>
                    <div id="section-values">
                        @foreach ($sensorValues as $value)
                            <div class="flex justify-between items-center px-3 section-value"
                                data-sensor-id="{{ $value->sensor_id }}">
                                <span class="text-xl sensor-name">{!! $value->sensor->name !!}</span>
                                <span class="text-8xl font-bold text-indigo-700 sensor-value">4</span>
                                <span class="text-xl sensor-unit">{{ $value->sensor->unit->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="w-1/2" id="section-right">
                    <div class="wrapper mx-auto">
                        <div class="clock"></div>
                        <div class="clock"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl mb-3 uppercase font-semibold font-sans">Calibrating</p>
                        <p class="text-2xl mb-3 uppercase font-semibold font-sans">{{ $type }}</p>
                        <p id="remaining"></p>
                        <div id="last-value" class="hidden">
                            <p class="text-indigo-500">Last Value : <span class="last-value"></span> ppm</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full px-3">
                <div id="error-msg"></div>
                <form id="form" class="mx-auto max-w-screen-sm">
                    <input type="text" name="target_value" value="" data-kioskboard-type="numpad"
                        class="js-virtual-keyboard px-5 py-4 rounded w-1/2" placeholder="Target Value">
                    <button type="submit" id="btn_set_target_value"
                        class="px-5 py-4 bg-indigo-500 rounded text-white disabled:bg-gray-500"
                        {{ @$calibrationLog->result_value == null ? 'disabled' : '' }}>
                        Set Target</button>
                    <button type="button" id="btn_last_data"
                        class="px-5 py-4 bg-blue-500 rounded text-white disabled:bg-gray-500"
                        {{ @$calibrationLog->result_value == null ? '' : 'disabled' }}>Save Last
                        Data</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ url('sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ url('js/kioskboard/kioskboard-2.2.0.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            KioskBoard.init({
                keysArrayOfObjects: [{
                        "0": "Q",
                        "1": "W",
                        "2": "E",
                        "3": "R",
                        "4": "T",
                        "5": "Y",
                        "6": "U",
                        "7": "I",
                        "8": "O",
                        "9": "P"
                    },
                    {
                        "0": "A",
                        "1": "S",
                        "2": "D",
                        "3": "F",
                        "4": "G",
                        "5": "H",
                        "6": "J",
                        "7": "K",
                        "8": "L"
                    },
                    {
                        "0": "Z",
                        "1": "X",
                        "2": "C",
                        "3": "V",
                        "4": "B",
                        "5": "N",
                        "6": "M"
                    }
                ],
                keysJsonUrl: `{{ url('js/kioskboard-keys-english.json') }}`,
                // Language Code (ISO 639-1) for custom keys (for language support) => e.g. "de" || "en" || "fr" || "hu" || "tr" etc...
                language: 'en',
                // The theme of keyboard => "light" || "dark" || "flat" || "material" || "oldschool"
                theme: 'oldschool',
                // Uppercase or lowercase to start. Uppercased when "true"
                capsLockActive: true,

                /*
                 * Allow or prevent real/physical keyboard usage. Prevented when "false"
                 * In addition, the "allowMobileKeyboard" option must be "true" as well, if the real/physical keyboard has wanted to be used.
                 */
                allowRealKeyboard: true,

                // Allow or prevent mobile keyboard usage. Prevented when "false"
                allowMobileKeyboard: true,

                // CSS animations for opening or closing the keyboard
                cssAnimations: true,

                // CSS animations duration as millisecond
                cssAnimationsDuration: 360,

                // CSS animations style for opening or closing the keyboard => "slide" || "fade"
                cssAnimationsStyle: 'slide',

                // Enable or Disable Spacebar functionality on the keyboard. The Spacebar will be passive when "false"
                keysAllowSpacebar: true,

                // Text of the space key (Spacebar). Without text => " "
                keysSpacebarText: 'Space',

                // Font family of the keys
                keysFontFamily: 'sans-serif',

                // Font size of the keys
                keysFontSize: '16px',

                // Font weight of the keys
                keysFontWeight: 'normal',

                // Size of the icon keys
                keysIconSize: '22px',

                // Scrolls the document to the top or bottom(by the placement option) of the input/textarea element. Prevented when "false"
                autoScroll: true,
            })
            KioskBoard.run('.js-virtual-keyboard', {

            })
        })
    </script>
    <script>
        $(document).ready(function() {
            let internvalRealtime = setInterval(getRealtimeValue, 1000);

            function getRealtimeValue() {
                let random = Math.floor(Math.random() * 100)
                $.ajax({
                    url: `{{ url('api/calibration/check-remaining') . '/' . strtolower($mode) . '/' . strtolower($type) }}?t=${random}`,
                    type: 'get',
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data) {
                        let section = $('#section-values')
                        let sectionLogs = $('#section-logs')
                        if (data.success) {
                            if (data.remaining_time < 0) {
                                clearInterval(internvalRealtime)
                                $('#section-left').removeClass('block')
                                $('#section-left').addClass('hidden')
                                $('#section-right').removeClass('w-1/2')
                                $('#section-right').addClass('w-full')
                                $('#remaining').addClass('hidden')
                                $('#last-value').removeClass('hidden')
                            }
                            let sensorValues = data.sensor_values
                            sensorValues.map(function(value) {
                                let div = section.find(
                                    `.section-value[data-sensor-id=${value.sensor_id}]`)
                                div.find('.sensor-value').html(`${value.value}`)
                                $('.last-value').html(`${value.value}`)
                            })
                            // Logs
                            let calibrationLogs = data.calibration_logs
                            let i = 2
                            let logs = []
                            let html = ``
                            calibrationLogs.map(function(value) {
                                logs[i] =
                                    ` <p class="block text-xs">${value.value} ${value.sensor.unit.name} - ${value.created_at}</p>`
                                i--
                            })
                            logs.map(function(element) {
                                html += element
                            })
                            sectionLogs.html(html)
                            $('#remaining').html(`${data.remaining_time} sec`)
                        }
                    }
                })
                // setTimeout(getRealtimeValue, 1000);
            }
        })
    </script>
    <script>
        $(document).ready(function() {
            $('#form').submit(function(e) {
                e.preventDefault()
                $.ajax({
                    url: `{{ url('api/calibration-set-value') . '/' . ($type == 'ZERO' ? 1 : 2) }}`,
                    type: 'POST',
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data) {
                        if (data.success) {
                            $('#btn_last_data').prop('disabled', false)
                            $('#error-msg').html(`
                            <p class="rounded p-4 font-medium text-white bg-green-500 my-4">${data.message}!</p>
                            `)
                            $('#btn_set_target_value').prop('disabled', true)
                            $('#btn_close').prop('disabled', true)
                        } else {
                            $('#error-msg').html(`
                            <p class="rounded p-4 font-medium text-white bg-red-500 my-4">${data.error}!</p>
                            `)
                        }
                        setTimeout(() => {
                            $('#error-msg').html(``);
                        }, 5000);
                    }
                })
            })
            $('#btn_last_data').click(function(e) {
                e.preventDefault()
                $.ajax({
                    url: `{{ url('api/calibration-last-value') }}`,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            $('#btn_close').prop('disabled', false)
                            $('#btn_set_target_value').prop('disabled', false)
                            $('#btn_last_data').prop('disabled', true)
                        }
                    }
                })
            })
            $('#btn_close').click(function(e) {
                e.preventDefault()
                $.ajax({
                    url: `{{ url('api/calibration-stop') }}`,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            window.location.href =
                                `{{ url('calibration/manual') }}`
                        }
                    }
                })
            })
        })
    </script>
@endsection
