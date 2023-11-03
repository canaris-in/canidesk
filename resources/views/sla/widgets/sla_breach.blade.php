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
            const data_all = <?php echo json_encode($sla_all); ?>;


            var chartData = {
                // labels: weekNames,
                datasets: [{
                        label: "{{ __('SLA Breached Ticket') }}",
                        data: data,
                        backgroundColor: 'rgba(0, 255, 0, 0.5)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    },
                    {
                        label: "{{ __('SLA Total Ticket') }}",
                        data: data_all,
                        backgroundColor: 'rgba(0, 123, 255, 0.5)',
                        borderColor: 'rgba(0, 123, 255, 1)',
                        borderWidth: 1
                    }
                ]
            };

            // Create the chart
            var lineChart = new Chart(ctxLine, {
                type: 'bar',
                data: chartData,
                options: {
                    indexAxis: 'x',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            beginAtZero: true,
                            stacked: true,
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
