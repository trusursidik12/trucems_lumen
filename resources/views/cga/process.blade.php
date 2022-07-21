@extends('layouts.theme')
@section('title', 'CGA Process')
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">
        <div>
            <p class="px-3 py-2 bg-red-500 text-white rounded hidden" id="error-msg">

            </p>
        </div>
        <div class="flex justify-between space-x-3">
            <div class="w-full bg-gray-300 rounded-tl-3xl rounded-br-3xl">
                <div class="flex justify-between">
                    <button type="button" id="btn-close" class="px-5 py-4 bg-red-500 rounded-tl-3xl rounded-br-3xl text-white">
                        Close
                    </button>
                   <div>
                        <button class="px-5 py-4 bg-indigo-700 text-white" id="timer">
                            <span id="second">0</span> sec
                        </button>
                        <button type="button" id="btn-switch" class="px-5 py-4 bg-indigo-700 text-white">
                            mg/m<sup>3</sup>
                        </button>
                   </div>
                </div>
                <div id="section-values" class="px-3 py-2 flex flex-col space-y-2">
                    @foreach ($sensorValues as $value)
                        <div class="bg-gray-400 h-[{{ $count == 1 ? 20 : ($count == 2 ? 12 : 7)}}rem] flex justify-between items-start" data-id="{{ $value->sensor_id }}">
                            <input type="hidden" name="sensor_id" class="sensor_id" value="{{ $value->sensor_id }}">
                            <div class="section-sensor-name transition duration-500 bg-gray-600 text-white h-full w-[5rem] flex flex-col items-center justify-center">
                                <span class="text-2xl font-bold sensor-name">{!! $value->sensor->name !!}</span>
                            </div>
                            <div class="section-sensor-value transition duration-500 bg-gray-500 text-white flex flex-1 flex-col h-full justify-center items-center">
                                <span class="text-5xl font-bold sensor-value">
                                    <span>{{ $value->value }}</span>
                                </span>
                            </div>
                            <div class="section-sensor-unit transition duration-500 bg-gray-400 flex w-[5rem] flex-col h-full justify-between items-center">
                                <span class="mt-3 text-xl sensor-unit">{{ $value->sensor->unit->name }}</span>
                                <button data-id="{{ $value->sensor_id }}" data-isClicked="false" class="w-full btn-highlight px-2 py-3 text-sm bg-indigo-700 text-white">Highlight</buttond>
                            </div>
                        </div>
                    @endforeach
                </div>
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
            var seconds = 00; 
            var tens = 00; 
            function startTimer () {
                tens++; 
                if (tens > 99) {
                    seconds++;
                    $('#second').html(`0${seconds}`)
                    tens = 0;
                }
                
                if (seconds > 9){
                    $('#second').html(seconds)
                }
                setTimeout(startTimer, 10);
            }
            startTimer()
        })
    </script>
    <script>
        $(document).ready(function(){
            $(".btn-highlight").click(function(){
                if($(this).data("isClicked") == "false" || $(this).data("isClicked") == undefined){
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
                }else{
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
        $(document).ready(function(){
            $('#btn-close').click(function(){
                $.ajax({
                    url : `{{ url('api/cga/finish') }}`,
                    type : 'PATCH',
                    dataType : 'json',
                    data : $(this).serialize(),
                    success : function(data){
                        if(data.success){
                            window.location.href=`{{ url('calibration/manual') }}`
                        }
                    }
                })
            })
        })
    </script>
@endsection
