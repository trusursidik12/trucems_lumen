@extends('layouts.theme')
@section('title',"Process {$mode} {$type} Calibration")
@section('css')
@endsection
@section('content')
<div class="px-6 py-3 bg-gray-200 rounded">
    <div class="flex justify-content-betwen items-center h-[88vh] bg-gray-300 px-4 py-3">
        <div class="w-1/2 border-r border-gray-400 block items-center" id="section-left">
            <p class="block font-semibold text-sm text-indigo-700">Realtime Value : </p>
            <span class="block ml-3" id="section-logs">
            </span>
            <p class="block font-semibold text-sm text-indigo-700 last-avg">Last AVG. Value :</p>
            <span class="block font-semibold text-sm text-gray-700 ml-3">{{ @$lastAvg->value ? $lastAvg->value : 0}} PPM</span>
            <div id="section-values">
                @foreach ($sensorValues as $value)
                <div class="flex justify-between items-center px-3 section-value" data-sensor-id="{{ $value->sensor_id }}">
                    <span class="text-xl sensor-name">{!! $value->sensor->name !!}</span>
                    <span class="text-8xl font-bold text-indigo-700 sensor-value">4</span>
                    <span class="text-xl sensor-unit">{{ $value->sensor->unit->name }}</span>
                </div>
                @endforeach
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
                    <p class="text-indigo-500 font-bold">Last AVG. Value : {{ @$lastAvg->value ? $lastAvg->value : 0 }} ppm</p>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function() {
        let internvalRealtime = setInterval(getRealtimeValue, 1000);

        function getRealtimeValue() {
            let random = Math.floor(Math.random() * 100)
            $.ajax({
                url: `{{ url('api/calibration/check-remaining')."/".strtolower($mode)."/".strtolower($type) }}?t=${random}`,
                type: 'get',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(data) {
                    let section = $('#section-values')
                    let sectionLogs = $('#section-logs')
                    if (data.success) {
                        if (data.remaining_time < 0) {
                            clearInterval(internvalRealtime)
                            $('#section-left').removeClass('block')
                            $('#section-left').addClass('hidden')
                            $('#section-right').removeClass('w-1/2')
                            $('#section-right').addClass('w-full')
                            $('#remaining').addClass('hidden')
                            $('#last-value').removeClass('hidden')
                            $.ajax({
                                url: `{{ url('api/calibration/update-calibration')."/".strtolower($mode)."/".strtolower($type) }}?t=${random}`,
                                type: 'get',
                                dataType: 'json',
                                success: function(data) {
                                    if (data.is_retry) {
                                        setTimeout(() => {
                                            $.ajax({
                                                url: `{{ url('api/calibration/update-time-calibration')."/".strtolower($mode)."/".strtolower($type) }}?t=${random}`,
                                                type: 'PATCH',
                                                dataType: 'json',
                                                success: function(data) {
                                                    if (data.success) {
                                                        console.log(data.message)
                                                        location.reload()
                                                    }
                                                }
                                            })
                                        }, 5000); //5 se

                                    } else {
                                        setTimeout(() => {
                                            window.location.href = `{{ url("calibration/".strtolower($mode)) }}`
                                        }, 5000); //5 sec
                                    }
                                }
                            })


                        }
                        let sensorValues = data.sensor_values
                        sensorValues.map(function(value) {
                            let div = section.find(`.section-value[data-sensor-id=${value.sensor_id}]`)
                            div.find('.sensor-value').html(`${value.value}`)
                            $('.last-value').html(`${value.value}`)
                        })
                        // Logs
                        let calibrationLogs = data.calibration_logs
                        let i = 2
                        let logs = []
                        let html = ``
                        calibrationLogs.map(function(value) {
                            logs[i] = ` <p class="block text-xs">${value.value} ${value.sensor.unit.name} - ${value.created_at}</p>`
                            i--
                        })
                        logs.map(function(element) {
                            html += element
                        })
                        sectionLogs.html(html)
                        $('#remaining').html(`${data.remaining_time} sec`)

                    }
                }
            })
            // setTimeout(getRealtimeValue, 1000);
        }

    })
</script>
@endsection