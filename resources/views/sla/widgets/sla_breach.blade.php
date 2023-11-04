<?php

?>
<div class="lineChart dashboard-widgets dashboard-widgets--average-time-sla">
    <canvas id="barChart"></canvas>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the canvas element
            var ctxLine = document.getElementById('barChart').getContext('2d');

            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            var from = <?php echo json_encode($sla_from); ?>;
            var to = <?php echo json_encode($sla_to); ?>;
            <?php echo json_encode($sla_to); ?>;
            const fromDate = new Date(from);
            const toDate = new Date(to);

            const dateNames = [];

            const currentDate = new Date(fromDate);

            while (currentDate <= toDate) {
                const dayOfMonth = currentDate.getDate();
                const month = months[currentDate.getMonth()];
                const dateName = `${dayOfMonth} ${month}`;
                dateNames.push(dateName);

                // Move to the next day
                currentDate.setDate(currentDate.getDate() + 1);
            }

            console.log(dateNames);


            const data = <?php echo json_encode($sla); ?>;
            const data_all = <?php echo json_encode($sla_all); ?>;


            var chartData = {
                labels: dateNames,
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
