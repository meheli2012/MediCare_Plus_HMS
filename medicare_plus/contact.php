<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Contact Us - MediCare Plus | 2026</title>

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

<!-- 🔵 HEADER -->
<header class="text-center py-12 relative z-10">
  <h1 class="text-5xl md:text-6xl font-extrabold bg-gradient-to-r from-sky-400 to-cyan-300 bg-clip-text text-transparent">
    Contact Us
  </h1>
  <p class="mt-2 text-sky-300 tracking-widest text-sm">MediCare Plus • 2026</p>

  <nav class="mt-6 flex gap-4 flex-wrap justify-center z-10 relative">
    <a href="index.php" class="nav-btn">Home</a>
    <a href="about.php" class="nav-btn">About</a>
    <a href="facilities.php" class="nav-btn">Facilities</a>
    <a href="register.php" class="nav-btn">Register</a>
    <a href="login.php" class="nav-btn">Login</a>
  </nav>
</header>

<!-- 🌌 HERO CONTACT FORM -->
<section class="relative max-w-4xl mx-auto px-6 mt-16 reveal">
  <div class="glass-card p-10 rounded-3xl shadow-glow backdrop-blur-xl relative z-10">

    <h2 class="text-4xl md:text-5xl font-bold neon-text mb-4 text-center">Get in Touch</h2>
    <p class="text-slate-200 text-center mb-8">
      Have questions or need assistance? Fill out the form below, and our team will respond promptly.
    </p>

    <form action="#" method="post" class="flex flex-col gap-4">
      <input type="text" name="name" placeholder="Your Name" required
             class="input-field">
      <input type="email" name="email" placeholder="Your Email" required
             class="input-field">
      <textarea name="message" rows="5" placeholder="Your Message" required
                class="input-field"></textarea>
      <button type="submit" class="f-btn mt-4">Send Message</button>
    </form>
  </div>
</section>

<!-- 🔻 FOOTER -->
<footer class="mt-20 py-6 text-center text-slate-400 border-t border-white/10">
  © 2026 MediCare Plus • Future of Healthcare
</footer>

<!-- 🌟 STYLES -->
<style>
.nav-btn{
  @apply px-6 py-2 rounded-full bg-white/10 backdrop-blur-lg hover:bg-sky-400 hover:text-black transition hover:scale-110 hover:shadow-glow font-semibold;
}

.glass-card{
  background: rgba(255,255,255,0.08);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.18);
  border-radius: 2rem;
  transition: all 0.6s ease;
}
.glass-card:hover{
  transform: translateY(-5px) scale(1.02);
  box-shadow: 0 0 60px rgba(56,189,248,0.6);
}

.input-field{
  @apply p-4 rounded-xl bg-white/10 border border-white/20 text-white placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-400 transition;
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
