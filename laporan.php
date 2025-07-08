<?php
session_start();
if (!isset($_SESSION['login']) || (isset($_SESSION['role']) && $_SESSION['role'] != 'admin')) {
    header('Location: dashboard.php');
    exit;
}
include 'koneksi.php';

// Filter tanggal
$tgl1 = isset($_GET['tgl1']) ? $_GET['tgl1'] : date('Y-m-01');
$tgl2 = isset($_GET['tgl2']) ? $_GET['tgl2'] : date('Y-m-d');

$where = "WHERE t.tanggal BETWEEN '$tgl1' AND '$tgl2'";

// Hitung KPI
$kpi_query = mysqli_query($conn, "
    SELECT 
        IFNULL(SUM(total), 0) as total_pendapatan,
        COUNT(*) as total_transaksi,
        IFNULL(AVG(total), 0) as rata_rata_transaksi
    FROM transaksi t
    $where
");
$kpi = mysqli_fetch_assoc($kpi_query);

// Data untuk chart
$q_chart = mysqli_query($conn, "
    SELECT tanggal, SUM(total) as pendapatan 
    FROM transaksi t
    $where 
    GROUP BY tanggal 
    ORDER BY tanggal
");
$chart_labels = [];
$chart_data = [];
while ($row = mysqli_fetch_assoc($q_chart)) {
    $chart_labels[] = date('d M Y', strtotime($row['tanggal']));
    $chart_data[] = $row['pendapatan'];
}

// Data untuk Pie Chart Layanan Terlaris
$q_layanan_chart = mysqli_query($conn, "
    SELECT l.nama_layanan, SUM(dt.subtotal) as total_pendapatan
    FROM detail_transaksi dt
    JOIN layanan l ON dt.id_layanan = l.id
    JOIN transaksi t ON dt.id_transaksi = t.id
    $where
    GROUP BY l.nama_layanan
    ORDER BY total_pendapatan DESC
");
$layanan_labels = [];
$layanan_data = [];
while ($row = mysqli_fetch_assoc($q_layanan_chart)) {
    $layanan_labels[] = $row['nama_layanan'];
    $layanan_data[] = $row['total_pendapatan'];
}

// Ambil data transaksi
$q = mysqli_query($conn, "
    SELECT t.*, p.nama as nama_pelanggan 
    FROM transaksi t 
    JOIN pelanggan p ON t.id_pelanggan = p.id 
    $where 
    ORDER BY t.tanggal DESC, t.id DESC
");

// Export ke CSV
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="laporan_laundry.csv"');
    echo "ID,Tanggal,Pelanggan,Total,Status\n";
    $q_export = mysqli_query($conn, "
        SELECT t.*, p.nama as nama_pelanggan 
        FROM transaksi t 
        JOIN pelanggan p ON t.id_pelanggan = p.id 
        $where 
        ORDER BY t.tanggal DESC, t.id DESC
    ");
    while ($row = mysqli_fetch_assoc($q_export)) {
        echo $row['id'] . ',' . $row['tanggal'] . ',"' . $row['nama_pelanggan'] . '",' . $row['total'] . ',' . $row['status'] . "\n";
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi Laundry</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="dashboard.php" class="btn btn-secondary me-2">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
        <h3 class="mb-0 me-auto">Laporan Transaksi</h3>
        <a href="?tgl1=<?php echo $tgl1; ?>&tgl2=<?php echo $tgl2; ?>&export=1" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Ekspor ke Excel
        </a>
    </div>
    
    <form method="get" class="row g-3 align-items-end mb-3 bg-light p-3 rounded">
        <div class="col-auto">
            <label class="form-label">Dari</label>
            <input type="date" name="tgl1" class="form-control" value="<?php echo $tgl1; ?>">
        </div>
        <div class="col-auto">
            <label class="form-label">Sampai</label>
            <input type="date" name="tgl2" class="form-control" value="<?php echo $tgl2; ?>">
        </div>
        <div class="col-md-4">
            <button class="btn btn-primary" type="submit">Tampilkan</button>
        </div>
    </form>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-bg-primary">
                <div class="card-body">
                    <h6 class="card-title">Total Pendapatan</h6>
                    <h4 class="mb-0">Rp <?php echo number_format($kpi['total_pendapatan'], 0, ',', '.'); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-info">
                <div class="card-body">
                    <h6 class="card-title">Total Transaksi</h6>
                    <h4 class="mb-0"><?php echo number_format($kpi['total_transaksi']); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h6 class="card-title">Rata-rata per Transaksi</h6>
                    <h4 class="mb-0">Rp <?php echo number_format($kpi['rata_rata_transaksi'], 0, ',', '.'); ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">Grafik Pendapatan Harian</div>
                <div class="card-body">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">Layanan Terlaris</div>
                <div class="card-body">
                    <canvas id="layananChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($q)): ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                    <td><?php echo $row['nama_pelanggan']; ?></td>
                    <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                    <td><?php echo ucfirst($row['status']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
const ctx = document.getElementById('incomeChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Pendapatan',
            data: <?php echo json_encode($chart_data); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value, index, values) {
                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                        return label;
                    }
                }
            }
        }
    }
});

const layananCtx = document.getElementById('layananChart');
new Chart(layananCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($layanan_labels); ?>,
        datasets: [{
            label: 'Pendapatan per Layanan',
            data: <?php echo json_encode($layanan_data); ?>,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed !== null) {
                            label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed);
                        }
                        return label;
                    }
                }
            }
        }
    },
});
</script>
</body>
</html> 