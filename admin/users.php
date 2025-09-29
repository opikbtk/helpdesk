<?php
$page_title = 'Kelola User';
include '../includes/database.php';

session_start();
$_SESSION["role"] = 'admin';

if ($_SESSION["role"] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$result = $conn->query("SELECT * FROM users ORDER BY id ASC");

include 'header.php';
?>

<h1><i class="fa-solid fa-users"></i> Kelola User</h1>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2><i class="fa-solid fa-list"></i> Daftar User</h2>
        <a href="add_user.php" class="btn btn-success"><i class="fa-solid fa-plus"></i> Tambah User</a>
    </div>
    
    <div class="table-container">
        <table class="content-table">
            <thead>
                <tr>
                    <th>ID</th> <th>Username</th> <th>Nama</th> <th>Email</th> <th>Role</th> <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><span class="badge badge-<?php echo $row['role']; ?>"><?php echo ucfirst($row['role']); ?></span></td>
                        <td class="actions">
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>"><i class="fa-solid fa-pen"></i> Edit</a>
                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin?')" style="color:var(--danger-color);"><i class="fa-solid fa-trash"></i> Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>