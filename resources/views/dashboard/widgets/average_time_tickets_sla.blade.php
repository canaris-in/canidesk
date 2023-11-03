<?php

?>
<div class="lineChart dashboard-widgets dashboard-widgets--average-time-sla">
    <canvas id="lineChart"></canvas>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            const data = <?php echo json_encode($sla); ?>;


            var chartData = {
                labels: weekNames,
                datasets: [{
                    label: "{{ __('Average Time Taken To Close Within SLA') }}",
                    data: data,
                    backgroundColor: 'rgba(46,165,251, 0.7)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            };

            // Create the chart
            var lineChart = new Chart(ctxLine, {
                type: 'line',
                data: chartData,
                options: {
                    indexAxis: 'x',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            window.addEventListener('afterprint', () => {
                lineChart.resize();
            });

        });
    </script>
@endpush
