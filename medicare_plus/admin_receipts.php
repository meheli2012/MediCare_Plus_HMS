<?php
session_start();

// Ensure only admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';

// Directory where receipt files are stored
$receiptsDirectory = 'uploads/receipts/';

// Fetch all appointments that have receipts
$query = "
    SELECT 
        a.id,
        a.receipt,
        a.appointment_date,
        a.receipt_status,
        u.fullname AS patient_name,
        u.email AS patient_email
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    WHERE a.receipt IS NOT NULL AND a.receipt != ''
    ORDER BY a.appointment_date DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$appointmentsWithReceipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Receipts - MediCare Plus</title>
<style>
/* General Page Styles */
body {
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f0f2f5;
}

/* Header */
.header {
    text-align: center;
    padding: 30px;
    background: #fff;
    border-bottom: 4px solid #e60000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.header h1 {
    margin: 0;
    color: #e60000;
}

/* Container */
.container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
th {
    background: #e60000;
    color: #fff;
    font-weight: 600;
}
tr:hover {
    background: #f9f9f9;
}

/* View Button */
a.view-btn {
    display: inline-block;
    padding: 6px 12px;
    background: #3498db;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
}
a.view-btn:hover {
    background: #21618c;
}

/* Logout / Back Button */
.logout-button {
    text-align: center;
    margin: 30px 0;
}
.logout-button a {
    padding: 12px 30px;
    background: #444;
    color: #fff;
    font-size: 18px;
    text-decoration: none;
    border-radius: 30px;
}
.logout-button a:hover {
    background: #000;
}
</style>
</head>
<body>

<!-- Page Header -->
<div class="header">
    <h1>Patient Receipts</h1>
</div>

<!-- Receipts Table -->
<div class="container">
<?php if (!empty($appointmentsWithReceipts)): ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Patient Name</th>
                <th>Email</th>
                <th>Appointment Date</th>
                <th>Receipt Status</th>
                <th>Receipt</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($appointmentsWithReceipts as $index => $appointment): 
                $receiptFile = $receiptsDirectory . $appointment['receipt'];
                $fileExists = file_exists($receiptFile);
            ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($appointment['patient_name']) ?></td>
                <td><?= htmlspecialchars($appointment['patient_email']) ?></td>
                <td><?= date('d M Y, H:i', strtotime($appointment['appointment_date'])) ?></td>
                <td><?= htmlspecialchars($appointment['receipt_status']) ?></td>
                <td>
                    <?php if ($fileExists): ?>
                        <a href="<?= htmlspecialchars($receiptFile) ?>" target="_blank" class="view-btn">View</a>
                    <?php else: ?>
                        <span style="color:red;font-weight:bold;">Missing</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="text-align:center;color:#555;font-size:18px;">No receipts available.</p>
<?php endif; ?>
</div>

<!-- Back Button -->
<div class="logout-button">
    <a href="dashboard_admin.php">Back to Dashboard</a>
</div>

</body>
</html>