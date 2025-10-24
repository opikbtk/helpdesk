<?php
include '../includes/database.php';

session_start();

// Pastikan hanya admin yang bisa menghapus tiket
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Validasi parameter ID tiket
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: tickets.php");
    exit();
}

$ticket_id = intval($_GET['id']);

// Cek apakah tiket ada
$stmt = $conn->prepare("SELECT subject FROM tickets WHERE id = ?");
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Tiket tidak ditemukan
    header("Location: tickets.php");
    exit();
}

$ticket = $result->fetch_assoc();
$subject = $ticket['subject'];
$stmt->close();

// Mulai transaksi agar aman jika terjadi kegagalan di salah satu langkah
$conn->begin_transaction();

try {
    // 1. Hapus balasan (replies) terkait tiket ini
    $stmt_replies = $conn->prepare("DELETE FROM replies WHERE ticket_id = ?");
    $stmt_replies->bind_param("i", $ticket_id);
    $stmt_replies->execute();
    $stmt_replies->close();

    // 2. Hapus tiket itu sendiri
    $stmt_ticket = $conn->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt_ticket->bind_param("i", $ticket_id);

    if ($stmt_ticket->execute()) {
        $conn->commit();
        $_SESSION['success'] = 'Tiket #' . htmlspecialchars((string)$ticket_id) . ' ("' . htmlspecialchars($subject) . '") berhasil dihapus!';
    } else {
        $conn->rollback();
        $_SESSION['error'] = 'Gagal menghapus tiket: ' . $stmt_ticket->error;
    }

    $stmt_ticket->close();
} catch (mysqli_sql_exception $e) {
    $conn->rollback();
    $_SESSION['error'] = 'Gagal menghapus tiket karena kesalahan database: ' . $e->getMessage();
}

$conn->close();

// Kembali ke halaman daftar tiket
header("Location: tickets.php");
exit();
?>