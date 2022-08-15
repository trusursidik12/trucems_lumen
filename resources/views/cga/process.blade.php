@extends('layouts.theme')
@section('title', 'CGA Process')
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
        <div class="flex justify-between space-x-3">
            <div class="w-full bg-gray-300 rounded-tl-3xl rounded-br-3xl">
                <div class="flex justify-between">
                    <button type="button" id="btn-close"
                        class="px-5 py-4 bg-red-500 rounded-tl-3xl rounded-br-3xl text-white disabled:bg-gray-500">
                        Close
                    </button>
                    <div>
                        <button id="btn-show-adjust-zero" type="button"
                            class="disabled:bg-gray-500 px-5 py-4 bg-indigo-700 text-white">
                            Adjust ZERO
                        </button>
                        <button id="btn-show-adjust-span" type="button"
                            class="disabled:bg-gray-500 px-5 py-4 bg-indigo-700 text-white">
                            Adjust SPAN
                        </button>
                        <button class="px-5 py-4 bg-indigo-700 disabled:bg-gray-500 text-white" id="timer">
                            <span id="second">0</span> sec
                        </button>
                        <button type="button" id="btn-switch"
                            class="px-5 py-4 bg-indigo-700 disabled:bg-gray-500 text-white">
                            mg/m<sup>3</sup>
                        </button>
                        <div class="flex justify-between mb-3">
                            <div id="zero-form" class="hidden flex-row space-x-3 items-center">
                                <form id="form_adjust_zero">
                                    <div class="text-red-500"></div>
                                    <input type="hidden" name="target_value" value="0">
                                    <button type="submit" id="btn-start-cga"
                                        class="disabled:bg-gray-500 px-5 py-4 bg-indigo-700 text-white">
                                        SET ZERO
                                    </button>
                                    <button type="button" id="btn-cancel-adjust-zero"
                                        class="disabled:bg-gray-500 px-5 py-4 bg-red-500 text-white">
                                        Close
                                    </button>
                                </form>
                            </div>
                            <div id="span-form" class="hidden flex-row space-x-3 items-center">
                                <form id="form_adjust_span">
                                    <div class="text-red-500"></div>
                                    <select name="sensor_id" id="sensorid_value" class="px-5 py-4 rounded w-1/8">
                                        <option value="">SELECT</option>
                                        @foreach ($sensorValues as $list)
                                            @if ($list->sensor_id != 4)
                                                <option value="{{ $list->sensor_id }}">{!! $list->sensor->name !!}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <input type="text" name="target_value" id="targetvalue_value" value=""
                                        data-kioskboard-type="keyboard" data-kioskboard-specialcharacters="false"
                                        data-kioskboard-key-capslock="false" class="js-virtual-keyboard px-5 py-4 rounded"
                                        placeholder="Target Value">
                                    <button type="submit" id="btn-start-blowback"
                                        class="disabled:bg-gray-500 px-5 py-4 bg-indigo-700 text-white">
                                        SET SPAN
                                    </button>
                                    <button type="button" id="btn-cancel-adjust-span"
                                        class="disabled:bg-gray-500 px-5 py-4 bg-red-500 text-white">
                                        Close
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="error-msg"></div>
                <div id="section-values" class="px-3 py-2 flex flex-col space-y-2">
                    @foreach ($sensorValues as $value)
                        <div class="bg-gray-400 h-[{{ $count == 1 ? 20 : ($count == 2 ? 12 : 7) }}rem] flex justify-between items-start"
                            data-id="{{ $value->sensor_id }}">
                            <input type="hidden" name="sensor_id" class="sensor_id" value="{{ $value->sensor_id }}">
                            <div
                                class="section-sensor-name transition duration-500 bg-gray-600 text-white h-full w-[9rem] flex flex-col items-center justify-center">
                                <span class="text-2xl font-bold sensor-name">{!! $value->sensor->name !!}</span>
                            </div>
                            <div
                                class="section-sensor-value transition duration-500 bg-gray-500 text-white flex flex-1 flex-col h-full justify-center items-center">
                                <span class="text-5xl font-bold sensor-value">
                                    <span>{{ $value->value }}</span>
                                </span>
                            </div>
                            <div
                                class="section-sensor-unit transition duration-500 bg-gray-400 flex w-[5rem] flex-col h-full justify-between items-center">
                                <span class="mt-3 text-xl sensor-unit">{{ $value->sensor->unit->name }}</span>
                                <button data-id="{{ $value->sensor_id }}" data-isClicked="false"
                                    class="w-full btn-highlight px-2 py-3 text-sm bg-indigo-700 disabled:bg-gray-500 text-white">Highlight
                                    </buttond>
                            </div>
                        </div>
                    @endforeach
                </div>
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
            $('#btn-show-adjust-zero').click(function() {
                $('#btn-show-adjust-span').addClass('hidden')
                $('#btn-switch').addClass('hidden')
                $('#timer').addClass('hidden')
                $(this).addClass('hidden')
                $('#zero-form').removeClass('hidden').addClass('flex')
            })
            $('#btn-cancel-adjust-zero').click(function() {
                $('#btn-show-adjust-span').removeClass('hidden')
                $('#btn-show-adjust-zero').removeClass('hidden')
                $('#btn-switch').removeClass('hidden')
                $('#timer').removeClass('hidden')
                $('#zero-form').addClass('hidden').removeClass('flex')
            })

            $('#btn-show-adjust-span').click(function() {
                $('#btn-show-adjust-zero').addClass('hidden')
                $('#btn-switch').addClass('hidden')
                $('#timer').addClass('hidden')
                $(this).addClass('hidden')
                $('#span-form').removeClass('hidden').addClass('flex')
            })
            $('#btn-cancel-adjust-span').click(function() {
                $('#btn-show-adjust-span').removeClass('hidden')
                $('#btn-show-adjust-zero').removeClass('hidden')
                $('#btn-switch').removeClass('hidden')
                $('#timer').removeClass('hidden')
                $('#sensorid_value').val('')
                $('#targetvalue_value').val('')
                $('#span-form').addClass('hidden').removeClass('flex')
            })

            $('#form_adjust_zero').submit(function(e) {
                e.preventDefault()
                $.ajax({
                    url: `{{ url('api/adjust-set-value-zero') }}`,
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

            $('#form_adjust_span').submit(function(e) {
                e.preventDefault()
                $.ajax({
                    url: `{{ url('api/adjust-set-value-span') }}`,
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
                                    concentrate = eval(value.sensor.unit_formula)
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
            var seconds = 00;
            var tens = 00;

            function startTimer() {
                tens++;
                if (tens > 99) {
                    seconds++;
                    $('#second').html(`0${seconds}`)
                    tens = 0;
                }

                if (seconds > 9) {
                    $('#second').html(seconds)
                }
                setTimeout(startTimer, 10);
            }
            startTimer()
        })
    </script>
    <script>
        $(document).ready(function() {
            $(".btn-highlight").click(function() {
                if ($(this).data("isClicked") == "false" || $(this).data("isClicked") == undefined) {
                    $(this).html("Cancel")
                    $(this).data("isClicked", "true")
                    $(this).removeClass(['bg-indigo-700']).addClass('bg-gray-700')
                    let sensorId = $(this).data('id')
                    let section = $(`div[data-id="${sensorId}"]`)
                    let sectionName = section.find('.section-sensor-name')
                    let sectionValue = section.find('.section-sensor-value')
                    let sectionUnit = section.find('.section-sensor-unit')
                    sectionName.removeClass(['bg-gray-600']).addClass(['bg-indigo-600'])
                    sectionValue.removeClass(['bg-gray-500']).addClass(['bg-indigo-500'])
                    sectionUnit.removeClass(['bg-gray-400']).addClass(['bg-indigo-400'])
                } else {
                    $(this).html("Highlight")
                    $(this).data("isClicked", "false")
                    $(this).addClass(['bg-indigo-700']).removeClass('bg-gray-700')
                    let sensorId = $(this).data('id')
                    let section = $(`div[data-id="${sensorId}"]`)
                    let sectionName = section.find('.section-sensor-name')
                    let sectionValue = section.find('.section-sensor-value')
                    let sectionUnit = section.find('.section-sensor-unit')
                    sectionName.addClass(['bg-gray-600']).removeClass(['bg-indigo-600'])
                    sectionValue.addClass(['bg-gray-500']).removeClass(['bg-indigo-500'])
                    sectionUnit.addClass(['bg-gray-400']).removeClass(['bg-indigo-400'])
                }

            })
        })
    </script>
    <script>
        $(document).ready(function() {
            $('#btn-close').click(function() {
                $(this).html('Loading...')
                $('button').prop('disabled', true)
                $.ajax({
                    url: `{{ url('api/cga/finish') }}`,
                    type: 'PATCH',
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function(data) {
                        if (data.success) {
                            setTimeout(() => {
                                window.location.href =
                                    `{{ url('calibration/manual') }}`
                            }, 5000);
                        } else {
                            $(this).html('Close')
                            $('button').prop('disabled', false)
                        }
                    }
                })
            })
        })
    </script>
@endsection
