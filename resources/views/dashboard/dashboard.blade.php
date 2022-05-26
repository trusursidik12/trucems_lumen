@extends('layouts.theme')
@section('title','Dashboard')
@section('content')
<div class="px-6 py-3 bg-gray-200 rounded">
    <div class="flex justify-start mb-3">
        <p>10:10:10</p>
    </div>
    <div class="flex justify-between pt-[14vh] space-x-3">
        <div class="w-2/3 px-6 py-3 bg-gray-300">
            <div class="flex justify-end">
                <button type="button" id="btn-switch" class="px-4 py-2 bg-indigo-500 text-white">
                    m/g
                </button>
            </div>
            <div id="section-values">
                @foreach ($sensorValues as $value)
                <div class="flex justify-between items-center space-x-3">
                    <input type="hidden" name="sensor_id" class="sensor_id" value="{{ $value->sensor_id }}">
                    <span class="text-2xl sensor-name">{!! $value->sensor->name !!}</span>
                    <span class="text-8xl font-bold sensor-value">{{ $value->value }}</span>
                    <span class="text-2xl sensor-unit">{{ $value->sensor->unit->name }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div class="w-1/3">
            <nav class="sidebar flex flex-col space-y-3">
                <a href="{{ url("calibration/auto") }}">Auto CAL</a>
                <a href="{{ url("calibration/manual") }}">Manual CAL</a>
                <a href="{{ url("calibration/logs") }}">Calibration Logs</a>
                <a href="{{ url("settings") }}">Setting</a>
                <a href="{{ url("quality-standards") }}">Baku Mutu</a>
            </nav>
        </div>
    </div>
</div>    
@endsection
@section('js')
<script>
    $(document).ready(function(){
        if(localStorage.getItem("unit") === undefined){
            localStorage.setItem("unit","ppm")
        }
        $('#btn-switch').click(function(){
            let unit = localStorage.getItem("unit")
            if(unit === "ppm"){
                $('.sensor-unit').html("m/g")
                localStorage.setItem("unit","m/g")
                $(this).html(unit)
            }else{
                $('.sensor-unit').html("ppm")
                localStorage.setItem("unit","ppm")
                $(this).html(unit)
            }
        })
        function getValues(){
            let random = Math.floor(Math.random() * 100)
            $.ajax({
                url : `{{ url('api/sensor-value-logs') }}?t=${random}`,
                type : 'get',
                dataType : 'json',
                data : $(this).serialize(),
                success : function(data){
                    let concentrate = 0
                    // 0.0409 * konsentrasi * 34.08
                    let unit = localStorage.getItem("unit")
                    let section = $('#section-values')
                    if(data.success){
                        let sensorValues = data.data
                        sensorValues.map(function(value){
                            if(unit === "m/g"){
                                concentrate = Math.round((0.0409 * value.value * 34.08) * 100) / 100
                                // Formula is (0.0409 * concentrate * 34.08)
                                // * 100 and / 100 is for rounding 2 decimal places
                            }else{
                                concentrate = value.value
                            }
                            let div = section.find(`.sensor_id[value=${value.sensor_id}]`).parent()
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
@endsection