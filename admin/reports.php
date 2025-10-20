<?php
$page_title = 'Laporan';
include '../includes/database.php';

session_start();
$_SESSION["role"] = 'admin';

if ($_SESSION["role"] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

include 'header.php';
?>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white;
        font-size: 10px;
        margin: 0;
        padding: 0;
    }
    
    .card {
        box-shadow: none;
        border: 1px solid #ddd;
        page-break-inside: avoid;
        margin-bottom: 8px;
        padding: 8px;
    }
    
    .sidebar {
        display: none;
    }
    
    .main-content {
        margin-left: 0;
        padding: 8px;
    }
    
    h1 {
        color: #333;
        font-size: 16px;
        margin-bottom: 5px;
        margin-top: 0;
    }
    
    h2 {
        font-size: 13px;
        margin-bottom: 6px;
        margin-top: 0;
    }
    
    .print-header {
        text-align: center;
        margin-bottom: 8px;
        border-bottom: 2px solid #333;
        padding-bottom: 6px;
    }
    
    .print-header h1 {
        margin-bottom: 3px;
        font-size: 18px;
    }
    
    .print-header p {
        margin: 2px 0;
        font-size: 9px;
        line-height: 1.2;
    }
    
    .print-info {
        margin-bottom: 8px;
        font-size: 10px;
        line-height: 1.2;
    }
    
    table {
        font-size: 10px;
        margin-bottom: 0;
    }
    
    table th, table td {
        padding: 4px 6px;
    }
    
    .content-table thead tr {
        background-color: #f8f9fa !important;
    }
    
    .table-container {
        margin-bottom: 0;
    }
    
    /* Optimasi Page Break */
    .card:last-child {
        margin-bottom: 0;
        page-break-after: auto;
    }
    
    /* Hindari orphan/widow */
    * {
        orphans: 3;
        widows: 3;
    }
    
    footer {
        display: none !important;
    }
}

.print-header {
    display: none;
}

@media print {
    .print-header {
        display: block;
    }
}


.btn-print {
    background: #28a745;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-left: 10px;
}

.btn-print:hover {
    background: #218838;
}
</style>

<div class="print-header">
    <h1>LAPORAN HELPDESK SYSTEM</h1>
    <p>© 2025 Helpdesk System. Didesain dengan ❤️</p>
    <p style="margin-top: 5px;"><strong>Kelompok 1:</strong> Mohamad Taufik Wibowo • Fabian Jason Song • Ridwan Abdillah • Reiksa Azra Octavian</p>
</div>

<div class="print-info">
    <strong>Periode Laporan:</strong> <?php echo date('d/m/Y', strtotime($start_date)); ?> - <?php echo date('d/m/Y', strtotime($end_date)); ?><br>
    <strong>Tanggal Cetak:</strong> <?php echo date('d/m/Y H:i:s'); ?>
</div>

<h1 class="no-print"><i class="fa-solid fa-chart-pie"></i> Laporan</h1>

<div class="card no-print">
    <form method="get" action="" style="display:flex; gap: 15px; align-items:center; flex-wrap: wrap; margin-bottom: 20px;">
        <div class="form-group" style="margin-bottom:0;">
            <label for="start_date">Dari Tanggal:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
        </div>
        <div class="form-group" style="margin-bottom:0;">
            <label for="end_date">Sampai Tanggal:</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
        </div>
        <button type="submit" class="btn" style="align-self: flex-end;">Filter</button>
        <button type="button" class="btn-print" onclick="window.print()" style="align-self: flex-end;">
            <i class="fa-solid fa-print"></i> Print Laporan
        </button>
    </form>
</div>

<div class="card">
    <h2>Jumlah Tiket per Status</h2>
    <div class="table-container">
        <table class="content-table">
            <thead>
                <tr><th>Status</th><th>Jumlah</th></tr>
            </thead>
            <tbody>
                <?php 
                $sql_status = "SELECT status, COUNT(*) AS jumlah FROM tickets WHERE created_at BETWEEN ? AND ? GROUP BY status";
                $stmt = $conn->prepare($sql_status);
                $start = $start_date . ' 00:00:00';
                $end = $end_date . ' 23:59:59';
                $stmt->bind_param("ss", $start, $end);
                $stmt->execute();
                $result_status = $stmt->get_result();
                
                $total_tiket = 0;
                $data_status = [];
                while ($row = $result_status->fetch_assoc()) {
                    $data_status[] = $row;
                    $total_tiket += $row['jumlah'];
                }
                
                foreach ($data_status as $row): ?>
                  <tr>
                    <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                    <td><?php echo $row['jumlah']; ?></td>
                  </tr>
                <?php endforeach; ?>
                <tr style="font-weight: bold; background-color: #f8f9fa;">
                    <td>Total</td>
                    <td><?php echo $total_tiket; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
// Query untuk statistik tambahan - Tiket per User
$sql_user = "SELECT u.username, COUNT(*) as jumlah 
             FROM tickets t 
             JOIN users u ON t.user_id = u.id 
             WHERE t.created_at BETWEEN ? AND ? 
             GROUP BY t.user_id, u.username 
             ORDER BY jumlah DESC 
             LIMIT 10";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("ss", $start, $end);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
?>

<div class="card">
    <h2>Top 10 User dengan Tiket Terbanyak</h2>
    <div class="table-container">
        <table class="content-table">
            <thead>
                <tr><th>Username</th><th>Jumlah Tiket</th></tr>
            </thead>
            <tbody>
                <?php 
                $total_user = 0;
                $data_user = [];
                while ($row = $result_user->fetch_assoc()) {
                    $data_user[] = $row;
                    $total_user += $row['jumlah'];
                }
                
                if (count($data_user) > 0) {
                    foreach ($data_user as $row): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo $row['jumlah']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="2" style="text-align: center;">Tidak ada data</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Query untuk statistik tambahan - Ringkasan Periode
$sql_summary = "SELECT 
                COUNT(*) as total_tiket,
                COUNT(DISTINCT user_id) as total_user,
                MIN(created_at) as tiket_pertama,
                MAX(created_at) as tiket_terakhir
                FROM tickets 
                WHERE created_at BETWEEN ? AND ?";
$stmt_summary = $conn->prepare($sql_summary);
$stmt_summary->bind_param("ss", $start, $end);
$stmt_summary->execute();
$result_summary = $stmt_summary->get_result();
$summary = $result_summary->fetch_assoc();
?>

<div class="card">
    <h2>Ringkasan Periode</h2>
    <div class="table-container">
        <table class="content-table">
            <thead>
                <tr><th>Keterangan</th><th>Nilai</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td>Total Tiket</td>
                    <td><?php echo $summary['total_tiket']; ?></td>
                </tr>
                <tr>
                    <td>Total User yang Membuat Tiket</td>
                    <td><?php echo $summary['total_user']; ?></td>
                </tr>
                <tr>
                    <td>Tiket Pertama</td>
                    <td><?php echo $summary['tiket_pertama'] ? date('d/m/Y H:i', strtotime($summary['tiket_pertama'])) : '-'; ?></td>
                </tr>
                <tr>
                    <td>Tiket Terakhir</td>
                    <td><?php echo $summary['tiket_terakhir'] ? date('d/m/Y H:i', strtotime($summary['tiket_terakhir'])) : '-'; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>