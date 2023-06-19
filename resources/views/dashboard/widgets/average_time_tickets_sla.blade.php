<div class="lineChart dashboard-widgets dashboard-widgets--average-time-sla">
    <canvas id="lineChart"></canvas>
</div>

@push('styles')
    <style>
        /*.dashboard-widgets.dashboard-widgets--average-time-sla > canvas {*/
        /*    width: 100% !important;*/
        /*}*/
    </style>
@endpush


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get the canvas element
            var ctxLine = document.getElementById('lineChart').getContext('2d');

            // Define the chart data
            const currentDate = new Date();
            const weekNames = [];

            for (let i = 6; i >= 0; i--) {
                const day = new Date(currentDate);
                day.setDate(day.getDate() - i);
                const options = {
                    weekday: 'long'
                }; // Specify the format of the weekday
                const weekName = new Intl.DateTimeFormat('en-US', options).format(day);
                weekNames.push(weekName);
            }

            const data = ["{{ $tickets['Sunday'] }}", "{{ $tickets['Monday'] }}",
                "{{ $tickets['Tuesday'] }}", "{{ $tickets['Wednesday'] }}", "{{ $tickets['Thursday'] }}",
                "{{ $tickets['Friday'] }}", "{{ $tickets['Saturday'] }}"
            ];

            var chartData = {
                labels: weekNames,
                datasets: [{
                    label: 'Average Time Taken To Close Within SLA',
                    data: data,
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            };

            // Create the chart
            var lineChart = new Chart(ctxLine, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            window.addEventListener('afterprint', () => {
                lineChart.resize();
            });

        });
    </script>
@endpush
