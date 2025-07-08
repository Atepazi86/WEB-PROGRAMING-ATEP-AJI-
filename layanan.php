<?php
session_start();
if (!isset($_SESSION['login']) || (isset($_SESSION['role']) && $_SESSION['role'] != 'admin')) {
    header('Location: dashboard.php');
    exit;
}
include 'koneksi.php';

// Proses hapus layanan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM layanan WHERE id=$id");
    header('Location: layanan.php');
    exit;
}

// Proses tambah/edit layanan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_layanan = $_POST['nama_layanan'];
    $harga = $_POST['harga'];
    
    if (isset($_POST['id'])) {
        // Update
        $id = $_POST['id'];
        mysqli_query($conn, "UPDATE layanan SET nama_layanan='$nama_layanan', harga=$harga WHERE id=$id");
    } else {
        // Insert
        mysqli_query($conn, "INSERT INTO layanan (nama_layanan, harga) VALUES ('$nama_layanan', $harga)");
    }
    header('Location: layanan.php');
    exit;
}

// Pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
if ($search) {
    $where = "WHERE nama_layanan LIKE '%$search%'";
}

// Ambil data layanan
$q = mysqli_query($conn, "SELECT * FROM layanan $where ORDER BY nama_layanan");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Layanan</title>
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
        <h4 class="mb-0">Data Layanan Laundry</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLayanan">
            <i class="bi bi-plus-lg"></i> Tambah Layanan
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <!-- Pencarian -->
            <form method="get" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama layanan..." value="<?php echo $search; ?>">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                    <?php if($search): ?>
                        <a href="layanan.php" class="btn btn-outline-danger">Atur Ulang</a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Tabel Layanan -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Layanan</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($q)): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['nama_layanan']; ?></td>
                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editLayanan(<?php echo $row['id']; ?>, '<?php echo $row['nama_layanan']; ?>', <?php echo $row['harga']; ?>)">
                                    Edit
                                </button>
                                <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus layanan ini?')">
                                    Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Layanan -->
<div class="modal fade" id="modalLayanan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Layanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Nama Layanan</label>
                        <input type="text" name="nama_layanan" id="edit_nama_layanan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" id="edit_harga" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editLayanan(id, nama_layanan, harga) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama_layanan').value = nama_layanan;
    document.getElementById('edit_harga').value = harga;
    document.getElementById('modalTitle').textContent = 'Edit Layanan';
    
    var modal = new bootstrap.Modal(document.getElementById('modalLayanan'));
    modal.show();
}

// Reset modal saat dibuka untuk tambah baru
document.getElementById('modalLayanan').addEventListener('show.bs.modal', function (event) {
    if (!event.relatedTarget || !event.relatedTarget.classList.contains('btn-warning')) {
        document.getElementById('edit_id').value = '';
        document.getElementById('edit_nama_layanan').value = '';
        document.getElementById('edit_harga').value = '';
        document.getElementById('modalTitle').textContent = 'Tambah Layanan';
    }
});
</script>
</body>
</html> 