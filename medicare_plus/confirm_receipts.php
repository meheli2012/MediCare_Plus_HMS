<?php
session_start();

// Only logged-in doctors can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';
$doctor_id = intval($_SESSION['user_id']);

// Handle receipt confirmation
if (isset($_GET['confirm_id'])) {
    $confirm_id = intval($_GET['confirm_id']);
    $stmt = $pdo->prepare("
        UPDATE appointments
        SET receipt_status = 'Confirmed'
        WHERE id = ? AND doctor_id = ? AND receipt_status = 'Pending'
    ");
    $stmt->execute([$confirm_id, $doctor_id]);
    header("Location: confirm_receipts.php");
    exit;
}

// Fetch pending receipts
$stmt = $pdo->prepare("
    SELECT a.id, a.appointment_date, a.receipt,
           u.fullname AS patient_name, s.name AS service_name
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    JOIN services s ON a.service_id = s.id
    WHERE a.doctor_id = ? AND a.receipt_status = 'Pending'
    ORDER BY a.appointment_date ASC
");
$stmt->execute([$doctor_id]);
$pendingReceipts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Confirm Patient Receipts</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                glowpink: '#ff5c8d',
                glowblue: '#38bdf8',
                deep: '#020617',
                success: '#33cc66',
                danger: '#e60000'
            },
            boxShadow: {
                glow: '0 0 25px rgba(56,189,248,0.7)'
            }
        }
    }
}
</script>
</head>
<body class="bg-gradient-to-br from-deep via-slate-900 to-blue-950 min-h-screen text-white">

<div class="max-w-7xl mx-auto py-12 px-4">
    <!-- Header -->
    <div class="text-center mb-10">
        <h1 class="text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-glowblue to-glowpink">
            Confirm Patient Receipts
        </h1>
        <p class="text-slate-300 mt-2">Manage pending receipts for your appointments</p>
    </div>

    <!-- Table Card -->
    <div class="bg-white/10 backdrop-blur-lg p-6 rounded-3xl shadow-glow overflow-x-auto">
        <?php if ($pendingReceipts): ?>
            <table class="min-w-full border-collapse text-white">
                <thead>
                    <tr class="bg-gradient-to-r from-glowpink to-glowblue">
                        <th class="p-3 text-left">Appointment Date</th>
                        <th class="p-3 text-left">Patient</th>
                        <th class="p-3 text-left">Service</th>
                        <th class="p-3 text-left">Receipt</th>
                        <th class="p-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingReceipts as $r): ?>
                    <tr class="border-b border-white/20 hover:bg-white/10 transition">
                        <td class="p-3"><?= date('F j, g:i A', strtotime($r['appointment_date'])) ?></td>
                        <td class="p-3"><?= htmlspecialchars($r['patient_name']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($r['service_name']) ?></td>
                        <td class="p-3">
                            <?php if (!empty($r['receipt'])): ?>
                                <a href="uploads/receipts/<?= htmlspecialchars($r['receipt']) ?>" target="_blank" class="px-4 py-1 rounded-full bg-glowblue text-black font-bold hover:shadow-glow transition">
                                    View
                                </a>
                            <?php else: ?>
                                No receipt
                            <?php endif; ?>
                        </td>
                        <td class="p-3">
                            <?php if (!empty($r['receipt'])): ?>
                                <a href="confirm_receipts.php?confirm_id=<?= $r['id'] ?>" onclick="return confirm('Confirm this receipt?');"
                                   class="px-4 py-1 rounded-full bg-success text-black font-bold hover:shadow-glow transition">
                                    Confirm
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-slate-300 mt-6 text-lg">No pending receipts.</p>
        <?php endif; ?>
    </div>

    <!-- Back Button -->
    <div class="text-center mt-8">
        <a href="dashboard_doctor.php" class="inline-block px-6 py-3 bg-gradient-to-r from-glowpink to-glowblue rounded-full font-bold hover:scale-105 transition">
            Back to Dashboard
        </a>
    </div>
</div>

</body>
</html>
