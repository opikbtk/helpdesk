<?php
// Di masa depan, Anda bisa aktifkan session check ini
// session_start();
// if(!isset($_SESSION["role"]) || $_SESSION["role"] != 'admin'){
//     header("Location: ../index.php");
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?> - Helpdesk Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3><i class="fa-solid fa-headset"></i> Helpdesk</h3>
            </div>
            <ul class="sidebar-menu">
                <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
                <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a></li>
                <li><a href="tickets.php" class="<?php echo ($current_page == 'tickets.php' || $current_page == 'view_ticket.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-ticket"></i> Kelola Tiket
                </a></li>
                <li><a href="users.php" class="<?php echo ($current_page == 'users.php' || $current_page == 'add_user.php' || $current_page == 'edit_user.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i> Kelola User
                </a></li>
                <li><a href="reports.php" class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-chart-pie"></i> Laporan
                </a></li>
                <li><a href="../logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <div class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <div class="user-info">
                    <span><i class="fa-solid fa-user-shield"></i> Selamat Datang, <strong>Admin</strong></span>
                </div>
            </header>
            
            <div class="content-body">
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-bottom:15px;">
                    <button class="btn" onclick="toggleTheme()"><i class="fa-solid fa-moon"></i> Tema</button>
                </div>
