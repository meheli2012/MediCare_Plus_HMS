<?php
include 'includes/db.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'patient';

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    if($stmt->rowCount() > 0){
        $error = "Email already registered. Please use a different email or login.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users(fullname,email,password_hash,role) VALUES(?,?,?,?)");
        if($stmt->execute([$fullname,$email,$password_hash,$role])){
            $success = "Registration successful! <a href='login.php' class='link-blue'>Login here</a>";
        } else {
            $error = "Error occurred during registration!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - MediCare Plus | 2026</title>

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

<!-- 🔵 HEADER -->
<header class="text-center py-12 relative z-10">
  <h1 class="text-5xl md:text-6xl font-extrabold bg-gradient-to-r from-sky-400 to-cyan-300 bg-clip-text text-transparent">
    MediCare Plus
  </h1>
  <p class="mt-2 text-sky-300 tracking-widest text-sm">Patient Registration • 2026</p>

  <nav class="mt-6 flex gap-4 flex-wrap justify-center z-10 relative">
    <a href="index.php" class="nav-btn">Home</a>
    <a href="about.php" class="nav-btn">About</a>
    <a href="facilities.php" class="nav-btn">Facilities</a>
    <a href="contact.php" class="nav-btn">Contact</a>
    <a href="login.php" class="nav-btn">Login</a>
  </nav>
</header>

<!-- 📝 FORM SECTION -->
<section class="flex-1 flex items-center justify-center px-6 mt-12 reveal">
  <div class="glass-card p-10 rounded-3xl shadow-glow max-w-md w-full">
    <h2 class="text-3xl md:text-4xl font-bold neon-text mb-6 text-center">Patient Registration</h2>

    <?php if(isset($error)) echo "<p class='error text-red-400 mb-4 text-center'>$error</p>"; ?>
    <?php if(isset($success)) echo "<p class='success text-green-400 mb-4 text-center'>$success</p>"; ?>

    <form method="POST" class="flex flex-col gap-4">
      <input type="text" name="fullname" placeholder="Full Name" required class="input-field">
      <input type="email" name="email" placeholder="Email" required class="input-field">
      <input type="password" name="password" placeholder="Password" required class="input-field">
      <button type="submit" class="f-btn mt-2">Register</button>
    </form>

    <p class="mt-4 text-center text-sky-300">
      Already have an account? <a href="login.php" class="underline hover:text-white">Login</a>
    </p>
  </div>
</section>

<!-- 🔻 FOOTER -->
<footer class="mt-12 py-6 text-center text-slate-400 border-t border-white/10">
  © 2026 MediCare Plus • Future of Healthcare
</footer>

<!-- 🌟 STYLES -->
<style>
.nav-btn{
  @apply px-6 py-2 rounded-full bg-white/10 backdrop-blur-lg hover:bg-sky-400 hover:text-black transition hover:scale-110 hover:shadow-glow font-semibold;
}

.glass-card{
  background: rgba(255,255,255,0.12);
  backdrop-filter: blur(25px);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 2rem;
  transition: all 0.6s ease;
}
.glass-card:hover{
  transform: translateY(-5px) scale(1.02);
  box-shadow: 0 0 60px rgba(56,189,248,0.6);
}

.input-field{
  padding: 16px;
  border-radius: 12px;
  border: 1px solid rgba(255,255,255,0.3);
  background-color: rgba(255,255,255,0.15);
  color: white; /* ensures typed text is visible */
  font-size: 1rem;
  transition: all 0.3s;
}
.input-field::placeholder{
  color: rgba(255,255,255,0.7); /* placeholder more visible */
}
.input-field:focus{
  outline: none;
  border: 1px solid #38bdf8;
  background-color: rgba(255,255,255,0.2);
  box-shadow: 0 0 15px rgba(56,189,248,0.5);
}

.f-btn{
  @apply px-10 py-4 rounded-full bg-sky-400 text-black font-bold hover:scale-105 hover:shadow-glow transition duration-500;
}

.reveal{
  opacity:0;
  transform:translateY(50px);
}
.reveal.show{
  opacity:1;
  transform:translateY(0);
  transition:1s ease;
}

.neon-text{
  text-shadow: 0 0 25px rgba(56,189,248,0.8);
}

.error{color:#f87171;}
.success{color:#4ade80;}
</style>

<!-- 🔥 SCROLL REVEAL SCRIPT -->
<script>
const reveals = document.querySelectorAll('.reveal');
window.addEventListener('scroll', () => {
  reveals.forEach(el => {
    if(el.getBoundingClientRect().top < window.innerHeight - 100){
      el.classList.add('show');
    }
  });
});
</script>

</body>
</html>
