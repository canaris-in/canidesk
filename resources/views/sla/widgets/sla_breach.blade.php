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
            const last7DateNames = [];
            const lastSevenDateNames = [];

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            for (let i = 7; i >= 0; i--) {
                const day = new Date(currentDate);
                day.setDate(day.getDate() - i);
                const dayOfMonth = day.getDate();
                const month = months[day.getMonth()];
                const dateName = `${dayOfMonth} ${month}`;
                last7DateNames.push(dateName);
            }

            const data = <?php echo json_encode($sla); ?>;
            const data_all = <?php echo json_encode($sla_all); ?>;


            var chartData = {
                labels: last7DateNames,
                datasets: [{
                        label: "{{ __('SLA Breached Ticket') }}",
                        data: data,
                        backgroundColor: 'rgba(0, 255, 0, 0.7)',
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
