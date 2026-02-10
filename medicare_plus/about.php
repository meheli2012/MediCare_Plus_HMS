<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>About Us | MediCare Plus 2026</title>

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

<!-- 🌌 HEADER -->
<header class="text-center py-12 relative">
  <div class="absolute inset-0 bg-gradient-to-b from-sky-500/10 to-transparent blur-3xl pointer-events-none"></div>

  <h1 class="text-6xl font-extrabold bg-gradient-to-r from-sky-400 to-cyan-300 bg-clip-text text-transparent">
    About MediCare Plus
  </h1>

  <p class="mt-4 text-sky-300 tracking-[0.3em] text-sm uppercase">
    Smart Healthcare • 2026
  </p>

  <!-- 🔗 NAV -->
  <nav class="relative z-10 mt-8 flex flex-wrap justify-center gap-4">
    <a href="index.php" class="nav-btn">Home</a>
    <a href="facilities.php" class="nav-btn">Facilities</a>
    <a href="contact.php" class="nav-btn">Contact</a>
    <a href="register.php" class="nav-btn">Register</a>
    <a href="login.php" class="nav-btn">Login</a>
  </nav>
</header>

<!-- 🚀 HERO IMAGE -->
<section class="max-w-7xl mx-auto px-6 mt-16">
  <div class="relative group rounded-[2rem] overflow-hidden shadow-glow">

    <img src="https://static.vecteezy.com/system/resources/thumbnails/002/658/283/small/wooden-ball-with-word-health-concept-photo.jpg"
         class="w-full h-[460px] object-cover transition-all duration-1000 
                scale-110 blur-sm group-hover:blur-0 
                group-hover:scale-125 group-hover:rotate-1">

    <!-- Scan line -->
    <div class="absolute inset-0 pointer-events-none">
      <div class="scan-line"></div>
    </div>

    <!-- Overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent pointer-events-none"></div>

    <!-- Text -->
    <div class="absolute inset-0 flex items-center justify-center z-10">
      <h2 class="text-5xl font-bold text-sky-300 neon-text">
        Caring Beyond Technology
      </h2>
    </div>
  </div>
</section>

<!-- 🧊 CONTENT -->
<section class="max-w-6xl mx-auto mt-24 px-6 space-y-20">

  <div class="glass-card reveal">
    <h3 class="section-title">Who We Are</h3>
    <p class="section-text">
      MediCare Plus simplifies healthcare by combining compassionate care with advanced digital technology.
      Patients can schedule appointments, access reports instantly, and communicate securely with doctors —
      all in one intelligent platform.
    </p>
  </div>

  <div class="glass-card reveal delay">
    <h3 class="section-title">Our Mission</h3>
    <p class="section-text">
      Our mission is to deliver accessible, caring, and secure healthcare for everyone.
      Through innovation and trust, we empower patients to take control of their health confidently.
    </p>
  </div>

  <div class="glass-card reveal delay2">
    <h3 class="section-title">Our Vision</h3>
    <p class="section-text">
      We envision a future where healthcare is effortless, intelligent, and patient-centered,
      reducing unnecessary hospital visits while enabling real-time digital care anywhere.
    </p>
  </div>

  <!-- ✅ WORKING BUTTON -->
  <div class="relative z-10 text-center reveal">
    <a href="index.php" class="main-btn inline-block">
      ← Back to Home
    </a>
  </div>

</section>

<!-- 🔻 FOOTER -->
<footer class="mt-32 py-8 text-center text-slate-400 border-t border-white/10">
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
  background: rgba(255,255,255,0.08);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.18);
  border-radius: 2rem;
  padding: 3rem;
  transition: all 0.6s ease;
}
.glass-card:hover{
  transform: translateY(-10px) scale(1.02);
  box-shadow: 0 0 60px rgba(56,189,248,0.6);
}

.section-title{
  @apply text-4xl font-bold text-sky-300 mb-6;
}

.section-text{
  @apply text-slate-300 text-lg leading-relaxed;
}

.main-btn{
  @apply px-12 py-4 rounded-full bg-sky-400 text-black font-bold
         hover:scale-110 hover:shadow-glow transition duration-500;
}

.scan-line{
  position:absolute;
  top:-100%;
  left:0;
  width:100%;
  height:200%;
  background: linear-gradient(
    to bottom,
    transparent,
    rgba(56,189,248,0.25),
    transparent
  );
  animation: scan 4s linear infinite;
}
@keyframes scan{
  from{transform:translateY(-50%);}
  to{transform:translateY(50%);}
}

.neon-text{
  text-shadow: 0 0 20px rgba(56,189,248,0.8);
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
