@extends('layouts.theme')
@section('title', 'Manual Calibration')
@section('content')
    <div class="px-6 py-3 bg-gray-200 rounded">
        <div class="flex justify-between mb-3">
            <a href="{{ url('/') }}" role="button" class="rounded px-4 py-2 bg-gray-500 text-white">
                Back
            </a>
            <div>
                <span>Filter:</span>
                <input type="hidden" name="filter" value="all">
                <button type="button" data-type="all" class="btn-filter rounded px-4 py-2 bg-indigo-300 text-white">
                    All
                </button>
                <button type="button" data-type="2" class="btn-filter rounded px-4 py-2 bg-indigo-300 text-white">
                    Span Only
                </button>
                <button type="button" data-type="1" class="btn-filter rounded px-4 py-2 bg-indigo-500 text-white">
                    Zero Only
                </button>
                <button type="button" id="btn-export" class="rounded ml-4 px-4 py-2 bg-green-500 text-white">
                    Export All
                </button>
            </div>
        </div>
        <div class="bg-gray-300 p-2 rounded mt-[12vh]">
            <table class="table w-full text-left rounded">
                <thead>
                    <th>Date Time</th>
                    <th>Parameter</th>
                    <th>Calibration Type</th>
                    <th>Before Cal</th>
                    <th>Set Point</th>
                    <th>Offset or Gain</th>
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
        $(document).ready(function() {
            let tbody = $("#tbody-logs");
            $('.btn-filter').click(function(){
                let type = $(this).data('type')
                $('input[name="filter"]').val(type)
            })
            function paginate(url) {
                $.ajax({
                    url: url,
                    type: 'get',
                    dataType: 'json',
                    data : {calibration_type : $('input[name="filter"]').val()},
                    success: function(data) {
                        let links = data.links
                        let logs = data.data
                        let html = ``
                        logs.map(function(log) {
                            html += ` <tr>
                                <td>${log.created_at}</td>
                                <td>${log.sensor.name}</td>
                                <td>${log.calibration_type == 1 ? 'Zero' : 'Span'}</td>
                                <td>${log.start_value}</td>
                                <td>${log.target_value}</td>
                                <td>${log.result_value}</td>
                                <td>${log.sensor.unit.name}</td>
                            </tr>`
                        })
                        tbody.html(html)
                        html = ``
                        links.map(function(link) {
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
                            html += `
                    <a href="#" data-url="${link.url}" class="btn-link rounded flex h-10 w-10 justify-center items-center bg-${link.active ? 'indigo' : 'slate'}-500 text-center text-white ml-1">
                        <span>${label}</span>
                    </a>
                    `
                        })
                        $('#section-links').html(html)

                        $('.btn-link').click(function() {
                            let url = $(this).data("url")
                            paginate(url)
                        })

                    },
                    error: function(xhr, status, err) {

                    }
                })
            }

            paginate(`{{ url('api/calibration-logs/paginate') }}`)
        })
    </script>
    <script>
        $(document).ready(function() {
            $('#btn-export').click(function() {
                window.location.href = `{{ url('api/calibration-logs/export') }}`
            })
        })
    </script>
@endsection
