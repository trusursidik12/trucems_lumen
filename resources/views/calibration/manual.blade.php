@extends('layouts.theme')
@section('title','Manual Calibration')
@section('css')
<link rel="stylesheet" href="{{ url("js/kioskboard/kioskboard-2.2.0.min.css") }}">
@endsection
@section('content')
<div class="px-6 py-3 bg-gray-200 rounded">
    <div class="flex justify-between mb-3">
        <a href="{{ url("/") }}" role="button" class="px-4 py-2 bg-gray-500 text-white">
            Back
        </a>
    </div>
    <div id="error-msg">
       
    </div>
    <form action="" class="bg-gray-300 h-[83vh]" id="form">
        <div class="flex justify-between space-x-3 items-center pt-[13vh]" id="section-form">
            <div class="w-1/2 px-6 py-3 border-r-2 border-gray-400">
                <div class="flex my-2 justify-between items-center">
                    <span class="w-2/2">
                        <span class="uppercase font-semibold text-2xl">Default Zero Loop</span>
                    </span>
                    <span class="w-1/3">
                        <input type="text" name="m_default_zero_loop" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="{{ $config->m_default_zero_loop }}" class="js-virtual-keyboard px-3 py-2 h-14 text-2xl outline-none w-full">
                    </span>
                </div>
                <div class="flex my-2 justify-between items-center">
                    <span class="w-2/2">
                        <span class="uppercase font-semibold text-2xl">Time Zero Loop <small class="font-thin text-xs lowercase">(sec)</small></span>
                    </span>
                    <span class="w-1/3">
                        <input type="text" name="m_time_zero_loop" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="{{ $config->m_time_zero_loop }}" class="js-virtual-keyboard px-3 py-2 h-14 text-2xl outline-none w-full">
                    </span>
                </div>
                {{-- Margin --}}
                <div class="invisible flex my-2 justify-between items-center">
                    <span class="w-2/2">
                        <span class="uppercase font-semibold text-2xl">Max Zero PPM</span>
                    </span>
                    <span class="w-1/3">
                        <input type="text" class="px-3 py-2 h-14 text-2xl outline-none w-full">
                    </span>
                </div>
                {{-- End Margin --}}
                <button data-type="zero" type="button" class="btn-start w-full py-4 text-xl font-bold bg-indigo-500 text-white">Start Zero Manual Calibration</button>
           
            </div>
            <div class="w-1/2 px-6 py-3">
                <div class="flex my-2 justify-between items-center">
                    <span class="w-2/2">
                        <span class="uppercase font-semibold text-2xl">Default Span Loop</span>
                    </span>
                    <span class="w-1/3">
                        <input type="text" name="m_default_span_loop" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="{{ $config->m_default_span_loop }}" class="js-virtual-keyboard px-3 py-2 h-14 text-2xl outline-none w-full">
                    </span>
                </div>
                <div class="flex my-2 justify-between items-center">
                    <span class="w-2/2">
                        <span class="uppercase font-semibold text-2xl">Time Span Loop <small class="font-thin text-xs lowercase">(sec)</small></span>
                    </span>
                    <span class="w-1/3">
                        <input type="text" name="m_time_span_loop" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="{{ $config->m_time_span_loop }}" class="js-virtual-keyboard px-3 py-2 h-14 text-2xl outline-none w-full">
                    </span>
                </div>
                <div class="flex my-2 justify-between items-center">
                    <span class="w-2/2">
                        <span class="uppercase font-semibold text-2xl">Max Span PPM</span>
                    </span>
                    <span class="w-1/3">
                        <input type="text" name="m_max_span_ppm" data-kioskboard-type="numpad" data-kioskboard-placement="bottom" value="{{ $config->m_max_span_ppm }}" class="js-virtual-keyboard px-3 py-2 h-14 text-2xl outline-none w-full">
                    </span>
                </div>
                <button data-type="span" type="button" class="btn-start w-full py-4 text-xl font-bold bg-indigo-500 text-white">Start Span Manual Calibration</button>
            </div>
        </div>
    </form>
    <div id="keyboard"></div>
</div>
@endsection
@section('js')
<script src="{{ url("js/kioskboard/kioskboard-2.2.0.min.js")  }}"></script>
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
         KioskBoard.run('.js-virtual-keyboard',{
             
         })
    })
 </script>

 <script>
     $(document).ready(function(){
         $('.btn-start').click(function(){
             let type = $(this).data('type');
             $.ajax({
                 url : `{{ url("api/set-calibration/manual") }}/${type}`,
                 type : 'PATCH',
                 dataType : 'json',
                 data : $('#form').serialize(),
                 success : function(data){
                     if(data.success){
                         return window.location.href = `{{ url('calibration/manual/') }}/${type}/process`
                     }else{
                        let html = ``;
                        Object.keys(data.errors).map(function(index){
                            let errors = data.errors[index]
                            errors.map(function(error){
                                html+=` <p class="rounded p-2 bg-red-500 text-white mb-2">${error}</p>`
                            })
                        })
                        $('#error-msg').html(html)
                     }
                 },
                 error : function(xhr, status, err){
                     
                 }
             })
         })
     })
 </script>
@endsection