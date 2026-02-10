<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient'){
    header("Location: login.php");
    exit;
}
include 'includes/db.php';
$patient_id = $_SESSION['user_id'];

// Handle deletion
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM appointments WHERE id=? AND patient_id=?");
    $stmt->execute([$delete_id, $patient_id]);
    header("Location: appointments.php");
    exit;
}

// Fetch all appointments for this patient
$appointments = $pdo->prepare("
    SELECT a.*, u.fullname as doctor_name, s.name as service_name
    FROM appointments a
    JOIN users u ON a.doctor_id = u.id
    JOIN services s ON a.service_id = s.id
    WHERE a.patient_id = ?
    ORDER BY a.appointment_date DESC
");
$appointments->execute([$patient_id]);
$appointments = $appointments->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Appointments - Medicare Plus</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 via-indigo-900 to-purple-900 min-h-screen flex items-center justify-center font-sans p-6">

<div class="w-full max-w-5xl space-y-6">

    <h2 class="text-4xl font-extrabold text-center text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-blue-400">
        My Appointments
    </h2>

    <?php if($appointments): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($appointments as $a): ?>
        <div class="bg-white/10 backdrop-blur-lg p-6 rounded-2xl shadow-lg flex flex-col justify-between hover:scale-105 transition-all duration-300">
            <div>
                <h3 class="text-xl font-bold text-pink-400 mb-2"><?= htmlspecialchars($a['service_name']) ?></h3>
                <p class="text-white/80 font-semibold"><?= date('F j, g:i A', strtotime($a['appointment_date'])) ?></p>
                <p class="text-white/70 mt-2">Doctor: Dr. <?= htmlspecialchars($a['doctor_name']) ?></p>
            </div>
            <a href="appointments.php?delete_id=<?= $a['id'] ?>" 
               onclick="return confirm('Are you sure you want to delete this appointment?')"
               class="mt-4 block text-center py-2 bg-red-500 rounded-xl font-semibold text-white hover:bg-red-600 transition">
               Delete
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-center text-white/80 text-lg mt-6">No appointments found.</p>
    <?php endif; ?>

    <a href="dashboard_patient.php" 
       class="block w-64 mx-auto mt-8 py-3 bg-pink-500 text-white font-bold text-center rounded-xl hover:bg-pink-600 transition">
       ← Back to Dashboard
    </a>
</div>

</body>
</html>
