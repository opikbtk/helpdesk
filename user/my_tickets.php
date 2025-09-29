<?php
include '../includes/database.php';
include '../includes/functions.php';
check_login();

$user_id = $_SESSION["user_id"];
$sql = "SELECT * FROM tickets WHERE user_id = $user_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tiket Saya</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="../css/my_ticket.css">
</head>
<body>
  <div class="topbar">
    <div class="brand"><i class="fa-solid fa-user"></i> Helpdesk User</div>
    <nav>
      <button type="button" class="btn btn-outline" onclick="toggleTheme()"><i class="fa-solid fa-moon"></i> Tema</button>
      <a href="create_ticket.php" class="btn"><i class="fa-solid fa-plus"></i> Buat Tiket</a>
      <a href="index.php" class="btn btn-outline"><i class="fa-solid fa-home"></i> Dashboard</a>
      <a href="../logout.php" class="btn btn-outline"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
  </div>

  <div class="container">
    <div class="card">
      <h1>Tiket Saya</h1>
      <p class="subtitle">Lihat daftar tiket yang pernah Anda buat dan statusnya.</p>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Subjek</th>
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
              <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
              <td><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
              <td class="actions"><a href="view_ticket.php?id=<?php echo $row['id']; ?>"><i class="fa-solid fa-eye"></i> Lihat</a></td>
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