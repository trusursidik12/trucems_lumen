@extends('layouts.theme')
@section('title','PLC Simulation')
@section('content')
<div class="px-6 py-3 bg-gray-200 rounded">
    <div class="flex justify-start mb-3">
        <a href="{{ url("/") }}" role="button" class="rounded px-4 py-2 bg-gray-500 text-white">
            Back
        </a>
    </div>
    <div class="bg-green-500"></div>
    <h1 class="text-xl text-center font-medium text-indigo-500">
        Debug PLC Status
    </h1>
    <div class="mt-5 grid grid-cols-4 gap-1">
        <div class="bg-gray-500 rounded p-5 text-center plc-el" data-id="d_off">D OFF: <span class="status">Active</span></div>
        <div class="bg-gray-500 rounded p-5 text-center plc-el" data-id="is_blowback">Is Blowback: <span class="status">Active</span></div>
        <div class="bg-gray-500 rounded p-5 text-center plc-el" data-id="is_calibration" id="btn-is-calibration">Is Calibration: <span class="status">Active</span></div>
        <div class="bg-gray-500 rounded p-5 text-center plc-el" data-id="is_maintenance">Is Maintenance: <span class="status">Active</span></div>
    </div>
    <div class="mt-5 grid grid-cols-8 gap-1">
        @for ($i = 0; $i < 8; $i++)
            <div class="bg-gray-500 rounded py-5 px-2 text-center plc-el" data-id="d{{ $i }}">D{{ $i }}: <span class="status">Active</span></div>
        @endfor
    </div>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function() {
        function getPLC(){
            $.ajax({
                url : `{{ url('plc-simulation/data') }}`,
                dataType : 'json',
                success : function(data){
                   if(data.success){
                        let plc = data.data
                        Object.keys(plc).forEach((column, index) => {
                            try {
                                let value = plc[column]
                                let selector = $(`div[data-id="${column}"]`)
                                let status = (value == 1 ? 'Active' : 'Not Yet')
                                if(value == '1'){
                                    selector.addClass('bg-green-500').removeClass(['bg-gray-500','bg-red-500'])
                                    selector.find('.status').html(status)
                                    
                                }else{
                                    selector.removeClass(['bg-gray-500','bg-green-500']).addClass('bg-red-500')
                                    selector.find('.status').html(status)
                                }
                            } catch (error) {
                                console.log(error)
                            }
                        })
                   } 
                }
            })
            setTimeout(getPLC, 1000);
        }
        getPLC()
    })
</script>
@endsection