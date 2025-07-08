<?php
session_start();
if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}
include 'koneksi.php';

// Proses hapus pelanggan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pelanggan WHERE id=$id");
    header('Location: pelanggan.php');
    exit;
}

// Proses tambah/edit pelanggan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = (int)$_POST['id'];  // Cast to integer for safety
        if ($id > 0) {  // Validate ID
            $nama = mysqli_real_escape_string($conn, $nama);
            $alamat = mysqli_real_escape_string($conn, $alamat);
            $telepon = mysqli_real_escape_string($conn, $telepon);
            
            $query = "UPDATE pelanggan SET nama='$nama', alamat='$alamat', telepon='$telepon' WHERE id=$id";
            if (!mysqli_query($conn, $query)) {
                die("Error updating record: " . mysqli_error($conn));
            }
        }
    } else {
        // Insert
        $nama = mysqli_real_escape_string($conn, $nama);
        $alamat = mysqli_real_escape_string($conn, $alamat);
        $telepon = mysqli_real_escape_string($conn, $telepon);
        mysqli_query($conn, "INSERT INTO pelanggan (nama, alamat, telepon) VALUES ('$nama', '$alamat', '$telepon')");
    }
    header('Location: pelanggan.php');
    exit;
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $q = mysqli_query($conn, "SELECT * FROM pelanggan WHERE id=$id");
    $edit_data = mysqli_fetch_assoc($q);
}

// Pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
if ($search) {
    $where = "WHERE nama LIKE '%$search%' OR telepon LIKE '%$search%'";
}

// Ambil data pelanggan
$q = mysqli_query($conn, "SELECT * FROM pelanggan $where ORDER BY nama");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Pelanggan</title>
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
        <h4 class="mb-0">Data Pelanggan</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPelanggan">
            <i class="bi bi-plus-lg"></i> Tambah Pelanggan
        </button>
    </div>
    <div class="card">
        <div class="card-body">
            <!-- Pencarian -->
            <form method="get" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau telepon..." value="<?php echo $search; ?>">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                    <?php if($search): ?>
                        <a href="pelanggan.php" class="btn btn-outline-danger">Atur Ulang</a>
                    <?php endif; ?>
                </div>
            </form>

            <!-- Tabel Pelanggan -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Telepon</th>
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
                            <td><?php echo $row['nama']; ?></td>
                            <td><?php echo $row['alamat']; ?></td>
                            <td><?php echo $row['telepon']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="editPelanggan(<?php echo $row['id']; ?>, '<?php echo $row['nama']; ?>', '<?php echo $row['alamat']; ?>', '<?php echo $row['telepon']; ?>')">
                                    Edit
                                </button>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                <a href="?hapus=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                                    Hapus
                                </a>
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

<!-- Modal Tambah/Edit Pelanggan -->
<div class="modal fade" id="modalPelanggan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" id="edit_telepon" class="form-control">
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
function editPelanggan(id, nama, alamat, telepon) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_alamat').value = alamat;
    document.getElementById('edit_telepon').value = telepon;
    document.getElementById('modalTitle').textContent = 'Edit Pelanggan';
    
    var modal = new bootstrap.Modal(document.getElementById('modalPelanggan'));
    modal.show();
}

// Reset modal saat dibuka untuk tambah baru
document.getElementById('modalPelanggan').addEventListener('show.bs.modal', function (event) {
    if (!event.relatedTarget || !event.relatedTarget.classList.contains('btn-warning')) {
        document.getElementById('edit_id').value = '';
        document.getElementById('edit_nama').value = '';
        document.getElementById('edit_alamat').value = '';
        document.getElementById('edit_telepon').value = '';
        document.getElementById('modalTitle').textContent = 'Tambah Pelanggan';
    }
});
</script>
</body>
</html> 