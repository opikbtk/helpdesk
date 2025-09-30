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

    ?>

    <!DOCTYPE html>
    <html lang="id">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Tiket #<?php echo $ticket['id']; ?> - Helpdesk Support</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
      <link rel="stylesheet" href="../css/support-modern.css">
    </head>
    <body>
      <!-- Topbar -->
      <div class="topbar">
        <div class="brand">
          <i class="fa-solid fa-headset"></i> Helpdesk Support
        </div>
        <nav>
          <button type="button" class="btn btn-outline" onclick="toggleTheme()">
            <i class="fa-solid fa-moon"></i> <span>Tema</span>
          </button>
          <a href="index.php" class="btn btn-outline">
            <i class="fa-solid fa-list"></i> <span>Daftar Tiket</span>
          </a>
          <a href="../logout.php" class="btn btn-outline">
            <i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span>
          </a>
        </nav>
      </div>

      <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
          <a href="index.php">
            <i class="fa-solid fa-list"></i> Daftar Tiket
          </a> 
          <span>/</span> 
          <span>Tiket #<?php echo $ticket['id']; ?></span>
        </nav>

        <!-- Ticket Detail Card -->
        <div class="card">
          <div class="ticket-header">
            <h1><?php echo htmlspecialchars($ticket['subject']); ?></h1>
            <p class="subtitle">Detail tiket dan percakapan dengan pengguna.</p>
          </div>
          
          <!-- Status Grid -->
          <div class="status-grid">
            <div class="status-item">
              <div class="label">
                <i class="fa-solid fa-tag"></i> Status
              </div>
              <div class="value">
                <span class="badge badge-<?php echo $ticket['status']; ?>">
                  <?php echo ucfirst($ticket['status']); ?>
                </span>
              </div>
            </div>
            <div class="status-item">
              <div class="label">
                <i class="fa-solid fa-user"></i> User
              </div>
              <div class="value">
                <?php echo htmlspecialchars($ticket['nama_user']); ?>
              </div>
            </div>
            <div class="status-item">
              <div class="label">
                <i class="fa-solid fa-calendar"></i> Dibuat
              </div>
              <div class="value">
                <?php echo date('d M Y, H:i', strtotime($ticket['created_at'])); ?>
              </div>
            </div>
          </div>
          
          <!-- Description -->
          <div class="ticket-description">
            <strong style="display: block; margin-bottom: 8px; color: var(--primary);">
              <i class="fa-solid fa-file-lines"></i> Deskripsi:
            </strong>
            <p><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>
          </div>
        </div>

        <!-- Replies Card -->
        <div class="card">
          <div class="replies-header">
            <h2><i class="fa-solid fa-comments"></i> Balasan</h2>
            <?php if ($replies_result->num_rows > 0): ?>
              <span class="replies-count"><?php echo $replies_result->num_rows; ?></span>
            <?php endif; ?>
          </div>
          
          <div class="replies-container">
            <?php if ($replies_result->num_rows > 0): ?>
              <div class="replies">
                <?php while ($reply = $replies_result->fetch_assoc()): ?>
                  <div class="reply-item">
                    <div class="reply-dot"></div>
                    <div class="reply-content">
                      <div class="meta">
                        <strong><?php echo htmlspecialchars($reply['nama_pengirim']); ?></strong> 
                        · 
                        <i class="fa-solid fa-clock"></i>
                        <?php echo date('d M Y, H:i', strtotime($reply['created_at'])); ?>
                      </div>
                      <div class="message">
                        <?php echo nl2br(htmlspecialchars($reply['message'])); ?>
                      </div>
                    </div>
                  </div>
                <?php endwhile; ?>
              </div>
            <?php else: ?>
              <div class="empty-state">
                <i class="fa-solid fa-inbox"></i>
                <p>Belum ada balasan pada tiket ini.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Action Card -->
        <div class="card action-card">
          <h2><i class="fa-solid fa-pen-to-square"></i> Tindakan</h2>
          <p class="subtitle">Update status tiket dan tambahkan balasan.</p>
          
          <form class="action-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $ticket_id); ?>">
            <div class="form-grid">
              <!-- Status Field -->
              <div class="form-field">
                <label for="status">
                  <i class="fa-solid fa-tag"></i> Status Tiket
                </label>
                <select id="status" name="status">
                  <option value="open" <?php if ($ticket['status'] == 'open') echo 'selected'; ?>>
                    Open - Menunggu Tindakan
                  </option>
                  <option value="pending" <?php if ($ticket['status'] == 'pending') echo 'selected'; ?>>
                    Pending - Sedang Diproses
                  </option>
                  <option value="closed" <?php if ($ticket['status'] == 'closed') echo 'selected'; ?>>
                    Closed - Selesai
                  </option>
                </select>
              </div>
              
              <!-- Message Field -->
              <div class="form-field">
                <label for="message">
                  <i class="fa-solid fa-message"></i> Pesan Balasan
                </label>
                <textarea 
                  id="message" 
                  name="message" 
                  placeholder="Tambahkan catatan atau balasan untuk pengguna..."
                ></textarea>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="btn-group" style="margin-top: 20px;">
              <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-paper-plane"></i> Submit Balasan
              </button>
              <a href="index.php" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
              </a>
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