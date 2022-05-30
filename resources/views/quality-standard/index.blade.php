@extends('layouts.theme')
@section('title','Quality Standards')
@section('content')
<div class="px-6 py-3 bg-gray-200 rounded">
    <div class="flex justify-start mb-3">
        <a href="{{ url("/") }}" role="button" class="rounded px-4 py-2 bg-gray-500 text-white">
            Back
        </a>
    </div>
    <div class="flex justify-between pt-[6vh] space-x-3">
        <div class="w-full rounded px-6 py-3 bg-gray-300">
            <div class="flex justify-end">
               <canvas id="chart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="{{ url('js/chart.js/chart.min.js') }}"></script>
<script src="{{ url('js/chart.js/chartjs-plugin-annotation.min.js') }}"></script>
<script>
    $(document).ready(function() {

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
    const labels = [
      'TRS (H2S)',
    ];
  
    const data = {
      labels: labels,
      datasets: [{
        barThickness : 68,
        label: 'Concentratation PPM',
        backgroundColor: 'rgb(255, 99, 132)',
        borderColor: 'rgb(255, 99, 132)',
        data: [10],
      }]
    };
  
    const config = {
      type: 'bar',
      data: data,
      options: {
        plugins: {
            autocolors: false,
            annotation: {
                annotations: {
                    line1: {
                        type: 'line',
                        yMin: 5,
                        yMax: 5,
                        borderColor: 'rgb(255, 99, 132)',
                        borderWidth: 2,
                    }
                }
            }
        }
      }
    };
    const myChart = new Chart(
        document.getElementById('chart'),
        config
    );
  </script>
@endsection