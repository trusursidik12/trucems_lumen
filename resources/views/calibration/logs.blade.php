@extends('layouts.theme')
@section('title','Manual Calibration')
@section('content')
<div class="px-6 py-3 bg-gray-200 rounded">
    <div class="flex justify-between mb-3">
        <a href="{{ url("/") }}" role="button" class="px-4 py-2 bg-gray-500 text-white">
            Back
        </a>
        <button type="button" id="btn-export" class="px-4 py-2 bg-green-500 text-white">
            Export CSV
        </button>
    </div>
    <div class="bg-gray-300 p-2 rounded mt-[12vh]">
        <table class="table w-full text-left">
            <thead>
                <th>Date Time</th>
                <th>Parameter</th>
                <th>Concentrate</th>
                <th>Row Count</th>
                <th>Unit</th>
            </thead>
            <tbody id="tbody-logs">
               
            </tbody>
        </table>
        <div class="flex justify-start mt-4" id="section-links">
            
        </div>
    </div>
</div>
<div class="bg-slate-500"></div>
@endsection
@section('js')
<script>
$(document).ready(function(){
    let tbody = $("#tbody-logs");
    function paginate(url){
        $.ajax({
            url : url,
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
                                <td>${log.row_count}</td>
                                <td>${log.sensor.unit.name}</td>
                            </tr>`
                })
                tbody.html(html)
                html = ``
                links.map(function(link){
                    let label = ``;
                    switch (link.label) {
                        case 'pagination.previous':
                            label = `&larr;`
                            break;
                        case 'pagination.next':
                            label = `&rarr;`
                            break;
                        default:
                            label = link.label
                            break;
                    }
                    html+=`
                    <a href="#" data-url="${link.url}" class="btn-link flex h-10 w-10 justify-center items-center bg-${link.active ? 'indigo' : 'slate'}-500 text-center text-white ml-1">
                        <span>${label}</span>
                    </a>
                    `
                })
                $('#section-links').html(html)

                $('.btn-link').click(function(){
                    let url = $(this).data("url")
                    paginate(url)
                })

            },
            error : function(xhr, status, err){
                
            }
        })
    }
    
    paginate(`{{ url('api/calibration-avg-logs/paginate') }}`)
})
</script>
<script>
    $(document).ready(function(){
        $('#btn-export').click(function(){
            window.location.href = `{{ url('api/calibration-avg-logs/export') }}`
        })
    })
</script>
@endsection