<?php
include '../includes/database.php';

session_start();

// Perhatian: Baris ini harus dihapus di produksi, karena Anda mengatur role secara manual!
// $_SESSION["role"] = 'admin'; 

// Cek session role yang sesungguhnya
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== 'admin') {
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
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // User tidak ditemukan
    header("Location: users.php");
    exit();
}

$user = $result->fetch_assoc();
$username = $user['username'];
$stmt->close();

// Mulai Transaksi untuk memastikan semua query berhasil atau tidak sama sekali
$conn->begin_transaction(); 

try {
    // 1. Hapus Balasan (replies) yang terkait dengan Tiket milik user ini
    // Ini harus dilakukan pertama karena 'replies' bergantung pada 'tickets'.
    $stmt_replies = $conn->prepare("
        DELETE FROM replies 
        WHERE ticket_id IN (SELECT id FROM tickets WHERE user_id = ?)
    ");
    $stmt_replies->bind_param("i", $user_id);
    $stmt_replies->execute();
    $stmt_replies->close();

    // 2. Hapus Tiket (tickets) milik user ini
    // Ini harus dilakukan kedua karena 'tickets' bergantung pada 'users'.
    $stmt_tickets = $conn->prepare("DELETE FROM tickets WHERE user_id = ?");
    $stmt_tickets->bind_param("i", $user_id);
    $stmt_tickets->execute();
    $stmt_tickets->close();

    // 3. Hapus User itu sendiri
    $stmt_user = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_user->bind_param("i", $user_id);
    
    if ($stmt_user->execute()) {
        $conn->commit(); // Commit transaksi jika semua berhasil
        $_SESSION['success'] = 'User "' . htmlspecialchars($username) . '" dan semua datanya berhasil dihapus!';
    } else {
        $conn->rollback(); // Rollback jika penghapusan user gagal
        $_SESSION['error'] = 'Gagal menghapus user: ' . $stmt_user->error;
    }
    
    $stmt_user->close();
} catch (mysqli_sql_exception $e) {
    $conn->rollback(); // Rollback jika ada error SQL di tengah jalan
    $_SESSION['error'] = 'Gagal menghapus user karena kesalahan database: ' . $e->getMessage();
}

$conn->close();

// Redirect kembali ke halaman users
header("Location: users.php");
exit();
?>