@extends('layouts.theme')
@section('title', 'Manual Calibration')
@section('css')
    <link rel="stylesheet" href="{{ url('js/kioskboard/kioskboard-2.2.0.min.css') }}">
    <link rel="stylesheet" href="{{ url('sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">
        <div class="flex justify-between mb-3">
            <a href="{{ url('/') }}" role="button" class="rounded px-4 py-2 bg-gray-500 text-white">
                Back
            </a>
            <div id="blowback-form" class="hidden flex-row space-x-3 items-center">
                <div class="text-red-500"></div>
                <p class="text-gray-700" id="remaining"></p>
                <input type="text" required value="5" name="blowback_duration" autocomplete="false"
                    data-kioskboard-type="numpad" data-kioskboard-placement="bottom" placeholder="Duration (sec)"
                    class="js-virtual-keyboard px-3 py-2 bg-white rounded w-[8rem]
            focus:outline-slate-100">
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
                    <button onclick="return window.location.href='{{ url('calibration/manual/zero/process') }}'"
                        type="button"
                        class="btn-start disabled:bg-gray-500  rounded w-full py-4 h-56 text-xl font-bold bg-indigo-500 text-white">
                        Zero Calibration
                    </button>
                </div>
                <div class="w-1/2 px-6 py-3">
                    <button data-type="span" type="button"
                        class="btn-start disabled:bg-gray-500  rounded w-full py-4 h-56 text-xl font-bold bg-indigo-500 text-white">
                        SPAN Calibration
                    </button>
                </div>
            </div>
        </form>
        <div id="keyboard"></div>
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

            var intervalRemaining;
            $('#btn-start-blowback').click(function() {
                $('#remaining').removeClass('text-red-500').addClass('text-gray-700')
                let duration = $('input[name=blowback_duration]').val()
                if (duration == null || duration == undefined || duration == "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Duration cant be empty!!',
                    })
                    return;
                }
                $(this).html(`Loading...`)
                $('button').prop('disabled', true)
                $('a').attr('href', 'javascript:void(0)')
                $.ajax({
                    url: `{{ url('api/blowback') }}`,
                    type: 'PATCH',
                    dataType: 'json',
                    data: `blowback_duration=${duration}`,
                    success: function(data) {
                        if (data.success) {
                            intervalRemaining = setInterval(remainingBlowback, 1000);
                        }
                    }
                })
            })
            // Function
            function remainingBlowback() {
                $.ajax({
                    url: `{{ url('api/blowback') }}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            let sec = data.remaining_time
                            // console.log(data.is_relay_open)
                            if (data.is_relay_open == 0) {
                                $('#remaining').html(`Failed to Blow back`)
                                $('#remaining').removeClass('text-gray-700').addClass('text-red-500')
                                $('#btn-start-blowback').html('Start Blow Back')
                                $('button').prop('disabled', false)
                                $('a').attr('href', '{{ url('/?ref=calibration') }}')
                                clearInterval(intervalRemaining)
                            } else if (sec <= 0) {
                                clearInterval(intervalRemaining)
                                $.ajax({
                                    url: `{{ url('api/blowback/finish') }}`,
                                    type: 'PATCH',
                                    dataType: 'json',
                                    data: $(this).serialize(),
                                    success: function(data) {
                                        if (data.success) {
                                            $('#remaining').html(``)
                                            $('#btn-start-blowback').html('Start Blow Back')
                                            $('button').prop('disabled', false)
                                            $('a').attr('href',
                                                '{{ url('/?ref=calibration') }}')
                                        }
                                    }
                                })
                            } else {
                                $('#remaining').html(`Remaining : ${sec} sec`)
                            }
                        }
                    }
                })
            }
        })
    </script>
    <script>
        $(document).ready(function() {

        })
    </script>
@endsection
