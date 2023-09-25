<div class="horizontalChart">
    <canvas id="horizontalChart"></canvas>
</div>



@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Horizontal Bar Data
            var ctxHorizontal = document.getElementById('horizontalChart').getContext('2d');
            // Set chart data
            let cValues = @json($categoryValues);
            var data = {
                labels: cValues,
                datasets: [{
                    label: "{{__('Average resolved tickets')}}",
                    data: <?php echo json_encode($categoryTickets); ?>,
                    backgroundColor: '#2EA5FB', // Bar color
                    borderColor: 'rgba(54, 162, 235, 1)', // Border color
                    borderWidth: 1 // Border width
                }]
            };

            // Set chart options
            var options = {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        stepSize: 4,
                    }
                }
            };

            // Create the bar chart
            var barChart = new Chart(ctxHorizontal, {
                type: 'bar',
                data: data,
                options: options
            });

        });
    </script>
@endpush
