<?php
session_start();

// Redirect to login if user is not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';
$admin_id = $_SESSION['user_id'];

// Fetch admin info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch counts
$doctorCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'doctor'")->fetchColumn();
$patientCount = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'patient'")->fetchColumn();
$pendingReceiptCount = $pdo->query("SELECT COUNT(*) FROM bills WHERE receipt IS NULL OR receipt = ''")->fetchColumn();
$feedbackCount = $pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn();

// Fetch doctors and patients
$doctors = $pdo->query("SELECT * FROM users WHERE role = 'doctor' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$patients = $pdo->query("SELECT * FROM users WHERE role = 'patient' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - MediCare Plus</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        neon: '#38bdf8',
        deep: '#020617',
        glowpink: '#ff5c8d',
        glowblue: '#38bdf8',
        danger: '#e60000',
        success: '#33cc66',
        warning: '#ff9933'
      },
      boxShadow: {
        glow: '0 0 25px rgba(56,189,248,0.7)'
      }
    }
  }
}
</script>
</head>
<body class="bg-gradient-to-br from-deep via-slate-900 to-blue-950 text-white min-h-screen">

<!-- Header -->
<div class="text-center py-12">
    <div class="mx-auto w-28 h-28 bg-gradient-to-tr from-glowpink to-glowblue rounded-full flex items-center justify-center shadow-glow">
        <span class="text-5xl font-bold text-white">+</span>
    </div>
    <h1 class="text-5xl mt-4 font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-glowblue to-glowpink">MediCare Plus</h1>
    <p class="text-slate-300 mt-2">Welcome, <?= htmlspecialchars($admin['fullname']) ?></p>
</div>

<!-- Dashboard Cards -->
<div class="max-w-7xl mx-auto grid sm:grid-cols-2 md:grid-cols-4 gap-8 px-6 mb-12">
    <div class="bg-gradient-to-r from-glowpink to-glowblue p-6 rounded-3xl shadow-glow hover:scale-105 transition cursor-pointer text-center">
        <h3 class="text-2xl font-bold mb-2">Doctors</h3>
        <p class="text-slate-200 mb-4"><?= $doctorCount ?> total</p>
        <a href="manage_doctors.php" class="inline-block px-6 py-2 rounded-full bg-white text-glowblue font-bold hover:shadow-glow transition">Manage</a>
    </div>
    <div class="bg-gradient-to-r from-glowblue to-glowpink p-6 rounded-3xl shadow-glow hover:scale-105 transition cursor-pointer text-center">
        <h3 class="text-2xl font-bold mb-2">Patients</h3>
        <p class="text-slate-200 mb-4"><?= $patientCount ?> total</p>
        <a href="#" class="inline-block px-6 py-2 rounded-full bg-white text-glowpink font-bold hover:shadow-glow transition">View</a>
    </div>
    <div class="bg-gradient-to-r from-success to-green-500 p-6 rounded-3xl shadow-glow hover:scale-105 transition cursor-pointer text-center">
        <h3 class="text-2xl font-bold mb-2">Pending Bills</h3>
        <p class="text-slate-200 mb-4"><?= $pendingReceiptCount > 0 ? "$pendingReceiptCount missing receipts" : "All receipts submitted" ?></p>
        <a href="admin_receipts.php" class="inline-block px-6 py-2 rounded-full bg-white text-success font-bold hover:shadow-glow transition">View</a>
    </div>
    <div class="bg-gradient-to-r from-warning to-orange-500 p-6 rounded-3xl shadow-glow hover:scale-105 transition cursor-pointer text-center">
        <h3 class="text-2xl font-bold mb-2">Feedback</h3>
        <p class="text-slate-200 mb-4"><?= $feedbackCount > 0 ? "$feedbackCount new feedbacks" : "No feedback yet" ?></p>
        <a href="feedback.php" class="inline-block px-6 py-2 rounded-full bg-white text-warning font-bold hover:shadow-glow transition">View</a>
    </div>
</div>

<!-- Doctors Table -->
<div class="max-w-7xl mx-auto bg-white/10 backdrop-blur-lg rounded-3xl shadow-glow overflow-x-auto mb-12 px-6 py-6">
    <h2 class="text-2xl font-bold mb-4 text-glowblue">Doctors List</h2>
    <table class="min-w-full text-white border-collapse">
        <thead class="bg-gradient-to-r from-glowpink to-glowblue">
            <tr>
                <th class="p-3 text-left">ID</th>
                <th class="p-3 text-left">Full Name</th>
                <th class="p-3 text-left">Email</th>
                <th class="p-3 text-left">Role</th>
                <th class="p-3 text-left">Specialization</th>
                <th class="p-3 text-left">Experience</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($doctors as $d): ?>
            <tr class="hover:bg-white/20 transition">
                <td class="p-3"><?= $d['id'] ?></td>
                <td class="p-3"><?= htmlspecialchars($d['fullname']) ?></td>
                <td class="p-3"><?= htmlspecialchars($d['email']) ?></td>
                <td class="p-3"><?= $d['role'] ?></td>
                <td class="p-3"><?= htmlspecialchars($d['specialization'] ?? '-') ?></td>
                <td class="p-3"><?= htmlspecialchars($d['experience'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Patients Table -->
<div class="max-w-7xl mx-auto bg-white/10 backdrop-blur-lg rounded-3xl shadow-glow overflow-x-auto mb-12 px-6 py-6">
    <h2 class="text-2xl font-bold mb-4 text-glowpink">Patients List</h2>
    <table class="min-w-full text-white border-collapse">
        <thead class="bg-gradient-to-r from-glowblue to-glowpink">
            <tr>
                <th class="p-3 text-left">ID</th>
                <th class="p-3 text-left">Full Name</th>
                <th class="p-3 text-left">Email</th>
                <th class="p-3 text-left">Role</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($patients as $p): ?>
            <tr class="hover:bg-white/20 transition">
                <td class="p-3"><?= $p['id'] ?></td>
                <td class="p-3"><?= htmlspecialchars($p['fullname']) ?></td>
                <td class="p-3"><?= htmlspecialchars($p['email']) ?></td>
                <td class="p-3"><?= $p['role'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Logout -->
<div class="text-center mb-12">
    <a href="logout.php" class="px-8 py-3 bg-red-700 rounded-full font-bold hover:scale-105 transition inline-block">Logout</a>
</div>

</body>
</html>
