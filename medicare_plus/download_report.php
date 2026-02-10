<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php"); 
    exit;
}

include 'includes/db.php';

$report_id = intval($_GET['id'] ?? 0);
if(!$report_id){
    die("Invalid report.");
}

// Fetch report
$stmt = $pdo->prepare("SELECT * FROM patient_reports WHERE id=?");
$stmt->execute([$report_id]);
$report = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$report){
    die("Report not found.");
}

// Access control
if($_SESSION['role'] === 'patient' && $_SESSION['user_id'] != $report['patient_id']){
    die("Unauthorized access.");
}
if($_SESSION['role'] === 'doctor' && $_SESSION['user_id'] != $report['doctor_id']){
    die("Unauthorized access.");
}

// Correct file path from DB
$file = $report['file_path']; // CORRECT

if(!file_exists($file)){
    die("File not found on server: " . $file);
}

// Force download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.basename($file).'"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;
?>