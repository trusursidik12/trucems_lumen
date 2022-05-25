@extends('layouts.theme')
@section('title','Manual Calibration')
@section('content')
<div class="px-6 py-3 bg-gray-200 rounded">
    <div class="flex justify-start mb-3">
        <a href="{{ url("/") }}" role="button" class="px-4 py-2 bg-gray-500 text-white">
            Back
        </a>
    </div>
    <div class="bg-gray-300 px-2 py-1">
        <table class="table w-full text-left">
            <thead>
                <th>Date Time</th>
                <th>Parameter</th>
                <th>Concentrate</th>
                <th>Unit</th>
            </thead>
            <tbody id="tbody-logs">
               
            </tbody>
        </table>
        <div class="flex justify-start">
            
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
$(document).ready(function(){
    let tbody = $("#tbody-logs");
    $.ajax({
        url : `{{ url('api/calibration-avg-logs/paginate') }}`,
        type : 'get',
        dataType : 'json',
        // data : $(this).serialize(),
        success : function(data){
            let links = data.links
            let logs = data.data
            let html = ``
            logs.map(function(log){
                html+=` <tr>
                            <td>${log.created_at}</td>
                            <td>${log.sensor.name}</td>
                            <td>${log.value}</td>
                            <td>${log.sensor.unit.name}</td>
                        </tr>`
            })

            tbody.html(html)
        },
        error : function(xhr, status, err){
            
        }
    })
})
</script>
@endsection