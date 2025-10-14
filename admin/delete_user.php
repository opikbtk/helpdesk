<?php
include '../includes/database.php';

session_start();
$_SESSION["role"] = 'admin';

if ($_SESSION["role"] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Ambil ID user dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$user_id = intval($_GET['id']);

// Cek apakah user ada
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

// Cegah admin menghapus diri sendiri (opsional, sesuaikan dengan kebutuhan)
// if ($user_id === $_SESSION['user_id']) {
//     $_SESSION['error'] = 'Anda tidak dapat menghapus akun Anda sendiri!';
//     header("Location: users.php");
//     exit();
// }

// Hapus user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // Berhasil dihapus
    $_SESSION['success'] = 'User "' . htmlspecialchars($user['username']) . '" berhasil dihapus!';
} else {
    // Gagal dihapus
    $_SESSION['error'] = 'Gagal menghapus user: ' . $conn->error;
}

$stmt->close();
$conn->close();

// Redirect kembali ke halaman users
header("Location: users.php");
exit();
?>
