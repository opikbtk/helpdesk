<?php
$page_title = 'Dashboard';
// Baris ini nantinya bisa Anda ganti dengan koneksi database asli
// include '../includes/database.php'; 

// Data dummy untuk demonstrasi
$total_tickets = 125;
$open_tickets = 15;
$pending_tickets = 5;
$closed_tickets = 105;

$recent_tickets = [
    ['id' => 201, 'subject' => 'Tidak bisa login ke aplikasi', 'nama_user' => 'Andi', 'status' => 'open', 'created_at' => '2025-09-29 10:30:00'],
    ['id' => 200, 'subject' => 'Printer kantor rusak', 'nama_user' => 'Budi', 'status' => 'closed', 'created_at' => '2025-09-28 15:00:00'],
    ['id' => 199, 'subject' => 'Request software baru', 'nama_user' => 'Citra', 'status' => 'pending', 'created_at' => '2025-09-28 11:45:00'],
    ['id' => 198, 'subject' => 'Koneksi internet lambat', 'nama_user' => 'Dewi', 'status' => 'closed', 'created_at' => '2025-09-27 09:00:00'],
];

// Anda bisa menghapus data dummy di atas dan uncomment kode di bawah ini
// untuk mengambil data dari database asli
/*
session_start();
if ($_SESSION["role"] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Total Tickets
$total_tickets = $conn->query("SELECT COUNT(id) as count FROM tickets")->fetch_assoc()['count'];
$open_tickets = $conn->query("SELECT COUNT(id) as count FROM tickets WHERE status='open'")->fetch_assoc()['count'];
$pending_tickets = $conn->query("SELECT COUNT(id) as count FROM tickets WHERE status='pending'")->fetch_assoc()['count'];
$closed_tickets = $conn->query("SELECT COUNT(id) as count FROM tickets WHERE status='closed'")->fetch_assoc()['count'];

// Recent Tickets
$result_recent = $conn->query("SELECT t.*, u.nama AS nama_user FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT 5");
$recent_tickets = [];
while($row = $result_recent->fetch_assoc()) {
    $recent_tickets[] = $row;
}
*/

include 'header.php';
?>

<h1><i class="fa-solid fa-chart-line"></i> Dashboard</h1>
<p class="subtitle">Selamat datang kembali, Admin. Berikut adalah ringkasan aktivitas helpdesk.</p>

<!-- Stat Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon icon-total">
            <i class="fa-solid fa-inbox"></i>
        </div>
        <div>
            <div class="stat-value"><?php echo $total_tickets; ?></div>
            <div class="stat-label">Total Tiket</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-open">
            <i class="fa-solid fa-envelope-open"></i>
        </div>
        <div>
            <div class="stat-value"><?php echo $open_tickets; ?></div>
            <div class="stat-label">Tiket Terbuka</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-pending">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div>
            <div class="stat-value"><?php echo $pending_tickets; ?></div>
            <div class="stat-label">Tiket Pending</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon icon-closed">
            <i class="fa-solid fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-value"><?php echo $closed_tickets; ?></div>
            <div class="stat-label">Tiket Selesai</div>
        </div>
    </div>
</div>

<!-- Recent Tickets -->
<div class="card">
    <div class="filter-controls">
        <strong>Filter:</strong>
        <button class="filter-btn active" data-filter="all">Semua</button>
        <button class="filter-btn" data-filter="open">Open</button>
        <button class="filter-btn" data-filter="pending">Pending</button>
        <button class="filter-btn" data-filter="closed">Closed</button>
    </div>
    <h2><i class="fa-solid fa-history"></i> Tiket Terbaru</h2>
    <div class="table-container">
        <table class="content-table" id="recentTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subjek</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recent_tickets)): ?>
                    <?php foreach ($recent_tickets as $row): ?>
                        <tr data-status="<?php echo $row['status']; ?>">
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama_user']); ?></td>
                            <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                            <td><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                            <td class="actions">
                                <a href="view_ticket.php?id=<?php echo $row['id']; ?>" class="action-link"><i class="fa-solid fa-eye"></i> Lihat</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">Tidak ada tiket terbaru.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Simple client-side filter
(function(){
  const buttons = document.querySelectorAll('.filter-btn');
  const rows = () => Array.from(document.querySelectorAll('#recentTable tbody tr'));
  buttons.forEach(btn => btn.addEventListener('click', function(){
    buttons.forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const f = this.getAttribute('data-filter');
    rows().forEach(tr => {
      const st = tr.getAttribute('data-status');
      tr.style.display = (f === 'all' || st === f) ? '' : 'none';
    });
  }));
})();
</script>

<?php include 'footer.php'; ?>
