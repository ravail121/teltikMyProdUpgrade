<div id="usageModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <input type="hidden" id ="phone-no-usages" name="phone_number" readonly="true">
                <input type="hidden" id ="subscription-no-usages" name="subscription_id" readonly="true">
            </div>
            <div class="modal-body">
                <div class="tabsdata">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="tabstable">
                                <div class="tabsbx">
                                    <ul class="nav nav-tabs usages-tab" id="myTab" role="tablist">
                                        <li class="nav-item active">
                                            <a class="nav-link voice-btn" id="voice-tab" data-toggle="tab" href="#voice" role="tab" aria-controls="voice" aria-selected="true" data-type="voice">VOICE</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link internet-btn" id="data-tab" data-toggle="tab" href="#data" role="tab" aria-controls="data" aria-selected="false" data-type="data">DATA</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link text-btn" id="text-tab" data-toggle="tab" href="#text" role="tab" aria-controls="text" aria-selected="false" data-type="text">TEXT</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-info">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6 width-70">
                                            {!! Form::label('cycle', 'Cycle  ') !!}
                                            {!! Form::select('cycle', [], null) !!}
                                        </div>
                                        <input type="hidden" id="cyclesStore" name="cyclesStore" value="">
                                        <div class="col-sm-6 col-md-6 col-lg-6 width-30 text-right">
                                            <button type="button" class="btn download-usages-btn" title="Download CSV">
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-content" id="myTabContent">
                                    @include('customer.partials.__voice')
                                    @include('customer.partials.__data')
                                    @include('customer.partials.__text')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(function(){
            let cycle_array = <?php echo json_encode($usages) ?>;
            var cy=1;
            $('#usageModal').on('change', '#cycle', function(e) {
                e.preventDefault();
                var subscriptionId = $('#usageModal #subscription-no-usages').val();
                var type = $('#usageModal .usages-tab .active .nav-link').attr('data-type');
                if(type === 'voice'){
                    getCallUsages(subscriptionId);
                }else if(type === 'data'){
                    getDataUsages(subscriptionId);
                }else{
                    getTextUsages(subscriptionId);
                }
            });

            $('.billPlans-line').on('click', '.show-usages', function(e) {
                e.preventDefault();
                
                let phone = $(this).attr('data-phone');
                let subscriptionId = $(this).attr('data-subscription');
               
                let cycleId = null;
                let cycleOption = "";
                console.log(cy);
                // $.each(cy, function (index, value) {
                   
                        
                            
                //     cycleOption+=`<option value="${cycle_index}" selected>${cycle_value}</option>`;
                            
                //     cycleOption+=`<option value="${cycle_index}" >${cycle_value}</option>`;
                            
                        
                    
                // })
                // if(cycleOption){
                //     $(document).find('#cycle').html(cycleOption);
                // }else{
                //     $(document).find('#cycle').html('<option value="">No Cycles Available</option>');
                // }
                // cycleOption+=`<option value="202201" >1 January - 31 January </option>`;
                // cycleOption+=`<option value="202202" >1 February - 28 February </option>`;
                // cycleOption+=`<option value="202203" >1 March - 31 March </option>`;
                // cycleOption+=`<option value="202204" >1 April - 30 April </option>`;
                // cycleOption+=`<option value="202205" >1 May - 31 May </option>`;
                // cycleOption+=`<option value="202206" >1 June - 30 June </option>`;
                // cycleOption+=`<option value="202207" >1 July - 31 July </option>`;
                // cycleOption+=`<option value="202208" >1 August - 31 August </option>`;
                // cycleOption+=`<option value="202209" >1 September - 30 September</option>`;
                // cycleOption+=`<option value="202210" >1 October - 31 October</option>`;
                // cycleOption+=`<option value="202211" >1 November - 30 November </option>`;
                // cycleOption+=`<option value="202212" >1 December - 31 December </option>`;
                // $(document).find('#cycle').html(cycleOption);
                $('#usageModal #phone-no-usages').val(phone);
                $('#usageModal #subscription-no-usages').val(subscriptionId);
                $('#usageModal .voice-btn').click();
            });

            $('#usageModal').on('click', '.voice-btn', function(e) {
                e.preventDefault();
                getCallUsages($('#usageModal #subscription-no-usages').val());
            });

            $('#usageModal').on('click', '.internet-btn', function(e) {
                e.preventDefault();
                getDataUsages($('#usageModal #subscription-no-usages').val());
            });

            $('#usageModal').on('click', '.text-btn', function(e) {
                e.preventDefault();
                getTextUsages($('#usageModal #subscription-no-usages').val());
            });

            $('#usageModal').on('click', '.download-usages-btn', function(e) {
                e.preventDefault();
                downloadUsages($('#usageModal #subscription-no-usages').val());
            });

            function downloadUsages(subscriptionId) {
                var urlParts = '?subscription_id=' + subscriptionId +
                    '&&usage_type=' + $('#usageModal .usages-tab .active .nav-link').attr('data-type') +
                    '&&cycle=' + $('#usageModal #cycle').val() +
                    '&&cycle_text=' + $('#usageModal #cycle').text() +
                    '&&download=true';
                var url = '{{  url('data-usages') }}';
                window.open(url+urlParts);
                
            }

            function getTextUsages(subscriptionId) {
                var cycle = $('#usageModal #cycle').val()
                $('#usageModal #text-usages-table').DataTable( {
                    "scrollX": true,
                    "lengthMenu": [[100, 250, 500], [100, 250, 500]],
                    "processing": true,
                    
                    "responsive": true,
                    "info": true,
                    "bDestroy": true,
                    "language": {
                        "processing": 'Please wait...',
                    },
                    "ajax": {
                        "type": "POST",
                        "url": '{{ route('usages.data') }}',
                        data: function ( d ) {
                            d.subscription_id = subscriptionId;
                            d.date=cycle;
                            d.usage_type = 2;
                        },
                        beforeSend: showLoader,
                        complete: function (data) {
                            let total = data.responseJSON.total
                            $(document).find('.plan-details-modal__usages_summary_total').text(`Total: ${total}`);
                            cyy=data.responseJSON.cycles;
                            let cycleOption = "";
                            $.each(cyy, function (index, value) {
                   
                        
                            
                                     cycleOption+=`<option selected value="${value['value1']}" >${value['key1']}</option>`;
                                        
                                    
                                
                            });

                            if(cy==1){
                                console.log()
                            $(document).find('#cycle').html(cycleOption);
                            cy=2;
                        }else if(!cyy){
                            $(document).find('#cycle').html('<option  value="">No Cycles Available</option>');
                        }
                            hideLoader();
                        },
                        error: function (xhr, status, error) {
                            if(xhr.status === '401'){
                                location.reload();
                            }
                        },
                    },
                    "columns": [
                        { "data": 'msisdn' },
                         { "data": 'local_time_call_started' },
                         { "data": 'ans_time_duration_min' },
                         { "data": 'call_direction' },
                         { "data": 'translated_number' },
                         { "data": 'tech_used' },

                    ]
                });
            };

            function getDataUsages(subscriptionId) {
                var cycle = $('#usageModal #cycle').val()
                $('#usageModal #data-usages-table').DataTable( {
                    "scrollX": true,
                    "lengthMenu": [[100, 250, 500], [100, 250, 500]],
                    "processing": true,
                   
                    "responsive": true,
                    "info": true,
                    "bDestroy": true,
                    "language": {
                        "processing": 'Please wait...',
                    },
                    "ajax": {
                        "type": "POST",
                        "url": '{{ route('usages.data') }}',
                        data: function ( d ) {
                            d.subscription_id = subscriptionId;
                            d.date=cycle;
                            d.usage_type = 3;
                        },
                        beforeSend: showLoader,
                        complete: function (data) {
                            let total = (data.responseJSON.total).toFixed(2)
                            $(document).find('.plan-details-modal__usages_summary_total').text(`Total: ${total} MB`);
                            cyy=data.responseJSON.cycles;
                            let cycleOption = "";
                            $.each(cyy, function (index, value) {
                   
                        
                            
                                     cycleOption+=`<option selected value="${value['value1']}" >${value['key1']}</option>`;
                                        
                                    
                                
                            });

                            if(cy==1){
                                console.log()
                            $(document).find('#cycle').html(cycleOption);
                            cy=2;
                        }else if(!cyy){
                            $(document).find('#cycle').html('<option value="">No Cycles Available</option>');
                        }
                            hideLoader();
                        },
                        error: function (xhr, status, error) {
                            if(xhr.status == '401'){
                                location.reload();
                            }
                        },
                    },
                    "columns": [
                        { "data": 'msisdn' },
                         { "data": 'local_time_call_started' },
                         { "data": 'total_volume_mb' },
                         { "data": 'tech_used' },

                    ]

                });
            };

            function getCallUsages(subscriptionId) {
                cycle = $('#usageModal #cycle').val()
                console.log(cycle);
                console.log(subscriptionId);
                $('#usageModal #call-usages-table').DataTable( {
                    "scrollX": true,
                    "lengthMenu": [[100, 250, 500], [100, 250, 500]],
                    "processing": true,
                   
                    "responsive": true,
                    "info": true,
                    "bDestroy": true,
                    "language": {
                        "processing": 'Please wait...',
                    },
                    "ajax": {
                        "type": "POST",
                        "url": '{{ route('usages.data') }}',
                        data: function ( d ) {
                            d.subscription_id = subscriptionId;
                            d.date=cycle;
                            d.usage_type = 1;
                        },
                        beforeSend: showLoader,
                        complete: function (data) {
                            
                            let total = (data.responseJSON.total).toFixed(2)
                            $(document).find('.plan-details-modal__usages_summary_total').text(`Total: ${total} Min`);
                            cyy=data.responseJSON.cycles;
                            let cycleOption = "";
                            $.each(cyy, function (index, value) {
                   
                        
                            
                                     cycleOption+=`<option selected value="${value['value1']}" >${value['key1']}</option>`;
                                        
                                    
                                
                            });

                            if(cy==1){
                                console.log()
                            $(document).find('#cycle').html(cycleOption);
                            cy=2;
                        }else if(!cyy){
                            $(document).find('#cycle').html('<option value="">No Cycles Available</option>');
                        }
                            hideLoader();
                        },
                        error: function (xhr, status, error) {
                            if(xhr.status == '401'){
                                location.reload();
                            }
                        },
                    },
                    "columns": [
                        { "data": 'msisdn' },
                         { "data": 'local_time_call_started' },
                         { "data": 'ans_time_duration_min' },
                         { "data": 'call_direction' },
                         { "data": 'translated_number' },
                         { "data": 'tech_used' },

                    ]

                });
            };
        });
    </script>
@endpush
