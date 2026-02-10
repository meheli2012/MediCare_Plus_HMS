<?php
session_start();

// Access control
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor'){
    header("Location: login.php");
    exit;
}

include 'includes/db.php';
$doctor_id = intval($_SESSION['user_id']);

// Fetch doctor info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch();

// Force logout if user not found
if(!$doctor){
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$success = "";

// Handle form submission
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];
    $experience = $_POST['experience'];

    $profilePicPath = $doctor['profile_pic'] ?? '';

    if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0){
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = "uploads/" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $filename);
        $profilePicPath = $filename;
    }

    $update = $pdo->prepare("UPDATE users SET fullname=?, email=?, specialization=?, experience=?, profile_pic=? WHERE id=?");
    $update->execute([$fullname, $email, $specialization, $experience, $profilePicPath, $doctor_id]);

    $success = "Profile updated successfully!";

    // Refresh doctor data
    $doctor['fullname'] = $fullname;
    $doctor['email'] = $email;
    $doctor['specialization'] = $specialization;
    $doctor['experience'] = $experience;
    $doctor['profile_pic'] = $profilePicPath;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor Profile - Medicare Plus</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                glowpink: '#ff5c8d',
                glowblue: '#38bdf8',
                deep: '#020617'
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

<div class="max-w-lg mx-auto py-12 px-4">
    <!-- Header -->
    <div class="text-center mb-10">
        <div class="mx-auto w-32 h-32 rounded-full bg-gradient-to-tr from-glowpink to-glowblue flex items-center justify-center shadow-glow">
            <img src="<?= !empty($doctor['profile_pic']) ? $doctor['profile_pic'] : 'assets/default.png' ?>" class="w-32 h-32 rounded-full object-cover border-4 border-white">
        </div>
        <h1 class="text-4xl font-bold mt-4 bg-clip-text text-transparent bg-gradient-to-r from-glowblue to-glowpink"><?= htmlspecialchars($doctor['fullname']) ?></h1>
        <p class="text-slate-300 mt-1"><?= htmlspecialchars($doctor['specialization'] ?? 'Specialization not set') ?></p>
        <?php if($success): ?>
        <p class="mt-3 text-green-400 font-semibold"><?= $success ?></p>
        <?php endif; ?>
    </div>

    <!-- Profile Form -->
    <form method="post" enctype="multipart/form-data" class="bg-white/10 backdrop-blur-lg p-6 rounded-3xl shadow-glow space-y-5">
        <div>
            <label class="block font-semibold mb-1">Full Name</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($doctor['fullname']) ?>" class="w-full p-3 rounded-lg text-black" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($doctor['email']) ?>" class="w-full p-3 rounded-lg text-black" required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Specialization</label>
            <input type="text" name="specialization" value="<?= htmlspecialchars($doctor['specialization'] ?? '') ?>" class="w-full p-3 rounded-lg text-black">
        </div>

        <div>
            <label class="block font-semibold mb-1">Experience</label>
            <input type="text" name="experience" value="<?= htmlspecialchars($doctor['experience'] ?? '') ?>" class="w-full p-3 rounded-lg text-black">
        </div>

        <div>
            <label class="block font-semibold mb-1">Profile Picture</label>
            <input type="file" name="profile_pic" class="w-full text-black rounded-lg p-2">
        </div>

        <div class="flex justify-center space-x-4 mt-5">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-glowblue to-glowpink font-bold rounded-full hover:scale-105 transition">Save Changes</button>
            <a href="dashboard_doctor.php" class="px-6 py-3 bg-gradient-to-r from-glowpink to-glowblue font-bold rounded-full hover:scale-105 transition text-black flex items-center justify-center">Dashboard</a>
        </div>
    </form>
</div>

</body>
</html>
