<?php
include '../includes/database.php';
include '../includes/functions.php';
check_login();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = $_POST["subject"];
    $description = $_POST["description"];
    $user_id = $_SESSION["user_id"];

    $sql = "INSERT INTO tickets (user_id, subject, description, status, created_at)
            VALUES ($user_id, '$subject', '$description', 'open', NOW())";

    if ($conn->query($sql) === TRUE) {
        $success = "Tiket berhasil dibuat!";
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buat Tiket Baru</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="../css/create_tiket.css">
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
    <div class="card">
      <h1>Buat Tiket Baru</h1>
      <?php if (isset($error)): ?>
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <?php if (isset($success)): ?>
        <div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
          <label for="subject">Subjek</label>
          <input type="text" id="subject" name="subject" required>
        </div>
        <div class="form-group">
          <label for="description">Deskripsi</label>
          <textarea id="description" name="description" required></textarea>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn"><i class="fa-solid fa-paper-plane"></i> Submit</button>
          <a href="my_tickets.php" class="btn btn-outline"><i class="fa-solid fa-list"></i> Tiket Saya</a>
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