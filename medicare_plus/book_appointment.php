<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient'){
    header("Location: login.php");
    exit;
}
include 'includes/db.php';
$patient_id = $_SESSION['user_id'];

// Fetch doctors
$doctors = $pdo->prepare("SELECT * FROM users WHERE role='doctor'");
$doctors->execute();
$doctors = $doctors->fetchAll();

// Fetch services
$services = $pdo->prepare("SELECT * FROM services");
$services->execute();
$services = $services->fetchAll();

// Handle form submission
$success = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $doctor_id = $_POST['doctor_id'];
    $service_id = $_POST['service_id'];
    $appointment_date = $_POST['appointment_date'];

    $stmt = $pdo->prepare("INSERT INTO appointments 
        (patient_id, doctor_id, service_id, appointment_date, status) 
        VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->execute([$patient_id, $doctor_id, $service_id, $appointment_date]);
    $success = "Appointment booked successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book Appointment - Medicare Plus</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 via-indigo-900 to-purple-900 min-h-screen flex items-center justify-center font-sans">

<div class="bg-white/10 backdrop-blur-lg rounded-3xl shadow-2xl w-full max-w-md p-8 space-y-6">
    <h2 class="text-3xl font-extrabold text-center text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-blue-400">
        Book an Appointment
    </h2>

    <?php if($success): ?>
    <div class="text-center text-green-400 font-semibold p-2 bg-green-900/30 rounded-lg">
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <form method="post" class="space-y-4">

        <div>
            <label class="block font-medium mb-1 text-white">Choose Doctor:</label>
            <select name="doctor_id" required class="w-full p-3 rounded-lg text-black focus:ring-2 focus:ring-pink-400">
                <option value="">Select Doctor</option>
                <?php foreach($doctors as $d): ?>
                    <option value="<?= $d['id'] ?>">Dr. <?= htmlspecialchars($d['fullname']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block font-medium mb-1 text-white">Choose Service:</label>
            <select name="service_id" required class="w-full p-3 rounded-lg text-black focus:ring-2 focus:ring-blue-400">
                <option value="">Select Service</option>
                <?php foreach($services as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block font-medium mb-1 text-white">Appointment Date & Time:</label>
            <input type="datetime-local" name="appointment_date" required
                   class="w-full p-3 rounded-lg text-black focus:ring-2 focus:ring-purple-400">
        </div>

        <button type="submit" class="w-full py-3 mt-4 bg-pink-500 hover:bg-pink-600 rounded-xl font-bold text-lg transition-all shadow-lg hover:shadow-pink-400/50">
            Book Appointment
        </button>
    </form>

    <a href="dashboard_patient.php" class="block mt-4 text-center py-3 bg-white/20 rounded-xl font-semibold text-white hover:bg-white/40 transition">
        ← Back to Dashboard
    </a>
</div>

</body>
</html>
