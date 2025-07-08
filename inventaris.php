<?php
session_start();
if (!isset($_SESSION['login']) || (isset($_SESSION['role']) && $_SESSION['role'] != 'admin')) {
    header('Location: dashboard.php');
    exit;
}
include 'koneksi.php';

// Proses Tambah / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nama_barang = $_POST['nama_barang'];
    $stok = $_POST['stok'];
    $satuan = $_POST['satuan'];
    $keterangan = $_POST['keterangan'];

    if ($id) { // Edit
        $sql = "UPDATE inventaris SET nama_barang='$nama_barang', stok=$stok, satuan='$satuan', keterangan='$keterangan' WHERE id=$id";
    } else { // Tambah
        $sql = "INSERT INTO inventaris (nama_barang, stok, satuan, keterangan) VALUES ('$nama_barang', $stok, '$satuan', '$keterangan')";
    }
    mysqli_query($conn, $sql);
    header('Location: inventaris.php');
    exit;
}

// Proses Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM inventaris WHERE id=$id");
    header('Location: inventaris.php');
    exit;
}

// Ambil data inventaris
$q_inventaris = mysqli_query($conn, "SELECT * FROM inventaris ORDER BY nama_barang");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Inventaris</title>
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
        <h4 class="mb-0">Data Inventaris</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalInventaris">
            <i class="bi bi-plus-lg"></i> Tambah Barang
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($item = mysqli_fetch_assoc($q_inventaris)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                        <td><?php echo $item['stok']; ?></td>
                        <td><?php echo htmlspecialchars($item['satuan']); ?></td>
                        <td><?php echo htmlspecialchars($item['keterangan']); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editInventaris(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                Edit
                            </button>
                            <a href="?hapus=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus barang ini?')">
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

<!-- Modal Tambah/Edit Inventaris -->
<div class="modal fade" id="modalInventaris" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Barang Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="inventaris_id">
                    <div class="mb-3">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" name="nama_barang" id="nama_barang" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" id="stok" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan</label>
                        <input type="text" name="satuan" id="satuan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control"></textarea>
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
const modal = new bootstrap.Modal(document.getElementById('modalInventaris'));
const modalTitle = document.getElementById('modalTitle');
const form = document.querySelector('#modalInventaris form');
const idInput = document.getElementById('inventaris_id');
const namaBarangInput = document.getElementById('nama_barang');
const stokInput = document.getElementById('stok');
const satuanInput = document.getElementById('satuan');
const keteranganInput = document.getElementById('keterangan');

document.getElementById('modalInventaris').addEventListener('hidden.bs.modal', () => {
    form.reset();
    idInput.value = '';
    modalTitle.textContent = 'Tambah Barang Baru';
});

function editInventaris(item) {
    modalTitle.textContent = 'Edit Barang Inventaris';
    idInput.value = item.id;
    namaBarangInput.value = item.nama_barang;
    stokInput.value = item.stok;
    satuanInput.value = item.satuan;
    keteranganInput.value = item.keterangan;
    modal.show();
}
</script>
</body>
</html> 