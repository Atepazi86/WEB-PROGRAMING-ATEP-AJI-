<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}
include 'koneksi.php';

// Ambil data notifikasi
$q = mysqli_query($conn, "
    SELECT n.*, t.id as id_transaksi, p.nama as nama_pelanggan
    FROM notifikasi n
    JOIN transaksi t ON n.id_transaksi = t.id
    JOIN pelanggan p ON t.id_pelanggan = p.id
    ORDER BY n.waktu_kirim DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Log Notifikasi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <h3>Log Notifikasi</h3>
    <div class="card mt-3">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Waktu Kirim</th>
                        <th>ID Transaksi</th>
                        <th>Pelanggan</th>
                        <th>Pesan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($q)): ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($row['waktu_kirim'])); ?></td>
                        <td>#<?php echo $row['id_transaksi']; ?></td>
                        <td><?php echo $row['nama_pelanggan']; ?></td>
                        <td><?php echo htmlspecialchars($row['pesan']); ?></td>
                        <td><span class="badge bg-success"><?php echo ucfirst($row['status']); ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 