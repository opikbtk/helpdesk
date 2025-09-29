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

<h1><i class="fa-solid fa-chart-pie"></i> Laporan</h1>

<div class="card">
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
                while ($row = $result_status->fetch_assoc()): ?>
                  <tr>
                    <td><span class="badge badge-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                    <td><?php echo $row['jumlah']; ?></td>
                  </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>