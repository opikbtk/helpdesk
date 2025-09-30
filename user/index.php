<?php
include '../includes/database.php';
include '../includes/functions.php';
check_login();

$user_id = $_SESSION['user_id'];
// Stats
$total = $conn->query("SELECT COUNT(*) AS c FROM tickets WHERE user_id = $user_id")->fetch_assoc()['c'];
$open = $conn->query("SELECT COUNT(*) AS c FROM tickets WHERE user_id = $user_id AND status='open'")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) AS c FROM tickets WHERE user_id = $user_id AND status='pending'")->fetch_assoc()['c'];
$closed = $conn->query("SELECT COUNT(*) AS c FROM tickets WHERE user_id = $user_id AND status='closed'")->fetch_assoc()['c'];
// Recent
$result_recent = $conn->query("SELECT * FROM tickets WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard User</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="../css/users.css">
  <!-- Modern scoped styles for user dashboard -->
  <link rel="stylesheet" href="../css/user-dashboard.modern.css">
</head>
<body>
  <div class="user-dashboard">
    <div class="topbar">
      <div class="brand"><i class="fa-solid fa-user"></i> Helpdesk User</div>
      <nav>
        <button type="button" class="btn btn-outline" onclick="toggleTheme()"><i class="fa-solid fa-moon"></i> Tema</button>
        <a href="create_ticket.php" class="btn"><i class="fa-solid fa-plus"></i> Buat Tiket</a>
        <a href="my_tickets.php" class="btn btn-outline"><i class="fa-solid fa-list"></i> Tiket Saya</a>
        <a href="../logout.php" class="btn btn-outline"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
      </nav>
    </div>

    <div class="container">
      <div class="card">
        <h1>Selamat datang, <?php echo htmlspecialchars(get_user_name($conn, $user_id)); ?>!</h1>
        <p class="subtitle">Ringkasan tiket Anda dan aktivitas terbaru.</p>
        <div class="stats-grid">
          <div class="stat-card"><div class="stat-icon icon-total"><i class="fa-solid fa-inbox"></i></div><div><div class="stat-value"><?php echo $total; ?></div><div class="stat-label">Total Tiket</div></div></div>
          <div class="stat-card"><div class="stat-icon icon-open"><i class="fa-solid fa-envelope-open"></i></div><div><div class="stat-value"><?php echo $open; ?></div><div class="stat-label">Open</div></div></div>
          <div class="stat-card"><div class="stat-icon icon-pending"><i class="fa-solid fa-clock"></i></div><div><div class="stat-value"><?php echo $pending; ?></div><div class="stat-label">Pending</div></div></div>
          <div class="stat-card"><div class="stat-icon icon-closed"><i class="fa-solid fa-check-circle"></i></div><div><div class="stat-value"><?php echo $closed; ?></div><div class="stat-label">Closed</div></div></div>
        </div>
        <div class="actions">
          <a href="create_ticket.php" class="btn"><i class="fa-solid fa-plus"></i> Buat Tiket Baru</a>
          <a href="my_tickets.php" class="btn btn-outline"><i class="fa-solid fa-list"></i> Lihat Tiket Saya</a>
        </div>
      </div>

      <div class="card">
        <h2><i class="fa-solid fa-history"></i> Tiket Terbaru</h2>
        <div class="table-container">
          <table class="content-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Subjek</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result_recent && $result_recent->num_rows > 0): ?>
                <?php while($row = $result_recent->fetch_assoc()): ?>
                  <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                    <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                    <td><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                    <td class="actions"><a href="view_ticket.php?id=<?php echo $row['id']; ?>"><i class="fa-solid fa-eye"></i> Lihat</a></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">Belum ada tiket.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
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