<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';
$patient_id = $_SESSION['user_id'];

// Handle receipt upload
$uploadMsg = '';
if (isset($_POST['upload_receipt'])) {
    $appointment_id = intval($_POST['appointment_id']);

    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] === 0) {
        $file = $_FILES['receipt'];
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $uploadDir = 'uploads/receipts/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $newName = 'receipt_' . $appointment_id . '_' . time() . '.' . $ext;
            $uploadPath = $uploadDir . $newName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Delete old receipt if exists
                $stmtOld = $pdo->prepare("SELECT receipt FROM appointments WHERE id=? AND patient_id=?");
                $stmtOld->execute([$appointment_id, $patient_id]);
                $oldReceipt = $stmtOld->fetchColumn();
                if ($oldReceipt && file_exists($uploadDir . $oldReceipt)) unlink($uploadDir . $oldReceipt);

                // Update appointment with new receipt
                $stmt = $pdo->prepare("UPDATE appointments SET receipt=?, receipt_status='Pending' WHERE id=? AND patient_id=?");
                $stmt->execute([$newName, $appointment_id, $patient_id]);
                $uploadMsg = "✅ Receipt uploaded successfully! Waiting for doctor confirmation.";
            } else {
                $uploadMsg = "⚠️ Failed to upload file!";
            }
        } else {
            $uploadMsg = "⚠️ Invalid file type! Only PDF, JPG, JPEG, PNG allowed.";
        }
    } else {
        $uploadMsg = "⚠️ No file selected or file too large!";
    }
}

// Handle receipt deletion
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    $stmtDel = $pdo->prepare("SELECT receipt FROM appointments WHERE id=? AND patient_id=?");
    $stmtDel->execute([$del_id, $patient_id]);
    $receiptFile = $stmtDel->fetchColumn();
    if ($receiptFile && file_exists('uploads/receipts/' . $receiptFile)) unlink('uploads/receipts/' . $receiptFile);

    $stmt = $pdo->prepare("UPDATE appointments SET receipt=NULL, receipt_status=NULL WHERE id=? AND patient_id=?");
    $stmt->execute([$del_id, $patient_id]);
    header("Location: billing.php");
    exit;
}

// Fetch patient's appointments
$stmt = $pdo->prepare("
    SELECT a.*, u.fullname AS doctor_name 
    FROM appointments a 
    JOIN users u ON a.doctor_id = u.id 
    WHERE a.patient_id=? 
    ORDER BY a.appointment_date DESC
");
$stmt->execute([$patient_id]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Receipts - Medicare Plus</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-blue-200 min-h-screen flex items-center justify-center p-4 font-sans">

<div class="bg-white shadow-xl rounded-xl w-full max-w-6xl p-6">
    <h2 class="text-center text-2xl font-bold text-pink-600 mb-6">Upload Receipts</h2>

    <?php if ($uploadMsg): ?>
        <div class="mb-6 p-4 rounded border <?= str_contains($uploadMsg,'success') ? 'border-green-500 bg-green-100 text-green-700' : 'border-red-500 bg-red-100 text-red-700' ?>">
            <?= htmlspecialchars($uploadMsg) ?>
        </div>
    <?php endif; ?>

    <?php if ($appointments): ?>
    <div class="overflow-x-auto">
    <table class="min-w-full border border-gray-200">
        <thead class="bg-pink-600 text-white">
            <tr>
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Doctor</th>
                <th class="py-2 px-4">Date & Time</th>
                <th class="py-2 px-4">Status</th>
                <th class="py-2 px-4">Upload</th>
                <th class="py-2 px-4">View</th>
                <th class="py-2 px-4">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($appointments as $a): ?>
            <tr class="text-center border-b hover:bg-gray-50">
                <td class="py-2 px-4"><?= $a['id'] ?></td>
                <td class="py-2 px-4"><?= htmlspecialchars($a['doctor_name']) ?></td>
                <td class="py-2 px-4"><?= date('Y-m-d H:i', strtotime($a['appointment_date'])) ?></td>
                <td class="py-2 px-4">
                    <?php 
                    if(isset($a['receipt_status'])){
                        if($a['receipt_status']=='Pending') echo "<span class='text-orange-500 font-bold'>Pending</span>";
                        elseif($a['receipt_status']=='Confirmed') echo "<span class='text-green-600 font-bold'>Confirmed</span>";
                    } else echo "<span class='text-red-500 font-bold'>Not Uploaded</span>";
                    ?>
                </td>
                <td class="py-2 px-4">
                    <form method="post" enctype="multipart/form-data" class="flex flex-col items-center space-y-2">
                        <input type="hidden" name="appointment_id" value="<?= $a['id'] ?>">
                        <input type="file" name="receipt" required class="border rounded px-2 py-1">
                        <button type="submit" name="upload_receipt" class="px-3 py-1 bg-pink-600 text-white rounded hover:bg-pink-700">Upload</button>
                    </form>
                </td>
                <td class="py-2 px-4">
                    <?php if(!empty($a['receipt'])): ?>
                        <a href="view_receipt.php?appointment_id=<?= $a['id'] ?>" target="_blank" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">View</a>
                    <?php else: ?> - <?php endif; ?>
                </td>
                <td class="py-2 px-4">
                    <?php if(!empty($a['receipt'])): ?>
                        <a href="billing.php?delete_id=<?= $a['id'] ?>" onclick="return confirm('Are you sure you want to delete this receipt?');" class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Delete</a>
                    <?php else: ?> - <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
        <p class="text-center text-gray-700 mt-6">No appointments found.</p>
    <?php endif; ?>

    <a href="dashboard_patient.php" class="block w-max mx-auto mt-6 px-6 py-2 bg-pink-600 text-white rounded hover:bg-pink-700 font-semibold">← Back to Dashboard</a>
</div>

</body>
</html>
