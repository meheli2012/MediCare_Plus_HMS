<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';
$patient_id = $_SESSION['user_id'];

// Get appointment_id from query string
if (!isset($_GET['appointment_id'])) {
    die("Invalid request.");
}
$appointment_id = intval($_GET['appointment_id']);

// Fetch the receipt file for this appointment and patient
$stmt = $pdo->prepare("
    SELECT receipt 
    FROM appointments 
    WHERE id=? AND patient_id=? AND receipt IS NOT NULL
");
$stmt->execute([$appointment_id, $patient_id]);
$receipt = $stmt->fetchColumn();

if (!$receipt || !file_exists("uploads/receipts/$receipt")) {
    die("Receipt not found.");
}

// Determine MIME type for proper display
$ext = strtolower(pathinfo($receipt, PATHINFO_EXTENSION));
$mime = '';
switch($ext){
    case 'pdf': $mime = 'application/pdf'; break;
    case 'jpg': case 'jpeg': $mime = 'image/jpeg'; break;
    case 'png': $mime = 'image/png'; break;
    default: $mime = 'application/octet-stream';
}

// Output headers and file
header("Content-Type: $mime");
header("Content-Disposition: inline; filename=\"$receipt\"");
header("Content-Length: " . filesize("uploads/receipts/$receipt"));
readfile("uploads/receipts/$receipt");
exit;