@extends('layouts.theme')
@section('title','Quality Standards')
@section('content')
<div class="px-6 py-3 bg-gray-200 rounded">
    <div class="flex justify-between pt-[3vh] space-x-3 ">
        <div class="w-full rounded-tl-3xl rounded-br-3xl bg-gray-300 h-[69vh]">
            <div class="flex justify-between">
                <a href="{{ url('/') }}" role="button" class="rounded-tl-3xl rounded-br-3xl px-5 py-4 bg-red-500 text-white">
                    Back
                </a>
                <span class="bg-indigo-700 px-5 py-4 text-white">
                    Baku Mutu
                </span>

            </div>
            <div class="px-6 py-3">
                <h2 class="text-xl text-center"><span id="concentrate"></span></h2>
                <div class="flex justify-end">
                   <canvas id="chart" class="max-h-[60vh]"></canvas>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="{{ url('js/chart.js/chart.min.js') }}"></script>
<script src="{{ url('js/chart.js/chartjs-plugin-datalabels.min.js') }}"></script>
<script src="{{ url('js/chart.js/chartjs-plugin-annotation.min.js') }}"></script>
<script>
    $(document).ready(function() {
        const data = {
            labels: [
                'TRS (H2S)',
                ],
            datasets: [{
                barThickness : 68,
                label: 'Concentration mg/m³',
                backgroundColor: 'rgb(255, 99, 132)',
                borderColor: 'rgb(255, 99, 132)',
                data: [90,11,23,13,21],
                datalabels: {
                    anchor: 'end',
                    align: 'start',
                } 
            }]
        };
    
        const config = {
            type: 'bar',
            data: data,
            options: {
                plugins: {
                    autocolors: true,
                    annotation: {
                        annotations: {
                            @foreach ($sensors as $sensor)  
                            line{{ $sensor->id }}: {
                                type: 'line',
                                yMin: 5,
                                yMax: 5,
                                borderColor: 'red',
                                borderWidth: 2,
                                label : {
                                    enabled : true,
                                    content : 'Baku Mutu {!! strtoupper($sensor->code) !!} - {{ $sensor->quality_standard }} mg/m3'
                                }
                            },
                            @endforeach
                            
                        }
                    },
                    
                        

                }
            },
             // Core options
            aspectRatio: 5 / 3,
            layout: {
            padding: {
                top: 24,
                right: 16,
                bottom: 0,
                left: 8
            }
            },
            elements: {
            line: {
                fill: false
            },
            point: {
                hoverRadius: 7,
                radius: 5
            }
            },
            scales: {
            x: {
                stacked: true
            },
            y: {
                stacked: true
            }
            }
        };
        const chartQualityStandard = new Chart(
            $('#chart'),
            config
        );

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
                    if (data.success) {
                        $('#concentrate').html('')
                        let sensorValues = data.data
                        sensorValues.map(function(value,index) {
                            concentrate = eval(value.sensor.unit_formula)
                            // Formula is (0.0409 * concentrate * 34.08)
                            // * 1000 and / 1000 is for rounding 3 decimal places
                            // Set Quality Standard
                            let line = `line${index+1}`
                            chartQualityStandard.options.plugins.annotation.annotations[line].label.content = `Baku Mutu ${value.sensor.code.toUpperCase()} : ${value.sensor.quality_standard} mg/m³`
                            chartQualityStandard.options.plugins.annotation.annotations[line].yMin = value.sensor.quality_standard
                            chartQualityStandard.options.plugins.annotation.annotations[line].yMax = value.sensor.quality_standard
                            // Set Concentrate Value
                            chartQualityStandard.data.labels[index] = value.sensor.code.toUpperCase() 
                            chartQualityStandard.data.datasets[0].data[index] = concentrate 
                            // Update chart
                            chartQualityStandard.update() 

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