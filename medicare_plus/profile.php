<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient'){
    header("Location: login.php");
    exit;
}

include 'includes/db.php';

$patient_id = $_SESSION['user_id'];

// Fetch user basic info
$user = $pdo->prepare("SELECT * FROM users WHERE id=?");
$user->execute([$patient_id]);
$user = $user->fetch();

// Fetch patient profile (or create if empty)
$profile = $pdo->prepare("SELECT * FROM patient_profile WHERE user_id=?");
$profile->execute([$patient_id]);
$profile = $profile->fetch();

if(!$profile){
    $create = $pdo->prepare("INSERT INTO patient_profile (user_id) VALUES (?)");
    $create->execute([$patient_id]);

    $profile = $pdo->prepare("SELECT * FROM patient_profile WHERE user_id=?");
    $profile->execute([$patient_id]);
    $profile = $profile->fetch();
}

$success = "";

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // From users table
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    $updateUser = $pdo->prepare("UPDATE users SET fullname=?, email=? WHERE id=?");
    $updateUser->execute([$fullname, $email, $patient_id]);

    // From patient_profile table
    $address = $_POST['address'];
    $age = $_POST['age'];
    $weight = $_POST['weight'];
    $height = $_POST['height'];

    $profilePicPath = $profile['profile_pic'];

    if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0){
        $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $filename = "uploads/" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $filename);
        $profilePicPath = $filename;
    }

    $updateProfile = $pdo->prepare("
        UPDATE patient_profile 
        SET address=?, age=?, weight=?, height=?, profile_pic=? 
        WHERE user_id=?
    ");
    $updateProfile->execute([$address, $age, $weight, $height, $profilePicPath, $patient_id]);

    $success = "Profile updated successfully!";

    // Refresh data
    $user['fullname'] = $fullname;
    $user['email'] = $email;
    $profile['address'] = $address;
    $profile['age'] = $age;
    $profile['weight'] = $weight;
    $profile['height'] = $height;
    $profile['profile_pic'] = $profilePicPath;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile - Medicare Plus</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 via-slate-900 to-indigo-900 min-h-screen font-sans text-white">

<div class="max-w-3xl mx-auto py-12 px-6">

    <h1 class="text-4xl font-extrabold text-center mb-6 text-gradient bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-pink-500">
        My Profile
    </h1>

    <?php if($success): ?>
    <div class="mb-6 p-4 bg-green-600 rounded-lg text-center font-semibold">
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="bg-white/10 backdrop-blur-lg p-8 rounded-3xl shadow-lg space-y-4">

        <div class="flex justify-center mb-4">
            <img src="<?= $profile['profile_pic'] ? $profile['profile_pic'] : 'assets/default.png' ?>" 
                 class="w-32 h-32 rounded-full object-cover border-4 border-pink-500 shadow-glow">
        </div>

        <div>
            <label class="block font-semibold mb-1">Full Name</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" 
                   class="w-full p-3 rounded-lg text-black">
        </div>

        <div>
            <label class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" 
                   class="w-full p-3 rounded-lg text-black">
        </div>

        <div>
            <label class="block font-semibold mb-1">Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($profile['address']) ?>" 
                   class="w-full p-3 rounded-lg text-black">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Age</label>
                <input type="number" name="age" value="<?= htmlspecialchars($profile['age']) ?>" 
                       class="w-full p-3 rounded-lg text-black">
            </div>
            <div>
                <label class="block font-semibold mb-1">Weight (kg)</label>
                <input type="number" step="0.1" name="weight" value="<?= htmlspecialchars($profile['weight']) ?>" 
                       class="w-full p-3 rounded-lg text-black">
            </div>
            <div>
                <label class="block font-semibold mb-1">Height (cm)</label>
                <input type="number" step="0.1" name="height" value="<?= htmlspecialchars($profile['height']) ?>" 
                       class="w-full p-3 rounded-lg text-black">
            </div>
            <div>
                <label class="block font-semibold mb-1">Profile Picture</label>
                <input type="file" name="profile_pic" class="w-full p-2 rounded-lg text-black">
            </div>
        </div>

        <button type="submit" 
                class="w-full py-3 bg-pink-500 hover:bg-pink-600 rounded-xl font-bold text-lg transition-all shadow-lg hover:shadow-glow">
            Save Changes
        </button>
    </form>

</div>
</body>
</html>
