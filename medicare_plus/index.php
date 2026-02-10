<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>MediCare Plus | 2026</title>

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

<body class="min-h-screen bg-gradient-to-br from-deep via-slate-900 to-blue-950 text-white overflow-x-hidden">

<!-- 🔵 NAVBAR -->
<header class="flex flex-col items-center py-10 relative z-10">
  <h1 class="text-5xl md:text-6xl font-extrabold tracking-wide bg-gradient-to-r from-sky-400 to-cyan-300 bg-clip-text text-transparent">
    MediCare Plus
  </h1>
  <p class="mt-2 text-sky-300 tracking-widest text-sm">SMART HEALTHCARE • 2026</p>

  <nav class="mt-6 flex gap-4 flex-wrap justify-center z-10 relative">
    <a href="index.php" class="nav-btn">Home</a>
    <a href="about.php" class="nav-btn">About</a>
    <a href="facilities.php" class="nav-btn">Facilities</a>
    <a href="contact.php" class="nav-btn">Contact</a>
    <a href="register.php" class="nav-btn">Register</a>
    <a href="login.php" class="nav-btn">Login</a>
  </nav>
</header>

<!-- 🌌 HERO -->
<section class="relative max-w-7xl mx-auto px-6 mt-16">
  <div class="relative rounded-3xl overflow-hidden shadow-glow group">

    <img src="https://t4.ftcdn.net/jpg/01/51/71/87/360_F_151718711_DqFYAW8pKmX3ayEYyzX2bhha9K0zmt3p.jpg"
         class="w-full h-[560px] object-cover scale-105 transition-transform duration-1000 group-hover:scale-110">

    <!-- Overlay + Glass Card -->
    <div class="absolute inset-0 flex items-center justify-center">
      <!-- Dark overlay -->
      <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-black/80 z-0"></div>

      <!-- Glass card -->
      <div class="glass-card max-w-3xl text-center p-12 rounded-3xl reveal z-10">

        <h2 class="text-4xl md:text-5xl font-bold leading-tight mb-6 neon-text">
          Healthcare Powered by <span class="text-sky-400">Technology</span>
        </h2>

        <p class="text-lg text-slate-200 mb-10">
          Appointments, reports, and doctor consultations —  
          <span class="text-sky-300">smart, secure, and instant.</span>
        </p>

        <div class="flex flex-wrap justify-center gap-6">
          <a href="register.php" class="f-btn">Get Started</a>
          <a href="login.php" class="f-btn-outline">Patient Login</a>
          <a href="staff_login.php" class="f-btn-outline">Staff Login</a>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- 🚀 FEATURES -->
<section class="max-w-6xl mx-auto mt-28 px-6 grid md:grid-cols-3 gap-8 text-center">
  <div class="feature-card reveal">
    ⚡
    <h3>Instant Care</h3>
    <p>Book & consult doctors within seconds.</p>
  </div>

  <div class="feature-card reveal delay">
    🔐
    <h3>Secure Records</h3>
    <p>AI-protected patient data & reports.</p>
  </div>

  <div class="feature-card reveal delay2">
    🤖
    <h3>Smart System</h3>
    <p>Next-gen healthcare automation.</p>
  </div>
</section>

<!-- 🔻 FOOTER -->
<footer class="mt-28 py-6 text-center text-slate-400 border-t border-white/10">
  © 2026 MediCare Plus • Future of Healthcare
</footer>

<!-- 🌟 STYLES -->
<style>
.nav-btn{
  @apply px-6 py-2 rounded-full bg-white/10 backdrop-blur-lg
         hover:bg-sky-400 hover:text-black transition
         hover:scale-110 hover:shadow-glow font-semibold;
}

.glass-card{
  background: rgba(255,255,255,0.12);
  backdrop-filter: blur(22px);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 2rem;
  padding: 3rem;
  transition: all 0.6s ease;
}
.glass-card:hover{
  transform: translateY(-10px) scale(1.02);
  box-shadow: 0 0 60px rgba(56,189,248,0.6);
}

.f-btn{
  @apply px-10 py-4 rounded-full bg-sky-400 text-black font-bold hover:scale-110 hover:shadow-glow transition duration-500;
}

.f-btn-outline{
  @apply px-10 py-4 rounded-full border border-sky-400 text-sky-300 hover:bg-sky-400 hover:text-black transition;
}

.feature-card{
  @apply bg-white/5 backdrop-blur-lg rounded-2xl p-8 border border-white/10 hover:shadow-glow transition;
}
.feature-card h3{
  @apply mt-4 text-xl font-bold text-sky-300;
}
.feature-card p{
  @apply mt-2 text-slate-300 text-sm;
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
.delay{transition-delay:.3s}
.delay2{transition-delay:.6s}

.neon-text{
  text-shadow: 0 0 25px rgba(56,189,248,0.9);
}
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
