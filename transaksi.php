<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}
include 'koneksi.php';

// Proses hapus transaksi
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM detail_transaksi WHERE id_transaksi=$id");
    mysqli_query($conn, "DELETE FROM transaksi WHERE id=$id");
    header('Location: transaksi.php');
    exit;
}

// Proses update status
if (isset($_GET['update_status'])) {
    $id = $_GET['update_status'];
    $status = $_GET['status'];
    mysqli_query($conn, "UPDATE transaksi SET status='$status' WHERE id=$id");
    header('Location: transaksi.php');
    exit;
}

// Proses tambah transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pelanggan = $_POST['id_pelanggan'];
    $tanggal = $_POST['tanggal'];
    $layanan_ids = $_POST['layanan_id'];
    $jumlahs = $_POST['jumlah'];
    $total = 0;
    
    // Hitung total
    for ($i = 0; $i < count($layanan_ids); $i++) {
        if ($layanan_ids[$i] && $jumlahs[$i] > 0) {
            $q_harga = mysqli_query($conn, "SELECT harga FROM layanan WHERE id=" . $layanan_ids[$i]);
            $harga = mysqli_fetch_assoc($q_harga)['harga'];
            $total += $harga * $jumlahs[$i];
        }
    }
    
    // Insert transaksi
    mysqli_query($conn, "INSERT INTO transaksi (id_pelanggan, tanggal, total, status) VALUES ($id_pelanggan, '$tanggal', $total, 'proses')");
    $id_transaksi = mysqli_insert_id($conn);
    
    // Insert detail transaksi
    for ($i = 0; $i < count($layanan_ids); $i++) {
        if ($layanan_ids[$i] && $jumlahs[$i] > 0) {
            $q_harga = mysqli_query($conn, "SELECT harga FROM layanan WHERE id=" . $layanan_ids[$i]);
            $harga = mysqli_fetch_assoc($q_harga)['harga'];
            $subtotal = $harga * $jumlahs[$i];
            mysqli_query($conn, "INSERT INTO detail_transaksi (id_transaksi, id_layanan, jumlah, subtotal) VALUES ($id_transaksi, " . $layanan_ids[$i] . ", " . $jumlahs[$i] . ", $subtotal)");
        }
    }
    
    header('Location: transaksi.php');
    exit;
}

// Pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
if ($search) {
    $where = "WHERE p.nama LIKE '%$search%' OR t.id LIKE '%$search%'";
}

// Ambil data transaksi
$q = mysqli_query($conn, "
    SELECT t.*, p.nama as nama_pelanggan, p.telepon 
    FROM transaksi t 
    JOIN pelanggan p ON t.id_pelanggan = p.id 
    $where 
    ORDER BY t.tanggal DESC, t.id DESC
");

// Ambil data untuk dropdown
$q_pelanggan = mysqli_query($conn, "SELECT * FROM pelanggan ORDER BY nama");
$q_layanan = mysqli_query($conn, "SELECT * FROM layanan ORDER BY nama_layanan");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Transaksi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h4 class="mb-0">Data Transaksi</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTransaksi">
            <i class="bi bi-plus-lg"></i> Tambah Transaksi
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <!-- Pencarian -->
            <form method="get" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama pelanggan atau ID transaksi..." value="<?php echo $search; ?>">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                    <?php if($search): ?>
                        <a href="transaksi.php" class="btn btn-outline-danger">Atur Ulang</a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Tabel Transaksi -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Telepon</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                            <th>Notifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($q)): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td><?php echo $row['nama_pelanggan']; ?></td>
                            <td><?php echo $row['telepon']; ?></td>
                            <td>Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $row['status'] == 'proses' ? 'warning' : 
                                        ($row['status'] == 'selesai' ? 'success' : 'info'); 
                                ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="lihatDetail(<?php echo $row['id']; ?>)">
                                    Detail
                                </button>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        Status
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=proses">Proses</a></li>
                                        <li><a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=selesai">Selesai</a></li>
                                        <li><a class="dropdown-item" href="?update_status=<?php echo $row['id']; ?>&status=diambil">Diambil</a></li>
                                    </ul>
                                </div>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus transaksi ini?')">
                                    Hapus
                                </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'selesai'): ?>
                                    <?php if (!empty($row['telepon'])): ?>
                                        <a href="https://api.whatsapp.com/send?phone=<?php echo $row['telepon']; ?>&text=Halo%20Sdr/i%20<?php echo urlencode($row['nama_pelanggan']); ?>%2C%20laundry%20Anda%20dengan%20ID%20#<?php echo $row['id']; ?>%20sudah%20selesai%20dan%20siap%20diambil.%20Terima%20kasih." 
                                           target="_blank" 
                                           class="btn btn-sm btn-success"
                                           onclick="return confirm('Anda akan mengirim notifikasi WhatsApp ke <?php echo $row['nama_pelanggan']; ?>. Lanjutkan?');">
                                            Kirim Notifikasi
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No. Telepon tidak tersedia</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="modalTransaksi" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Transaksi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" id="formTransaksi">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pelanggan</label>
                                <select name="id_pelanggan" class="form-select" required>
                                    <option value="">Pilih Pelanggan</option>
                                    <?php while ($p = mysqli_fetch_assoc($q_pelanggan)): ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo $p['nama']; ?> - <?php echo $p['telepon']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <h6>Detail Layanan:</h6>
                    <div id="layananContainer">
                        <div class="row layanan-row">
                            <div class="col-md-5">
                                <select name="layanan_id[]" class="form-select layanan-select">
                                    <option value="">Pilih Layanan</option>
                                    <?php 
                                    mysqli_data_seek($q_layanan, 0);
                                    while ($l = mysqli_fetch_assoc($q_layanan)): 
                                    ?>
                                    <option value="<?php echo $l['id']; ?>" data-harga="<?php echo $l['harga']; ?>">
                                        <?php echo $l['nama_layanan']; ?> - Rp <?php echo number_format($l['harga'], 0, ',', '.'); ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="jumlah[]" class="form-control jumlah-input" placeholder="Jumlah" min="1">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control subtotal-display" placeholder="Subtotal" readonly>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm hapus-layanan">X</button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="tambahLayanan()">
                        + Tambah Layanan
                    </button>
                    
                    <div class="mt-3">
                        <h5>Total: <span id="totalDisplay">Rp 0</span></h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content akan diisi via AJAX -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function tambahLayanan() {
    const container = document.getElementById('layananContainer');
    const newRow = container.querySelector('.layanan-row').cloneNode(true);
    
    // Reset values
    newRow.querySelector('.layanan-select').value = '';
    newRow.querySelector('.jumlah-input').value = '';
    newRow.querySelector('.subtotal-display').value = '';
    
    // Add event listeners
    addLayananEventListeners(newRow);
    
    container.appendChild(newRow);
}

function addLayananEventListeners(row) {
    const layananSelect = row.querySelector('.layanan-select');
    const jumlahInput = row.querySelector('.jumlah-input');
    const subtotalDisplay = row.querySelector('.subtotal-display');
    const hapusBtn = row.querySelector('.hapus-layanan');
    
    layananSelect.addEventListener('change', updateSubtotal);
    jumlahInput.addEventListener('input', updateSubtotal);
    
    hapusBtn.addEventListener('click', function() {
        if (document.querySelectorAll('.layanan-row').length > 1) {
            row.remove();
            updateTotal();
        }
    });
    
    function updateSubtotal() {
        const selectedOption = layananSelect.options[layananSelect.selectedIndex];
        const harga = selectedOption.dataset.harga || 0;
        const jumlah = jumlahInput.value || 0;
        const subtotal = harga * jumlah;
        
        subtotalDisplay.value = 'Rp ' + subtotal.toLocaleString('id-ID');
        updateTotal();
    }
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.layanan-row').forEach(row => {
        const layananSelect = row.querySelector('.layanan-select');
        const jumlahInput = row.querySelector('.jumlah-input');
        
        const selectedOption = layananSelect.options[layananSelect.selectedIndex];
        const harga = selectedOption.dataset.harga || 0;
        const jumlah = jumlahInput.value || 0;
        
        total += harga * jumlah;
    });
    
    document.getElementById('totalDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function lihatDetail(id) {
    // Implementasi untuk melihat detail transaksi
    // Bisa menggunakan AJAX atau redirect ke halaman detail
    alert('Detail transaksi #' + id + ' akan ditampilkan di sini');
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.layanan-row').forEach(addLayananEventListeners);
});
</script>
</body>
</html> 