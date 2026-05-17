<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FeriaPass - Admin Dashboard</title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        .admin-dashboard {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .admin-header {
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            margin: 0;
            font-size: 32px;
        }
        .admin-back {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .admin-back:hover {
            background-color: #0056b3;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
        }
        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
        }
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .chart-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .chart-card h3 {
            margin: 0 0 20px 0;
            color: #333;
        }
        .scans-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .scans-table h3 {
            padding: 20px;
            margin: 0;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        .scans-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .scans-table th,
        .scans-table td {
            padding: 12px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .scans-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            color: #333;
        }
        .scans-table tr:hover {
            background-color: #f9f9f9;
        }
        .percentage-bar {
            width: 100%;
            height: 6px;
            background-color: #eee;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 8px;
        }
        .percentage-bar .fill {
            height: 100%;
            background-color: #28a745;
        }
    </style>
</head>
<body>
<header class="topbar">
    <div class="brand">FeriaPass - Admin</div>
    <nav class="nav" style="margin-left: auto; margin-bottom: 0;">
        <a href="/panel">Panel</a>
        <a href="/scanner">Escaner</a>
    </nav>
</header>

<main class="admin-dashboard">
    <div class="admin-header">
        <h1>Panel Administrativo</h1>
        <p style="margin: 0; color: #666;">{{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Boletos</h3>
            <div class="value">{{ $totalTickets }}</div>
        </div>
        <div class="stat-card">
            <h3>Boletos Escaneados</h3>
            <div class="value" style="color: #28a745;">{{ $totalScannedTickets }}</div>
        </div>
        <div class="stat-card">
            <h3>Boletos Disponibles</h3>
            <div class="value" style="color: #ffc107;">{{ $availableTickets }}</div>
        </div>
        <div class="stat-card">
            <h3>Total Clientes</h3>
            <div class="value" style="color: #17a2b8;">{{ $totalCustomers }}</div>
        </div>
        <div class="stat-card">
            <h3>Total Escaneos</h3>
            <div class="value" style="color: #6f42c1;">{{ $totalScans }}</div>
        </div>
        <div class="stat-card">
            <h3>Tasa de Escaneo</h3>
            <div class="value" style="color: #dc3545;">{{ $totalTickets > 0 ? round(($totalScannedTickets / $totalTickets) * 100, 1) : 0 }}%</div>
            @if ($totalTickets > 0)
            <div class="percentage-bar">
                <div class="fill" style="width: {{ ($totalScannedTickets / $totalTickets) * 100 }}%"></div>
            </div>
            @endif
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <h3>Boletos por Tipo de Evento</h3>
            <canvas id="ticketTypeChart"></canvas>
        </div>
        <div class="chart-card">
            <h3>Escaneos por Fecha (últimos 7 días)</h3>
            <canvas id="scansByDateChart"></canvas>
        </div>
    </div>

    <div class="scans-table">
        <h3>Últimos Escaneos (10)</h3>
        <table>
            <thead>
                <tr>
                    <th>ID Boleto</th>
                    <th>Nombre</th>
                    <th>Fecha/Hora</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentScans as $scan)
                <tr>
                    <td><strong>{{ $scan->ticket_id }}</strong></td>
                    <td>{{ $scan->nombre }}</td>
                    <td>{{ \Carbon\Carbon::createFromTimestamp($scan->scanned_at_epoch_seconds)->format('d/m/Y H:i:s') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #999;">No hay escaneos registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>

<script>
    // Gráfico de boletos por tipo
    const typeData = @json($ticketsByType);
    const typeLabels = typeData.map(item => item.tipo_evento === 'concierto' ? 'Concierto' : 'Feria');
    const typeValues = typeData.map(item => item.count);

    const typeCtx = document.getElementById('ticketTypeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeValues,
                backgroundColor: ['#FF6384', '#36A2EB']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de escaneos por fecha
    const dateData = @json($scansByDate);
    const dateLabels = dateData.map(item => item.scan_date);
    const dateValues = dateData.map(item => item.count);

    const dateCtx = document.getElementById('scansByDateChart').getContext('2d');
    new Chart(dateCtx, {
        type: 'bar',
        data: {
            labels: dateLabels,
            datasets: [{
                label: 'Escaneos',
                data: dateValues,
                backgroundColor: '#4CAF50'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>
