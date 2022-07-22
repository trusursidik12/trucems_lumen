@extends('layouts.theme')
@section('title', 'Configuration')
@section('css')
    <link rel="stylesheet" href="{{ url('js/kioskboard/kioskboard-2.2.0.min.css') }}">
    <link rel="stylesheet" href="{{ url('sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">
        
        <form id="configuration-form" action="{{ url('configurations') }}" method="PATCH"
            class="bg-gray-300 h-[83vh] rounded-tl-3xl rounded-br-3xl" id="form">
           <div class="flex justify-between">
                <a href="{{ url('/') }}" role="button" class="rounded-tl-3xl rounded-br-3xl px-5 py-4 bg-red-500 text-white">
                    Back
                </a>
                <a href="{{ url("sensors") }}" class="bg-indigo-700 px-5 py-4 text-white">
                    Sensors &rarr;
                </a>
           </div>
            <div id="error-msg" class="px-4">
            </div>
            <div class="flex justify-between space-x-3 items-start pt-[6vh]" id="section-form">
                <div class="w-1/2 px-6 py-3">
                    <div class="flex my-2 justify-between items-center">
                        <span class="w-2/2">
                            <span class="uppercase font-semibold text-2xl">Time Sampling <small
                                    class="font-thin text-xs lowercase">(sec)</small></span>
                        </span>
                        <span class="w-1/3">
                            <input type="number" required min="1" name="sleep_sampling"
                                data-kioskboard-type="numpad" data-kioskboard-placement="bottom"
                                value="{{ $plc->sleep_sampling }}"
                                class="js-virtual-keyboard rounded px-3 py-2 h-14 text-2xl outline-none w-full">
                        </span>
                    </div>
                    <div class="flex my-2 justify-between items-center">
                        <span class="w-2/2">
                            <span class="uppercase font-semibold text-2xl">Sampling Loop</span>
                        </span>
                        <span class="w-1/3">
                            <input type="number" required min="1" name="loop_sampling" data-kioskboard-type="numpad"
                                data-kioskboard-placement="bottom" value="{{ $plc->loop_sampling }}"
                                class="js-virtual-keyboard rounded px-3 py-2 h-14 text-2xl outline-none w-full">
                        </span>
                    </div>
                </div>
                <div class="w-1/2 px-6 py-3 border-l-2 border-gray-400">
                    <div class="flex my-2 justify-between items-center">
                        <span class="w-2/2">
                            <span class="uppercase font-semibold text-2xl">Time Blowback <small
                                    class="font-thin text-xs lowercase">(sec)</small></span>
                        </span>
                        <span class="w-1/3">
                            <input type="number" required min="1" name="sleep_blowback"
                                data-kioskboard-type="numpad" data-kioskboard-placement="bottom"
                                value="{{ $plc->sleep_blowback }}"
                                class="js-virtual-keyboard rounded px-3 py-2 h-14 text-2xl outline-none w-full">
                        </span>
                    </div>
                    <div class="flex my-2 justify-between items-center">
                        <span class="w-2/2">
                            <span class="uppercase font-semibold text-2xl">Blowback Loop</span>
                        </span>
                        <span class="w-1/3">
                            <input type="number" required min="1" name="loop_blowback" data-kioskboard-type="numpad"
                                data-kioskboard-placement="bottom" value="{{ $plc->loop_blowback }}"
                                class="js-virtual-keyboard rounded px-3 py-2 h-14 text-2xl outline-none w-full">
                        </span>
                    </div>
                    <div class="flex my-2 justify-between items-center">
                        <span class="w-2/2">
                            <span class="uppercase font-semibold text-2xl">Sleep Default <small
                                    class="font-thin text-xs lowercase">(sec)</small></span>
                        </span>
                        <span class="w-1/3">
                            <input type="number" required min="1" name="sleep_default"
                                data-kioskboard-type="numpad" data-kioskboard-placement="bottom"
                                value="{{ $plc->sleep_default }}"
                                class="js-virtual-keyboard rounded px-3 py-2 h-14 text-2xl outline-none w-full">
                        </span>
                    </div>
                </div>

            </div>
            <div class="px-5">
                <button type="submit"
                    class="btn-start disabled:bg-gray-500 mt-5 mx-auto  rounded w-full py-4 text-xl font-bold bg-indigo-500 text-white">
                    Save Changes
                </button>
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
        $(document).ready(function() {
            $('#configuration-form').submit(function(e) {
                e.preventDefault()
                let buttonSubmit = $(this).find('button')
                $('button').prop('disabled', true)
                buttonSubmit.html(`Saving...`)
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(data) {
                        setTimeout(() => {
                            $('button').prop('disabled', false)
                            buttonSubmit.html(`Save Changes`)
                            if (data.success) {
                                $('#error-msg').html(`
                                    <p class="rounded px-4 py-1 font-medium text-white bg-green-500 my-4">${data.message}!</p>
                                `)
                            } else {
                                $('#error-msg').html(`
                                    <p class="rounded px-4 py-1 font-medium text-white bg-red-500 my-4">${data.message}!</p>
                                `)
                            }
                            setTimeout(() => {
                                $('#error-msg').html('')
                            }, 3000);
                        }, 1000);
                    },
                    error: function(xhr, data, type) {
                        $('button').prop('disabled', false)
                        buttonSubmit.html(`Save Changes`)
                        $('#error-msg').html(`
                            <p class="rounded p-4 font-medium text-white bg-red-500 my-4">Error while saving data!</p>
                        `)
                    }
                })
            })
        })
    </script>
@endsection
