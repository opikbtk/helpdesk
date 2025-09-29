<?php
include '../includes/database.php';
include '../includes/functions.php';
check_login();

if (isset($_GET["id"])) {
    $ticket_id = $_GET["id"];

    // Ambil data tiket
    $sql = "SELECT t.*, u.nama AS nama_user 
            FROM tickets t
            JOIN users u ON t.user_id = u.id
            WHERE t.id = $ticket_id";
    $result = $conn->query($sql);
    $ticket = $result->fetch_assoc();

    // Ambil balasan pada tiket
    $sql = "SELECT r.*, u.nama AS nama_pengirim
            FROM replies r
            JOIN users u ON r.user_id = u.id
            WHERE r.ticket_id = $ticket_id
            ORDER BY r.created_at ASC";
    $replies_result = $conn->query($sql);

    // Update tiket (jika ada post data)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $status = $_POST["status"];
        $message = $_POST["message"];

        // Update status tiket
        $sql = "UPDATE tickets SET status = '$status' WHERE id = $ticket_id";
        $conn->query($sql);

        // Tambah balasan
        if (!empty($message)) {
            $user_id = $_SESSION["user_id"];
            $sql = "INSERT INTO replies (ticket_id, user_id, message, created_at)
                    VALUES ($ticket_id, $user_id, '$message', NOW())";
            $conn->query($sql);
        }

        // Redirect kembali ke halaman tiket
        header("Location: view_ticket.php?id=$ticket_id");
        exit();
    }

    // --- Kode untuk menampilkan data tiket dipindahkan ke dalam blok if ---

    ?>

    <!DOCTYPE html>
    <html lang="id">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Lihat Tiket (Support)</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
      <link rel="stylesheet" href="../css/support.css">
    </head>
    <body>
      <div class="topbar">
        <div class="brand"><i class="fa-solid fa-headset"></i> Helpdesk Support</div>
        <nav>
          <button type="button" class="btn btn-outline" onclick="toggleTheme()"><i class="fa-solid fa-moon"></i> Tema</button>
          <a href="index.php" class="btn btn-outline"><i class="fa-solid fa-list"></i> Daftar Tiket</a>
          <a href="../logout.php" class="btn btn-outline"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
      </div>

      <div class="container">
        <nav class="breadcrumb"><a href="index.php"><i class="fa-solid fa-list"></i> Daftar Tiket</a> <span>/</span> <span>#<?php echo $ticket['id']; ?></span></nav>

        <div class="card">
          <h1><?php echo htmlspecialchars($ticket['subject']); ?></h1>
          <p class="subtitle">Detail tiket dan percakapan.</p>
          <div class="meta-grid">
            <div><span class="chip">Status</span> <span class="badge badge-<?php echo $ticket['status']; ?>"><?php echo ucfirst($ticket['status']); ?></span></div>
            <div><span class="chip">User</span> <?php echo htmlspecialchars($ticket['nama_user']); ?></div>
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
          <h2><i class="fa-solid fa-pen-to-square"></i> Tindakan</h2>
          <form class="action-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $ticket_id); ?>">
            <div class="form-row">
              <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                  <option value="open" <?php if ($ticket['status'] == 'open') echo 'selected'; ?>>Open</option>
                  <option value="pending" <?php if ($ticket['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                  <option value="closed" <?php if ($ticket['status'] == 'closed') echo 'selected'; ?>>Closed</option>
                </select>
              </div>
              <div class="form-group full">
                <label for="message">Pesan</label>
                <textarea id="message" name="message" placeholder="Tambahkan catatan atau balasan..."></textarea>
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn"><i class="fa-solid fa-paper-plane"></i> Submit</button>
              <a href="index.php" class="btn btn-outline"><i class="fa-solid fa-list"></i> Kembali</a>
            </div>
          </form>
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

    <?php

} else {
    header("Location: index.php");
    exit();
}
?>