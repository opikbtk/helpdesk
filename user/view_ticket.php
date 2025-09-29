<?php
include '../includes/database.php';
include '../includes/functions.php';
check_login();

if (isset($_GET["id"])) {
    $ticket_id = (int)$_GET["id"];

    // Ambil data tiket
    $sql = "SELECT * FROM tickets WHERE id = $ticket_id";
    $result = $conn->query($sql);
    $ticket = $result->fetch_assoc();
    if (!$ticket) {
        header("Location: my_tickets.php");
        exit();
    }

    // Ambil balasan pada tiket
    $sql = "SELECT r.*, u.nama AS nama_pengirim
            FROM replies r
            JOIN users u ON r.user_id = u.id
            WHERE r.ticket_id = $ticket_id
            ORDER BY r.created_at ASC";
    $replies_result = $conn->query($sql);

    // Tambah balasan (jika ada post data) dan tiket tidak closed
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $message = trim($_POST["message"] ?? '');
        if ($ticket['status'] === 'closed') {
            $error = "Tiket sudah ditutup, Anda tidak bisa menambahkan balasan.";
        } elseif (!empty($message)) {
            $user_id = $_SESSION["user_id"];
            $sql = "INSERT INTO replies (ticket_id, user_id, message, created_at)
                    VALUES ($ticket_id, $user_id, '$message', NOW())";
            if ($conn->query($sql) === TRUE) {
                header("Location: view_ticket.php?id=$ticket_id");
                exit();
            } else {
                $error = "Terjadi kesalahan saat menyimpan balasan.";
            }
        } else {
            $error = "Pesan tidak boleh kosong.";
        }
    }
} else {
    header("Location: my_tickets.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lihat Tiket</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="../css/viewtiket.css">
</head>
<body>
  <div class="topbar">
    <div class="brand"><i class="fa-solid fa-user"></i> Helpdesk User</div>
    <nav>
      <button type="button" class="btn btn-outline" onclick="toggleTheme()"><i class="fa-solid fa-moon"></i> Tema</button>
      <a href="my_tickets.php" class="btn btn-outline"><i class="fa-solid fa-list"></i> Tiket Saya</a>
      <a href="index.php" class="btn btn-outline"><i class="fa-solid fa-home"></i> Dashboard</a>
      <a href="../logout.php" class="btn btn-outline"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </div>

  <div class="container">
    <nav class="breadcrumb"><a href="my_tickets.php"><i class="fa-solid fa-list"></i> Tiket Saya</a> <span>/</span> <span>#<?php echo $ticket['id']; ?></span></nav>

    <div class="card">
      <h1><?php echo htmlspecialchars($ticket['subject']); ?></h1>
      <p class="subtitle">Detail tiket dan percakapan.</p>
      <div class="meta-grid">
        <div><span class="chip">Status</span> <span class="badge badge-<?php echo $ticket['status']; ?>"><?php echo ucfirst($ticket['status']); ?></span></div>
        <div><span class="chip">Dibuat</span> <?php echo date('d M Y, H:i', strtotime($ticket['created_at'])); ?></div>
      </div>
      <div class="detail">
        <p><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
      </div>
    </div>

    <div class="card">
      <h2><i class="fa-solid fa-comments"></i> Balasan</h2>
      <div class="replies">
      <?php if ($replies_result->num_rows > 0): ?>
        <?php while ($reply = $replies_result->fetch_assoc()): ?>
          <div class="reply-item">
            <div class="reply-dot"></div>
            <div class="reply-content">
              <div class="meta"><strong><?php echo htmlspecialchars($reply['nama_pengirim']); ?></strong> · <?php echo date('d M Y, H:i', strtotime($reply['created_at'])); ?></div>
              <div class="message"><?php echo nl2br(htmlspecialchars($reply['message'])); ?></div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="subtitle">Belum ada balasan pada tiket ini.</p>
      <?php endif; ?>
      </div>
    </div>

    <div class="card">
      <h2><i class="fa-solid fa-pen-to-square"></i> Tambahkan Balasan</h2>
      <?php if ($ticket['status'] === 'closed'): ?>
        <p class="subtitle">Tiket ini sudah ditutup. Anda tidak bisa menambahkan balasan.</p>
      <?php else: ?>
        <?php if (isset($error)): ?>
          <p class="subtitle" style="color:#b91c1c;">&nbsp;<?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $ticket_id); ?>">
          <div class="form-group">
            <label for="message">Pesan</label>
            <textarea id="message" name="message" placeholder="Tulis balasan Anda..."></textarea>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn"><i class="fa-solid fa-paper-plane"></i> Kirim</button>
            <a href="my_tickets.php" class="btn btn-outline"><i class="fa-solid fa-list"></i> Kembali</a>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>
  <script>
    function toggleTheme(){
      const next = (document.documentElement.getAttribute('data-theme')==='dark')?'light':'dark';
      document.documentElement.setAttribute('data-theme', next);
      localStorage.setItem('appTheme', next);
    }
    (function(){
      const saved = localStorage.getItem('appTheme');
      if(saved) document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>
</body>
</html>