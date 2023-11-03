{{--      Tickets Donut chart      --}}

    <div class="donut-chart">
        <div class="col-md-6">
            <canvas id="donutChart" height="230px" width="100%"></canvas>
        </div>
        <div>
            <div class="donut-chart-lable">
                <div class="donut-chart-box"></div>
                <div>
                    <p class="donutp">{{__('Number Of Tickets')}}</p>
                    <p class="donutp">{{ $totalCount }}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-6">
                    <span class="circle circle-green"></span>
                    <p class="donutp">{{__('Open Tickets')}}</p>
                    <p class="donutp">{{ $unclosedCount }}</p>
                </div>
                <div class="col-sm-6">
                    <span class="circle circle-red"></span>
                    <p class="donutp">{{__('Close Tickets')}}</p>
                    <p class="donutp">{{ $closedCount }}</p>
                </div>
                <div class="col-sm-6">
                    <span class="circle circle-blue"></span>
                    <p class="donutp">{{__('Pending Tickets')}}</p>
                    <p class="donutp">{{ $holdTicket }}</p>
                </div>
                <div class="col-sm-6">
                    <span class="circle circle-cyan"></span>
                    <p class="donutp">{{__('SLA Breach ')}}</p>
                    <p class="donutp">{{ $overdueCount }}</p>
                </div>
            </div>
        </div>
    </div>


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the canvas element
        var ctx = document.getElementById('donutChart').getContext('2d');

        // Set chart data
        var data = {
            datasets: [{
                data: ["{{ $unclosedCount }}", "{{ $closedCount }}",
                    "{{ $holdTicket }}","{{ $overdueCount }}"
                ],
                backgroundColor: ['#89F81B', 'red', '#173292','#00FFFF'],
                borderColor: 'transparent',
            }]
        };

        // Set chart options
        var options = {
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 70
        };

        // Create the donut chart
        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: options
        });
    });
</script>
@endpush
