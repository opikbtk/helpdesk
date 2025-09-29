<?php
$page_title = 'Detail Tiket';
include '../includes/database.php';

session_start();
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: tickets.php");
    exit();
}
$ticket_id = (int)$_GET['id'];

// Ambil data tiket
$stmt = $conn->prepare("SELECT t.*, u.nama AS nama_user FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();
if (!$ticket) {
    header("Location: tickets.php");
    exit();
}

// Ambil balasan
$stmt = $conn->prepare("SELECT r.*, u.nama AS nama_pengirim FROM replies r JOIN users u ON r.user_id = u.id WHERE r.ticket_id = ? ORDER BY r.created_at ASC");
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$replies = $stmt->get_result();

include 'header.php';
?>

<h1><i class="fa-solid fa-ticket"></i> Detail Tiket #<?php echo $ticket['id']; ?></h1>
<p class="subtitle">Lihat detail tiket dan percakapan.</p>

<div class="card">
  <h2><?php echo htmlspecialchars($ticket['subject']); ?></h2>
  <p><strong>User:</strong> <?php echo htmlspecialchars($ticket['nama_user']); ?></p>
  <p><strong>Status:</strong> <span class="badge badge-<?php echo $ticket['status']; ?>"><?php echo ucfirst($ticket['status']); ?></span></p>
  <p><strong>Dibuat pada:</strong> <?php echo date('d M Y, H:i', strtotime($ticket['created_at'])); ?></p>
  <p><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
</div>

<div class="card">
  <h2><i class="fa-solid fa-comments"></i> Balasan</h2>
  <?php if ($replies->num_rows > 0): ?>
    <?php while ($reply = $replies->fetch_assoc()): ?>
      <div class="reply">
        <div class="meta"><strong><?php echo htmlspecialchars($reply['nama_pengirim']); ?></strong> · <?php echo date('d M Y, H:i', strtotime($reply['created_at'])); ?></div>
        <div class="message"><?php echo nl2br(htmlspecialchars($reply['message'])); ?></div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="subtitle">Belum ada balasan.</p>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
