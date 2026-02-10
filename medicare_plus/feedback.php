<?php
session_start();
include 'includes/db.php';

// --------------------
// ENSURE ADMIN IS LOGGED IN
// --------------------
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// --------------------
// FETCH ADMIN INFO
// --------------------
$adminId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// --------------------
// FETCH ALL FEEDBACKS WITH PATIENT & DOCTOR NAMES
// --------------------
$feedbacks = $pdo->query("
    SELECT 
        f.id, f.comment, f.rating, 
        p.fullname AS patient_name, 
        d.fullname AS doctor_name
    FROM feedback f
    JOIN users p ON f.patient_id = p.id
    JOIN users d ON f.doctor_id = d.id
    ORDER BY f.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Count total feedbacks
$totalFeedbacks = count($feedbacks);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Feedbacks</title>
<style>
body {
    margin:0;
    font-family:'Segoe UI', Arial, sans-serif;
    background:#f9f9f9;
}

/* HEADER */
.header {
    text-align:center;
    padding:40px 20px;
    background:white;
    border-bottom:3px solid #e60000;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}
.header h1 {
    margin-top:15px;
    font-size:36px;
    color:#e60000;
}
.header p {
    font-size:18px;
    color:#444;
    margin-top:5px;
}

/* TABLE CONTAINER */
.table-container {
    max-width:1200px;
    margin:40px auto;
    background:white;
    padding:20px;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.1);
}
.table-container h2 {
    margin-bottom:15px;
    color:#e60000;
}
table {
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}
table th, table td {
    padding:12px 10px;
    border-bottom:1px solid #ddd;
    text-align:left;
    word-wrap:break-word;
}
table th {
    background:#e60000;
    color:white;
    font-weight:600;
}
table tr:hover {
    background:#f1f1f1;
}

/* LOGOUT BUTTON */
.logout-button {
    text-align:center;
    margin:40px 0;
}
.logout-button a {
    padding:10px 30px;
    background:#444;
    color:white;
    font-size:18px;
    text-decoration:none;
    border-radius:30px;
}
.logout-button a:hover {
    background:black;
}
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <h1>MediCare Plus - Feedbacks</h1>
    <p>Welcome, <?= htmlspecialchars($admin['fullname']) ?></p>
</div>

<!-- FEEDBACK TABLE -->
<div class="table-container">
    <h2>All Feedbacks (<?= $totalFeedbacks ?>)</h2>
    
    <?php if ($totalFeedbacks > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Comment</th>
                <th>Rating</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($feedbacks as $feedback): ?>
            <tr>
                <td><?= $feedback['id'] ?></td>
                <td><?= htmlspecialchars($feedback['patient_name']) ?></td>
                <td><?= htmlspecialchars($feedback['doctor_name']) ?></td>
                <td><?= htmlspecialchars($feedback['comment']) ?></td>
                <td><?= $feedback['rating'] ?>/5</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No feedback available yet.</p>
    <?php endif; ?>
</div>

<!-- LOGOUT BUTTON -->
<div class="logout-button">
    <a href="logout.php">Logout</a>
</div>

</body>
</html>