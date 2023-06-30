<div class="barChart">
    <canvas id="barChart"></canvas>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Define the chart data
            const currentDate = new Date();
            const weekNames = [];
            // Bar Chart
            var ctxBar = document.getElementById('barChart').getContext('2d');
            // Set chart data
            const weeklyBarChart = ["{{ $tickets['Sunday'] }}", "{{ $tickets['Monday'] }}",
                "{{ $tickets['Tuesday'] }}", "{{ $tickets['Wednesday'] }}", "{{ $tickets['Thursday'] }}",
                "{{ $tickets['Friday'] }}", "{{ $tickets['Saturday'] }}"
            ];

            for (let i = 6; i >= 0; i--) {
                const day = new Date(currentDate);
                day.setDate(day.getDate() - i);
                const options = {
                    weekday: 'long'
                }; // Specify the format of the weekday
                const weekName = new Intl.DateTimeFormat('en-US', options).format(day);
                weekNames.push(weekName);
            }

            var data = {
                labels: weekNames,
                datasets: [{
                    label: "{{__('Average resolved tickets')}}",
                    data: weeklyBarChart,
                    backgroundColor: '#2EA5FB', // Bar color
                    borderColor: 'rgba(54, 162, 235, 1)', // Border color
                    borderWidth: 1 // Border width
                }]
            };

            // Set chart options
            var options = {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        stepSize: 4,
                    }
                }
            };

            // Create the bar chart
            var barChart = new Chart(ctxBar, {
                type: 'bar',
                data: data,
                options: options
            });
        });
    </script>
@endpush
