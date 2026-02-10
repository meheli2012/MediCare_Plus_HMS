<?php
session_start();
include 'includes/db.php';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Only staff: admin or doctor
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND role IN ('admin','doctor')");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password_hash'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if($user['role']=='admin'){
            header('Location: dashboard_admin.php');
        } elseif($user['role']=='doctor'){
            header('Location: dashboard_doctor.php');
        }
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Login - MediCare Plus</title>

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        neon: '#38bdf8',
        deep: '#020617'
      },
      boxShadow: {
        glow: '0 0 45px rgba(56,189,248,0.7)'
      }
    }
  }
}
</script>
</head>

<body class="min-h-screen bg-gradient-to-br from-deep via-slate-900 to-blue-950 text-white flex flex-col">

<!-- Header -->
<header class="text-center py-10">
  <h1 class="text-4xl md:text-5xl font-extrabold bg-gradient-to-r from-sky-400 to-cyan-300 bg-clip-text text-transparent">
    MediCare Plus Staff Login
  </h1>
  <nav class="mt-6 flex gap-4 justify-center flex-wrap">
    <a href="index.php" class="nav-btn">Home</a>
    <a href="register.php" class="nav-btn">Register</a>
    <a href="login.php" class="nav-btn">Patient Login</a>
  </nav>
</header>

<!-- Form -->
<div class="flex-grow flex items-center justify-center px-4">
  <div class="glass-card max-w-md w-full p-10 rounded-2xl shadow-glow">
    <h2 class="text-2xl font-bold text-sky-400 mb-6 text-center">Staff Login</h2>
    <?php if(isset($error)) echo "<p class='text-red-500 mb-4 text-center'>$error</p>"; ?>
    <form method="POST" class="flex flex-col gap-4">
      <input type="email" name="email" placeholder="Email" required
             class="p-3 rounded-lg border border-white/30 bg-white/10 text-white placeholder-white/70 focus:ring-2 focus:ring-sky-400 focus:outline-none">
      <input type="password" name="password" placeholder="Password" required
             class="p-3 rounded-lg border border-white/30 bg-white/10 text-white placeholder-white/70 focus:ring-2 focus:ring-sky-400 focus:outline-none">
      <button type="submit"
              class="px-6 py-3 rounded-full bg-sky-400 text-black font-bold hover:scale-105 hover:shadow-glow transition">
        Login
      </button>
    </form>
  </div>
</div>

<!-- Footer -->
<footer class="text-center py-6 mt-auto text-slate-400 border-t border-white/10">
  &copy; 2026 MediCare Plus. All Rights Reserved.
</footer>

<!-- Styles -->
<style>
.nav-btn{
  @apply px-6 py-2 rounded-full bg-white/10 backdrop-blur-lg
         hover:bg-sky-400 hover:text-black transition
         hover:scale-105 hover:shadow-glow font-semibold;
}

.glass-card{
  background: rgba(255,255,255,0.12);
  backdrop-filter: blur(22px);
  border: 1px solid rgba(255,255,255,0.2);
  transition: all 0.5s ease;
}
.glass-card:hover{
  transform: translateY(-5px) scale(1.02);
  box-shadow: 0 0 60px rgba(56,189,248,0.6);
}
</style>

</body>
</html>
