<?php
$page_title = 'Edit User';
include '../includes/database.php';

session_start();
$_SESSION["role"] = 'admin';

if ($_SESSION["role"] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$error = '';
$success = '';

// Ambil ID user dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = intval($_GET['id']);

// Ambil data user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: users.php");
    exit();
}

$user = $result->fetch_assoc();
$stmt->close();

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];

    // Validasi
    if (empty($username) || empty($nama) || empty($email) || empty($role)) {
        $error = 'Semua field harus diisi (kecuali password jika tidak ingin diubah)!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } else {
        // Cek apakah username sudah digunakan oleh user lain
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'Username sudah digunakan oleh user lain!';
        } else {
            // Cek apakah email sudah digunakan oleh user lain
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = 'Email sudah digunakan oleh user lain!';
            } else {
                // Update user
                if (!empty($password)) {
                    // Jika password diisi, update dengan password baru
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, nama = ?, email = ?, role = ? WHERE id = ?");
                    $stmt->bind_param("sssssi", $username, $hashed_password, $nama, $email, $role, $user_id);
                } else {
                    // Jika password tidak diisi, update tanpa mengubah password
                    $stmt = $conn->prepare("UPDATE users SET username = ?, nama = ?, email = ?, role = ? WHERE id = ?");
                    $stmt->bind_param("ssssi", $username, $nama, $email, $role, $user_id);
                }

                if ($stmt->execute()) {
                    $success = 'User berhasil diupdate!';
                    // Refresh data user
                    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    // Redirect setelah 2 detik
                    header("refresh:2;url=users.php");
                } else {
                    $error = 'Gagal mengupdate user: ' . $conn->error;
                }
            }
        }
        $stmt->close();
    }
}

include 'header.php';
?>

<h1><i class="fa-solid fa-user-pen"></i> Edit User</h1>

<div class="card">
    <h2><i class="fa-solid fa-pen"></i> Form Edit User</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($success); ?> Mengalihkan ke daftar user...
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="form-container">
        <div class="form-group">
            <label for="username"><i class="fa-solid fa-user"></i> Username:</label>
            <input type="text" id="username" name="username" required
                   value="<?php echo htmlspecialchars($user['username']); ?>">
        </div>

        <div class="form-group">
            <label for="password"><i class="fa-solid fa-lock"></i> Password:</label>
            <input type="password" id="password" name="password" minlength="6">
            <small style="color: var(--text-secondary);">Kosongkan jika tidak ingin mengubah password</small>
        </div>

        <div class="form-group">
            <label for="nama"><i class="fa-solid fa-id-card"></i> Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" required
                   value="<?php echo htmlspecialchars($user['nama']); ?>">
        </div>

        <div class="form-group">
            <label for="email"><i class="fa-solid fa-envelope"></i> Email:</label>
            <input type="email" id="email" name="email" required
                   value="<?php echo htmlspecialchars($user['email']); ?>">
        </div>

        <div class="form-group">
            <label for="role"><i class="fa-solid fa-user-tag"></i> Role:</label>
            <select id="role" name="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                <option value="support" <?php echo ($user['role'] === 'support') ? 'selected' : ''; ?>>Support</option>
                <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-save"></i> Update
            </button>
            <a href="users.php" class="btn btn-secondary">
                <i class="fa-solid fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>

<style>
.form-container {
    max-width: 600px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-primary);
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    background: var(--bg-primary);
    color: var(--text-primary);
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-danger {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

.alert-success {
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(34, 197, 94, 0.3);
    color: #22c55e;
}
</style>

<?php include 'footer.php'; ?>
