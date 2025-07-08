<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}
include 'koneksi.php';

// Jumlah pelanggan
$jml_pelanggan = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM pelanggan"))[0];
// Jumlah layanan
$jml_layanan = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM layanan"))[0];
// Transaksi hari ini
$tgl = date('Y-m-d');
$jml_transaksi = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM transaksi WHERE tanggal='$tgl'"))[0];
// Total pendapatan hari ini
$total_hari_ini = mysqli_fetch_row(mysqli_query($conn, "SELECT IFNULL(SUM(total),0) FROM transaksi WHERE tanggal='$tgl'"))[0];
// Jumlah barang inventaris yang stoknya menipis (misal, stok < 10)
$jml_inventaris_menipis = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM inventaris WHERE stok < 10"))[0];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dasbor Laundry</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
      .card {
        transition: transform 0.15s, box-shadow 0.15s;
        cursor: pointer;
      }
      .card:hover, .card:active {
        transform: scale(1.05);
        box-shadow: 0 8px 24px rgba(0,0,0,0.18);
        z-index: 2;
      }
      a.card-link {
        text-decoration: none;
      }
      .disabled-card {
        opacity: 0.65;
        cursor: not-allowed;
      }
      .disabled-card:hover, .disabled-card:active {
        transform: none;
        box-shadow: none;
        z-index: 0;
      }
      /* Custom soft colors for dashboard cards */
      .card-pelanggan {
        background:rgb(54, 158, 227) !important;
        color:rgb(19, 99, 170) !important;
      }
      .card-layanan {
        background:rgb(67, 218, 56) !important;
        color:rgb(9, 47, 21) !important;
      }
      .card-transaksi {
        background:rgb(223, 205, 42) !important;
        color:rgb(115, 113, 6) !important;
      }
      .card-pendapatan {
        background:rgb(55, 199, 215) !important;
        color:rgb(18, 59, 62) !important;
      }
      .card-stok {
        background:rgb(220, 44, 59) !important;
        color:rgb(63, 12, 21) !important;
      }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <h3>Dashboard</h3>
    <div class="row mt-4">
        <!-- Pelanggan -->
        <div class="col-md-3">
            <a href="pelanggan.php" class="card-link">
                <div class="card card-pelanggan mb-3">
                    <div class="card-body text-center">
                        <h5 class="card-title">Pelanggan</h5>
                        <h2><?php echo $jml_pelanggan; ?></h2>
                    </div>
                </div>
            </a>
        </div>
        <!-- Layanan -->
        <div class="col-md-3">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <a href="layanan.php" class="card-link">
                    <div class="card card-layanan mb-3">
            <?php else: ?>
                <div class="card card-layanan mb-3 disabled-card">
            <?php endif; ?>
                        <div class="card-body text-center">
                            <h5 class="card-title">Layanan</h5>
                            <h2><?php echo $jml_layanan; ?></h2>
                        </div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    </div>
                </a>
            <?php else: ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- Transaksi Hari Ini -->
        <div class="col-md-3">
            <a href="transaksi.php" class="card-link">
                <div class="card card-transaksi mb-3">
                    <div class="card-body text-center">
                        <h5 class="card-title">Transaksi Hari Ini</h5>
                        <h2><?php echo $jml_transaksi; ?></h2>
                    </div>
                </div>
            </a>
        </div>
        <!-- Pendapatan Hari Ini -->
        <div class="col-md-3">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <a href="laporan.php" class="card-link">
                    <div class="card card-pendapatan mb-3">
            <?php else: ?>
                <div class="card card-pendapatan mb-3 disabled-card">
            <?php endif; ?>
                        <div class="card-body text-center">
                            <h5 class="card-title">Pendapatan Hari Ini</h5>
                            <h2>Rp <?php echo number_format($total_hari_ini, 0, ',', '.'); ?></h2>
                        </div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    </div>
                </a>
            <?php else: ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- Stok Menipis -->
        <div class="col-md-3">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <a href="inventaris.php" class="card-link">
                    <div class="card card-stok mb-3">
            <?php else: ?>
                <div class="card card-stok mb-3 disabled-card">
            <?php endif; ?>
                        <div class="card-body text-center">
                            <h5 class="card-title">Stok Menipis</h5>
                            <h2><?php echo $jml_inventaris_menipis; ?></h2>
                        </div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    </div>
                </a>
            <?php else: ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html> 