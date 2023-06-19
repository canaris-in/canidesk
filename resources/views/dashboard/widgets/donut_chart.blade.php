{{--      Tickets Donut chart      --}}

    <div class="donut-chart">
        <div class="col-md-6">
            <canvas id="donutChart" height="230px" width="100%"></canvas>
        </div>
        <div>
            <div class="donut-chart-lable">
                <div class="donut-chart-box"></div>
                <div>
                    <p class="donutp">Number Of Tickets</p>
                    <p class="donutp">{{ $totalCount }}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-6">
                    <span class="circle circle-green"></span>
                    <p class="donutp">Open tickets</p>
                    <p class="donutp">{{ $unclosedCount }}</p>
                </div>
                <div class="col-sm-6">
                    <span class="circle circle-red"></span>
                    <p class="donutp">Close tickets</p>
                    <p class="donutp">{{ $closedCount }}</p>
                </div>
                <div class="col-sm-6">
                    <span class="circle circle-blue"></span>
                    <p class="donutp">Hold tickets</p>
                    <p class="donutp">{{ $unclosedCreated30DaysAgoCount }}</p>
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
                    "{{ $unclosedCreated30DaysAgoCount }}"
                ],
                backgroundColor: ['#89F81B', 'red', '#173292'],
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
