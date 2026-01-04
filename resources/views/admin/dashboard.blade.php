@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .stat-card .icon {
            font-size: 2.5rem;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f9ff;
            border-radius: 50%;
        }
        .stat-card .info .number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1;
            margin-bottom: 4px;
        }
        .stat-card .info .label {
            color: #7f8c8d;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Analytics Charts Section */
        .charts-container {
            display: grid;
            grid-template-columns: 2fr 1fr; 
            gap: 1.5rem;
        }
        .chart-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .chart-header h3 {
            margin: 0;
            color: #2c3e50;
            font-size: 1.1rem;
        }
        .chart-controls button {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            color: #6c757d;
            transition: all 0.2s;
        }
        .chart-controls button:hover {
            background: #e2e6ea;
        }
        .chart-controls button.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        @media (max-width: 992px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div style="margin-bottom: 2rem;">
        <h2 style="margin:0;">Welcome back, Admin.</h2>
        <p style="color: #7f8c8d; margin-top: 0.5rem;">Here is the real-time overview of the SubayBus system.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="icon" style="color: #3498db; background: #eaf6fc;">👥</div>
            <div class="info">
                <div class="number">{{ $stats['users'] }}</div>
                <div class="label">Total Passengers</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon" style="color: #2ecc71; background: #eafaf1;">🚚</div>
            <div class="info">
                <div class="number">{{ $stats['drivers'] }}</div>
                <div class="label">Active Drivers</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon" style="color: #f1c40f; background: #fef9e7;">🚌</div>
            <div class="info">
                <div class="number">{{ $stats['buses'] }}</div>
                <div class="label">Total Buses</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon" style="color: #e74c3c; background: #fdedec;">🛣️</div>
            <div class="info">
                <div class="number">{{ $stats['routes'] }}</div>
                <div class="label">Active Routes</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="icon" style="color: #9b59b6; background: #f5eef8;">💬</div>
            <div class="info">
                <div class="number">{{ $stats['feedback'] }}</div>
                <div class="label">New Feedback</div>
            </div>
        </div>
    </div>

    <div class="charts-container">
        
        <div class="chart-card">
            <div class="chart-header">
                <h3>Passenger Growth</h3>
                <div class="chart-controls">
                    <button onclick="updateChart('daily')" id="btn-daily">Daily</button>
                    <button onclick="updateChart('weekly')" id="btn-weekly">Weekly</button>
                    <button onclick="updateChart('monthly')" id="btn-monthly" class="active">Monthly</button>
                    <button onclick="updateChart('yearly')" id="btn-yearly">Yearly</button>
                </div>
            </div>
            <canvas id="userGrowthChart"></canvas>
        </div>

        <div class="chart-card">
            <div class="chart-header">
                <h3>Current Fleet Status</h3>
            </div>
            <canvas id="fleetStatusChart"></canvas>
        </div>
    </div>

    <script>
        // --- Prepare Data from Controller ---
        const chartData = @json($chartData);

        // --- Chart 1: Passenger Growth ---
        const ctx1 = document.getElementById('userGrowthChart').getContext('2d');
        let userGrowthChart = new Chart(ctx1, {
            type: 'line',
            data: {
                // Default to Monthly
                labels: chartData.monthly.labels,
                datasets: [{
                    label: 'New Registrations',
                    data: chartData.monthly.data,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // Function to switch views
        function updateChart(timeframe) {
            // Update Buttons
            document.querySelectorAll('.chart-controls button').forEach(btn => btn.classList.remove('active'));
            document.getElementById('btn-' + timeframe).classList.add('active');

            // Update Chart Data
            userGrowthChart.data.labels = chartData[timeframe].labels;
            userGrowthChart.data.datasets[0].data = chartData[timeframe].data;
            userGrowthChart.update();
        }

        // --- Chart 2: Fleet Status (Doughnut) ---
const ctx2 = document.getElementById('fleetStatusChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ['On Route', 'At Terminal', 'Offline/Maintenance'],
        datasets: [{
            data: [
                {{ $fleetStatus['on_route'] }}, 
                {{ $fleetStatus['at_terminal'] }}, 
                {{ $fleetStatus['offline'] }}
            ], 
            
            backgroundColor: ['#2ecc71', '#f1c40f', '#95a5a6'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        cutout: '70%',
        plugins: { legend: { position: 'bottom' } }
    }
});
    </script>
@endsection