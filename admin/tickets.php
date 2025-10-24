<?php
$page_title = 'Kelola Tiket';
include '../includes/database.php';

session_start();
$_SESSION["role"] = 'admin'; // Hapus ini jika sudah production

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "SELECT t.*, u.nama AS nama_user 
        FROM tickets t JOIN users u ON t.user_id = u.id";
if ($filter_status && in_array($filter_status, ['open', 'pending', 'closed'])) {
    $sql .= " WHERE t.status = ?";
}
$sql .= " ORDER BY t.created_at DESC";

$stmt = $conn->prepare($sql);

if ($filter_status) {
    $stmt->bind_param("s", $filter_status);
}

$stmt->execute();
$result = $stmt->get_result();


include 'header.php';
?>

<h1><i class="fa-solid fa-ticket"></i> Kelola Tiket</h1>
<p class="subtitle">Lihat, kelola, dan respon semua tiket dukungan pelanggan di sini.</p>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="filter-controls">
        <strong><i class="fa-solid fa-filter"></i> Filter Status:</strong>
        <a href="tickets.php" class="filter-btn <?php echo empty($filter_status) ? 'active' : ''; ?>">Semua</a>
        <a href="tickets.php?status=open" class="filter-btn <?php echo $filter_status == 'open' ? 'active' : ''; ?>">Open</a>
        <a href="tickets.php?status=pending" class="filter-btn <?php echo $filter_status == 'pending' ? 'active' : ''; ?>">Pending</a>
        <a href="tickets.php?status=closed" class="filter-btn <?php echo $filter_status == 'closed' ? 'active' : ''; ?>">Closed</a>
    </div>

    <div class="table-container">
        <table class="content-table">
            <thead>
                <tr>
                    <th>ID</th> 
                    <th>Subjek</th> 
                    <th>User</th> 
                    <th>Status</th> 
                    <th>Dibuat Pada</th> 
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                            <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                            <td><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                            <td class="actions">
                                <a href="view_ticket.php?id=<?php echo $row['id']; ?>" class="action-link"><i class="fa-solid fa-eye"></i> Lihat</a>
                                <a href="delete_ticket.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Anda yakin ingin menghapus tiket ini?')" class="action-link" style="color:var(--danger-color);"><i class="fa-solid fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center; padding: 20px;">Tidak ada tiket yang cocok dengan filter ini.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-danger {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #ef4444;
}

.alert-success {
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(34, 197, 94, 0.3);
    color: #22c55e;
}
</style>

<?php include 'footer.php'; ?>
