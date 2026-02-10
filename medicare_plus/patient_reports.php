<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';
$patient_id = intval($_SESSION['user_id']);

// Fetch patient reports
$stmt = $pdo->prepare("
    SELECT pr.*, u.fullname AS doctor_name 
    FROM patient_reports pr
    JOIN users u ON pr.doctor_id = u.id
    WHERE pr.patient_id = ?
    ORDER BY pr.id DESC
");
$stmt->execute([$patient_id]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Health Reports</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-900 via-blue-900 to-purple-900 min-h-screen flex items-center justify-center p-6 font-sans">

<div class="w-full max-w-6xl space-y-6">

    <h2 class="text-4xl font-extrabold text-center text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-blue-400">
        My Health Reports
    </h2>

    <?php if($reports): ?>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white/10 backdrop-blur-lg rounded-2xl overflow-hidden">
            <thead class="bg-pink-500 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Report Name</th>
                    <th class="py-3 px-4 text-left">Description</th>
                    <th class="py-3 px-4 text-left">Report Date</th>
                    <th class="py-3 px-4 text-left">Doctor</th>
                    <th class="py-3 px-4 text-center">Download</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reports as $r): ?>
                <tr class="odd:bg-white/20 even:bg-white/10 hover:bg-white/30 transition">
                    <td class="py-3 px-4"><?= htmlspecialchars($r['report_name']) ?></td>
                    <td class="py-3 px-4 text-sm text-white/80"><?= nl2br(htmlspecialchars($r['description'])) ?: "<i>No description</i>" ?></td>
                    <td class="py-3 px-4"><?= htmlspecialchars(date('F j, Y', strtotime($r['report_date']))) ?></td>
                    <td class="py-3 px-4">Dr. <?= htmlspecialchars($r['doctor_name']) ?></td>
                    <td class="py-3 px-4 text-center">
                        <a href="download_report.php?id=<?= $r['id'] ?>" class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition">
                            Download
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p class="text-center text-white/80 text-lg mt-6">No reports available.</p>
    <?php endif; ?>

    <!-- Back to Dashboard -->
    <a href="dashboard_patient.php" class="block w-64 mx-auto mt-8 py-3 bg-pink-500 text-white font-bold text-center rounded-xl hover:bg-pink-600 transition">
        ← Back to Dashboard
    </a>

</div>
</body>
</html>
