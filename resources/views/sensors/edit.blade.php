@extends('layouts.theme')
@section('title', 'Configuration')
@section('css')
    <link rel="stylesheet" href="{{ url('js/kioskboard/kioskboard-2.2.0.min.css') }}">
    <style>
        #KioskBoard-VirtualKeyboard {
            height: 55vh;
        }
    </style>
@endsection
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">
        <div class="bg-gray-300 h-[83vh] rounded-tl-3xl rounded-br-3xl">
            <div class="flex justify-between">
                <a href="{{ url('/sensors') }}" role="button" class="rounded-tl-3xl rounded-br-3xl px-5 py-4 bg-red-500 text-white">
                    Back
                </a>
                <div class="py-4">
                    <span class="bg-indigo-700 py-4 px-3 text-white">
                    </span>
                </div>
           </div>
            <div id="response-message" class="px-3">
            </div>
            <form id="form-edit" action="{{ url("sensor/update/{$sensor->id}") }}" method="PATCH" class="p-5 flex justify-between">
                <div class="w-1/2 border-r-2 mr-2 pr-2">
                    <div class="flex my-2 justify-between items-center">
                        <span class="w-1/3">
                            <span class="uppercase font-semibold text-2xl">Name</span>
                        </span>
                        <span class="w-2/3">
                            <input type="text" required name="name"
                                data-kioskboard-type="keyboard" data-kioskboard-specialcharacters="true" data-kioskboard-placement="bottom"
                                value="{{ $sensor->name }}"
                                class="js-virtual-keyboard rounded px-3 py-2 h-14 text-2xl outline-none w-full">
                        </span>
                    </div>
                    <div class="flex my-2 justify-between items-center">
                        <span class="w-1/3">
                            <span class="uppercase font-semibold text-2xl">Code</span>
                        </span>
                        <span class="w-2/3">
                            <input type="text" required name="code"
                                data-kioskboard-type="keyboard" data-kioskboard-specialcharacters="true" data-kioskboard-placement="bottom"
                                value="{{ $sensor->code }}"
                                class="js-virtual-keyboard rounded px-3 py-2 h-14 text-2xl outline-none w-full">
                        </span>
                    </div>
                    <div class="flex my-2 justify-between items-center">
                        <span class="w-1/3">
                            <span class="uppercase font-semibold text-2xl">Baku Mutu</span>
                            <span class="text-sxs">(mg)</span>
                        </span>
                        <span class="w-2/3">
                            <input type="number" required name="quality_standard"
                                data-kioskboard-type="numpad" data-kioskboard-placement="bottom"
                                value="{{ $sensor->quality_standard }}"
                                class="js-virtual-keyboard rounded px-3 py-2 h-14 text-2xl outline-none w-full">
                        </span>
                    </div>
                </div>
                <div class="w-1/2">
                    <div class="flex my-2 justify-between items-center">
                        <span class="w-1/3">
                            <span class="uppercase font-semibold text-xl">Read Formula</span>
                        </span>
                        <span class="w-2/3">
                            <input type="text" required name="read_formula"
                                data-kioskboard-type="keyboard" data-kioskboard-specialcharacters="true" data-kioskboard-placement="bottom"
                                value="{{ $sensor->read_formula }}"
                                class="js-virtual-keyboard rounded px-3 py-2 h-14 text-2xl outline-none w-full">
                        </span>
                    </div>
                    <div class="flex my-2 justify-between items-center">
                        <span class="w-1/3">
                            <span class="uppercase font-semibold text-xl">Write Formula</span>
                        </span>
                        <span class="w-2/3">
                            <input type="text" required name="write_formula"
                                data-kioskboard-type="keyboard" data-kioskboard-specialcharacters="true" data-kioskboard-placement="bottom"
                                value="{{ $sensor->write_formula }}"
                                class="js-virtual-keyboard rounded px-3 py-2 h-14 text-2xl outline-none w-full">
                        </span>
                    </div>
                    <button type="submit" class="px-5 py-4 w-full bg-indigo-700 text-white text-bold"> Save Changes</button>
                </div>
            </form>
        </div>
        
    </div>
@endsection
@section('js')
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
        $(document).ready(function(){
            $('#form-edit').submit(function(e){
                e.preventDefault();
                $.ajax({
                    url : `${$(this).attr('action')}`,
                    type : `${$(this).attr('method')}`,
                    dataType : 'json',
                    data : $(this).serialize(),
                    success : function(data){
                        if(data.success){
                            $('#response-message').html(`
                            <p class="rounded px-4 py-1 font-medium text-white bg-green-500 my-4">${data.message}!</p>
                            `)
                        }else{
                            $('#response-message').html(`
                            <p class="rounded px-4 py-1 font-medium text-white bg-red-500 my-4">${data.message}!</p>
                            `)
                        }
                    }
                })
            })
        })
    </script>
@endsection
