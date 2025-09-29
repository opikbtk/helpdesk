<?php
include '../includes/database.php';
include '../includes/functions.php';
check_login();

// Ambil semua tiket 
$sql = "SELECT t.*, u.nama AS nama_user
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        ORDER BY t.created_at DESC"; 
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Support</title>
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
      <a href="../logout.php" class="btn btn-outline"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </div>

  <div class="container">
    <div class="card">
      <h1>Selamat datang, <?php echo htmlspecialchars(get_user_name($conn, $_SESSION["user_id"])); ?>!</h1>
      <p class="subtitle">Lihat dan tangani tiket dari pengguna.</p>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Subjek</th>
              <th>User</th>
              <th>Status</th>
              <th>Dibuat pada</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                <td><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                <td class="actions"><a class="btn" href="view_ticket.php?id=<?php echo $row['id']; ?>"><i class="fa-solid fa-eye"></i> Lihat</a></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
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