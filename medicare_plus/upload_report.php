<?php
session_start();

// Only allow logged-in doctors
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

include 'includes/db.php';

$doctor_id = intval($_SESSION['user_id']);
$patient_id = intval($_GET['patient_id'] ?? 0);

// Validate patient ID
if (!$patient_id) {
    die("Invalid patient.");
}

$success = $error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_name = trim($_POST['report_name']);
    $report_date = trim($_POST['report_date']);
    $description = trim($_POST['description']);

    if (empty($report_name) || empty($report_date)) {
        $error = "Please fill all required fields.";
    } else {
        if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] === 0) {
            $ext = strtolower(pathinfo($_FILES['report_file']['name'], PATHINFO_EXTENSION));
            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

            if (!in_array($ext, $allowed)) {
                $error = "Invalid file type. Only PDF, JPG, PNG allowed.";
            } else {
                $filename = "reports/" . uniqid() . "." . $ext;
                move_uploaded_file($_FILES['report_file']['tmp_name'], $filename);

                $stmt = $pdo->prepare("
                    INSERT INTO patient_reports
                    (doctor_id, patient_id, file_path, report_name, report_date, description)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $doctor_id,
                    $patient_id,
                    $filename,
                    $report_name,
                    $report_date,
                    $description
                ]);

                $success = "Report uploaded successfully!";
            }
        } else {
            $error = "Please select a file to upload.";
        }
    }
}

// Fetch patient info
$patient = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
$patient->execute([$patient_id]);
$patient = $patient->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Patient Report</title>
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
<body class="bg-gradient-to-br from-deep via-slate-900 to-blue-950 min-h-screen flex items-center justify-center">

<div class="bg-white/10 backdrop-blur-lg rounded-3xl shadow-glow p-8 w-full max-w-md text-white">
    <h2 class="text-3xl font-bold text-center bg-clip-text text-transparent bg-gradient-to-r from-glowblue to-glowpink mb-6">
        Upload Report for <?= htmlspecialchars($patient['fullname']) ?>
    </h2>

    <?php if ($success): ?>
        <p class="text-center text-success font-bold mb-4"><?= $success ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="text-center text-danger font-bold mb-4"><?= $error ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label class="block font-semibold mb-1">Report Name *</label>
            <input type="text" name="report_name" required class="w-full p-2 rounded text-black">
        </div>

        <div>
            <label class="block font-semibold mb-1">Report Date *</label>
            <input type="date" name="report_date" value="<?= date('Y-m-d') ?>" required class="w-full p-2 rounded text-black">
        </div>

        <div>
            <label class="block font-semibold mb-1">Description</label>
            <textarea name="description" placeholder="Write a short report description..." class="w-full p-2 rounded text-black h-24"></textarea>
        </div>

        <div>
            <label class="block font-semibold mb-1">Upload File *</label>
            <input type="file" name="report_file" required class="w-full p-2 rounded text-black">
        </div>

        <button type="submit" class="w-full py-3 rounded-full bg-gradient-to-r from-glowpink to-glowblue font-bold hover:scale-105 transition">
            Upload Report
        </button>
    </form>

    <a href="dashboard_doctor.php" class="mt-6 inline-block w-full text-center py-3 rounded-full bg-gradient-to-r from-glowblue to-glowpink font-bold hover:scale-105 transition">
        Back to Dashboard
    </a>
</div>

</body>
</html>
