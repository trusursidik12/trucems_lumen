@extends('layouts.theme')
@section('title','Dashboard')
@section('content')
<div class="px-6 py-3 bg-gray-200 rounded">
    <div class="flex justify-start mb-3">
        <p id="runtime">10:10:10</p>
    </div>
    <div class="flex justify-between pt-[14vh] space-x-3">
        <div class="w-2/3 px-6 py-3 bg-gray-300 rounded">
            <div class="flex justify-end">
                <button type="button" id="btn-switch" class="rounded px-4 py-2 bg-indigo-500 text-white">
                    mg/m<sup>3</sup>
                </button>
            </div>
            <div id="section-values">
                @foreach ($sensorValues as $value)
                <div class="flex justify-between items-start space-x-3">
                    <input type="hidden" name="sensor_id" class="sensor_id" value="{{ $value->sensor_id }}">
                    <span class="text-2xl sensor-name">{!! $value->sensor->name !!}</span>
                    <span class="text-8xl font-bold sensor-value h-64 flex items-center">
                        <span>{{ $value->value }}</span>
                    </span>
                    <span class="text-2xl sensor-unit">{{ $value->sensor->unit->name }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div class="w-1/3">
            <nav class="sidebar grid grid-rows justify-center gap-3 h-full">
                {{-- <a href="{{ url("calibration/auto") }}">Auto CAL</a> --}}
                <a href="{{ url("calibration/manual") }}">Calibration</a>
                <a href="{{ url("calibration/logs") }}">Calibration Logs</a>
                {{-- <a href="{{ url("settings") }}">Setting</a> --}}
                <a href="{{ url("quality-standards") }}">Baku Mutu</a>
            </nav>
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
                    localStorage.setItem("unit", "mg/m<sup>3</sup>")
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
                            if (unit === "m/g") {
                                concentrate = Math.round((0.0409 * value.value * 34.08) * 1000) / 1000
                                // Formula is (0.0409 * concentrate * 34.08)
                                // * 1000 and / 1000 is for rounding 3 decimal places
                            } else {
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
<script>
    $(document).ready(function(){
        function loadRuntime(){
            $.ajax({
                url : `{{ url('api/runtime') }}`,
                type : 'get',
                dataType : 'json',
                data : $(this).serialize(),
                success : function(data){
                    if(data.success){
                        let runtime = data.data
                        $('#runtime').html(`${runtime.days}:${runtime.hours}:${runtime.minutes}`)
                    }
                }
            })
            setTimeout(loadRuntime, (1000*60)); //load every  mins
        }
        loadRuntime()
    })
</script>
@endsection