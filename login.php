<?php
session_start();
if (isset($_SESSION['login'])) {
    header('Location: dashboard.php');
    exit;
}

include 'koneksi.php';

$login_msg = '';
$register_msg = '';
$register_success = '';

if ($_POST) {
    if ($_POST['action'] == 'login') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $query = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);
            if (password_verify($password, $data['password'])) {
                $_SESSION['login'] = true;
                $_SESSION['username'] = $data['username'];
                $_SESSION['role'] = $data['role'];
                header('Location: dashboard.php');
                exit;
            } else {
                $login_msg = 'Password salah!';
            }
        } else {
            $login_msg = 'Username tidak ditemukan!';
        }
    } elseif ($_POST['action'] == 'register') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $role = $_POST['role'];
        
        if ($password !== $confirm_password) {
            $register_msg = 'Konfirmasi password tidak cocok!';
        } else {
            $check_user = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
            if (mysqli_num_rows($check_user) > 0) {
                $register_msg = 'Username sudah digunakan!';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert = mysqli_query($conn, "INSERT INTO admin (username, password, role) VALUES ('$username', '$hashed_password', '$role')");
                if ($insert) {
                    $register_success = 'Registrasi berhasil! Silakan login.';
                } else {
                    $register_msg = 'Gagal mendaftar!';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Laundry</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { 
            background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card { 
            border: none; 
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            border-radius: 18px;
        }
        #register-form { display: none; }
        .welcome-header {
            text-align: center;
            margin-bottom: 1rem;
        }
        .welcome-header h4 {
            color: #2d3748;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <!-- Form Login -->
            <div class="card" id="login-form">
                <div class="card-header text-center bg-primary text-white">
                    <div class="welcome-header">
                        <h4>Login Laundry Family Group</h4>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if($login_msg): ?><div class="alert alert-danger p-2 text-center"><?php echo $login_msg; ?></div><?php endif; ?>
                    <?php if($register_success): ?><div class="alert alert-success p-2 text-center"><?php echo $register_success; ?></div><?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="#" id="show-register-form">Buat akun admin baru</a>
                    </div>
                </div>
            </div>

            <!-- Form Registrasi -->
            <div class="card" id="register-form">
                <div class="card-header text-center bg-primary text-white">
                    <div class="welcome-header">
                        <h4>Registrasi Admin Baru</h4>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if($register_msg): ?><div class="alert alert-danger p-2 text-center"><?php echo $register_msg; ?></div><?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="action" value="register">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="register_password" class="form-control" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('register_password', this)">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" name="confirm_password" id="register_confirm_password" class="form-control" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('register_confirm_password', this)">
                                    <i class="bi bi-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="karyawan">Karyawan</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Daftar</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="#" id="show-login-form">Sudah punya akun? Login di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId, button) {
    const passwordField = document.getElementById(fieldId);
    const icon = button.querySelector('i');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    }
}

document.getElementById('show-register-form').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('login-form').style.display = 'none';
    document.getElementById('register-form').style.display = 'block';
});

document.getElementById('show-login-form').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('register-form').style.display = 'none';
    document.getElementById('login-form').style.display = 'block';
});

// Jika ada error pada form registrasi, tampilkan form registrasi secara default
<?php if($register_msg): ?>
document.getElementById('login-form').style.display = 'none';
document.getElementById('register-form').style.display = 'block';
<?php endif; ?>
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 