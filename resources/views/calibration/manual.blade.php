<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ url('css/app.css') }}">
</head>
<body>
    <div class="max-w-3xl mx-auto min-h-full">
        <div class="px-6 py-3 bg-gray-200 rounded">
            <div class="flex justify-start mb-3">
                <a href="{{ url("/") }}" role="button" class="px-4 py-2 bg-gray-500 text-white">
                    Back
                </a>
            </div>
            <form action="">
                <div class="flex justify-between space-x-3">
                    <div class="w-1/2 px-6 py-3 border-r-2 border-gray-300">
                        <div class="flex my-2 justify-between items-center">
                            <span class="w-2/2">
                                <span class="uppercase font-semibold">Default Zero Loop</span>
                            </span>
                            <span class="w-1/3">
                                <input type="text" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="1" class="js-virtual-keyboard px-3 py-1 outline-none w-full">
                            </span>
                        </div>
                        <div class="flex my-2 justify-between items-center">
                            <span class="w-2/2">
                                <span class="uppercase font-semibold">Time Zero Loop <small class="font-thin text-xs lowercase">(sec)</small></span>
                            </span>
                            <span class="w-1/3">
                                <input type="text" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="200" class="js-virtual-keyboard px-3 py-1 outline-none w-full">
                            </span>
                        </div>
                    </div>
                    <div class="w-1/2 px-6 py-3">
                        <div class="flex my-2 justify-between items-center">
                            <span class="w-2/2">
                                <span class="uppercase font-semibold">Default Span Loop</span>
                            </span>
                            <span class="w-1/3">
                                <input type="text" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="1" class="js-virtual-keyboard px-3 py-1 outline-none w-full">
                            </span>
                        </div>
                        <div class="flex my-2 justify-between items-center">
                            <span class="w-2/2">
                                <span class="uppercase font-semibold">Time Span Loop <small class="font-thin text-xs lowercase">(sec)</small></span>
                            </span>
                            <span class="w-1/3">
                                <input type="text" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="200" class="js-virtual-keyboard px-3 py-1 outline-none w-full">
                            </span>
                        </div>
                        <div class="flex my-2 justify-between items-center">
                            <span class="w-2/2">
                                <span class="uppercase font-semibold">Max Span PPM</span>
                            </span>
                            <span class="w-1/3">
                                <input type="text" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="200" class="js-virtual-keyboard px-3 py-1 outline-none w-full">
                            </span>
                        </div>
                        <button class="w-full px-3 py-2 bg-indigo-500 text-white">Start Manual Calibration</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script src="{{ url("js/jquery.min.js") }}"></script>
<script src="{{ url("js/kioskboard/kioskboard-aio-2.2.0.min.js")  }}"></script>
<script>
   $(document).ready(function(){
       KioskBoard.init({
            keysArrayOfObjects: [
                    {
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
            theme: 'flat',
            // Uppercase or lowercase to start. Uppercased when "true"
            capsLockActive: true,

            /*
            * Allow or prevent real/physical keyboard usage. Prevented when "false"
            * In addition, the "allowMobileKeyboard" option must be "true" as well, if the real/physical keyboard has wanted to be used.
            */
            allowRealKeyboard: true,

            // Allow or prevent mobile keyboard usage. Prevented when "true"
            allowMobileKeyboard: true,

            // CSS animations for opening or closing the keyboard
            cssAnimations: false,

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
            keysFontSize: '22px',

            // Font weight of the keys
            keysFontWeight: 'normal',

            // Size of the icon keys
            keysIconSize: '25px',

            // Scrolls the document to the top or bottom(by the placement option) of the input/textarea element. Prevented when "false"
            autoScroll: true,
            })
        KioskBoard.run('.js-virtual-keyboard',{
            
        })
   })
</script>
</body>
</html>