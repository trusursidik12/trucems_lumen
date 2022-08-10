@extends('layouts.theme')
@section('title', "Process {$mode} {$type} Calibration")
@section('css')
    <link rel="stylesheet" href="{{ url('js/kioskboard/kioskboard-2.2.0.min.css') }}">
    <link rel="stylesheet" href="{{ url('sweetalert2/sweetalert2.min.css') }}">
    <style>
        #KioskBoard-VirtualKeyboard {
            height: 46vh;
        }
    </style>
@endsection
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">

        <div class="h-[88vh] bg-gray-300 rounded-tl-3xl rounded-br-3xl">
            <div class="flex justify-between">
                <button id="btn_close"
                    class="px-5 py-4 bg-red-500 rounded-tl-3xl rounded-br-3xl text-white disabled:bg-gray-500"{{ @$calibrationLog->result_value == null && !empty($calibrationLog) ? 'disabled' : '' }}>Close</button>
                <span class="bg-indigo-700 px-5 py-4"></span>
            </div>
            <div class="flex justify-content-betwen items-center px-4">
                <div class="w-1/2 border-r border-gray-400 block items-center" id="section-left">
                    <p class="block font-semibold text-sm text-indigo-700 last-avg">Current Value :</p>
                    <div id="section-values">
                        @if ($type == 'ZERO')
                            @foreach ($sensorValues as $value)
                                @if ($value->sensor_id != 4)
                                    <div class="flex justify-between items-center px-3 section-value border-b border-gray-400 py-2"
                                        data-sensor-id="{{ $value->sensor_id }}">
                                        <span class="text-xl sensor-name">{!! $value->sensor->name !!}</span>
                                        <span class="text-7xl font-bold text-indigo-700 sensor-value"></span>
                                        <span class="text-xl sensor-unit">{{ $value->sensor->unit->name }}</span>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="flex justify-between items-center px-3 section-value"
                                data-sensor-id="{{ $sensorValues->sensor_id }}">
                                <span class="text-xl sensor-name">{!! $sensorValues->sensor->name !!}</span>
                                <span class="text-8xl font-bold text-indigo-700 sensor-value"></span>
                                <span class="text-xl sensor-unit">{{ $sensorValues->sensor->unit->name }}</span>
                            </div>
                        @endif

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
            <div class="w-full px-3 mt-2">
                <div id="error-msg"></div>
                <form id="form" class="mx-auto max-w-screen-md text-center">
                    <input type="hidden" id="current_value" name="current_value" value="">
                    <input type="{{ $type == 'ZERO' ? 'hidden' : 'text' }}" name="target_value"
                        value="{{ $type == 'ZERO' ? 0 : '' }}" data-kioskboard-type="keyboard"
                        data-kioskboard-specialcharacters="false" data-kioskboard-key-capslock="false"
                        class="js-virtual-keyboard px-5 py-4 rounded w-1/2" placeholder="Target Value">
                    <button type="submit" id="btn_set_target_value"
                        class="px-20 py-4 bg-indigo-500 rounded text-white disabled:bg-gray-500">
                        Set Target</button>
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
                        "0": "0",
                        "1": "1",
                        "2": "2",
                        "3": "3",
                        "4": "4",
                    },
                    {
                        "0": "5",
                        "1": "6",
                        "2": "7",
                        "3": "8",
                        "4": "9",
                        "5": ".",
                    },
                ],
                keysSpecialCharsArrayOfStrings: ['.'],
                keysJsonUrl: `{{ url('js/kioskboard-keys-english.json') }}`,
                // Language Code (ISO 639-1) for custom keys (for language support) => e.g. "de" || "en" || "fr" || "hu" || "tr" etc...
                language: 'en',
                // The theme of keyboard => "light" || "dark" || "flat" || "material" || "oldschool"
                theme: 'oldschool',
                // Uppercase or lowercase to start. Uppercased when "true"
                capsLockActive: false,

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
                keysAllowSpacebar: false,

                // Text of the space key (Spacebar). Without text => " "
                keysSpacebarText: 'Space',

                // Font family of the keys
                keysFontFamily: 'sans-serif',

                // Font size of the keys
                keysFontSize: '20px',

                // Font weight of the keys
                keysFontWeight: 'bold',

                // Size of the icon keys
                keysIconSize: '24px',

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
                    url: `{{ url('api/calibration/get-realtime-value/' . ($type == 'ZERO' ? 1 : 2)) }}?t=${random}`,
                    type: 'get',
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data) {
                        let section = $('#section-values')
                        let sectionLogs = $('#section-logs')
                        if (data.success) {
                            let sensorValues = data.sensor_values
                            sensorValues.map(function(value) {
                                let div = section.find(
                                    `.section-value[data-sensor-id=${value.sensor_id}]`)
                                div.find('.sensor-value').html(`${value.value}`)
                                $('#current_value').val(value.value)
                                // $('.last-value').html(`${value.value}`)
                            })
                        }
                    }
                })
            }
        })
    </script>
    <script>
        $(document).ready(function() {
            // Set Target Value On Calibration
            $('#form').submit(function(e) {
                e.preventDefault()
                $.ajax({
                    url: `{{ url('api/calibration-set-value') . '/' . ($type == 'ZERO' ? 1 : 2) }}`,
                    type: 'POST',
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data) {
                        if (data.success) {
                            $('#error-msg').html(`
                            <p class="rounded px-4 py-1 font-medium text-white bg-green-500 my-4">${data.message}!</p>
                            `)
                        } else {
                            $('#error-msg').html(`
                            <p class="rounded px-4 py-1 font-medium text-white bg-red-500 my-4">${data.error}!</p>
                            `)
                        }
                        setTimeout(() => {
                            $('#error-msg').html(``);
                        }, 5000);
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
