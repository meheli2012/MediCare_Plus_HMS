<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Facilities - MediCare Plus | 2026</title>

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
    Our Facilities
  </h1>
  <p class="mt-2 text-sky-300 tracking-widest text-sm">MediCare Plus • 2026</p>

  <nav class="mt-6 flex gap-4 flex-wrap justify-center z-10 relative">
    <a href="index.php" class="nav-btn">Home</a>
    <a href="about.php" class="nav-btn">About</a>
    <a href="contact.php" class="nav-btn">Contact</a>
    <a href="register.php" class="nav-btn">Register</a>
    <a href="login.php" class="nav-btn">Login</a>
  </nav>
</header>

<!-- 🌌 HERO IMAGE -->
<section class="relative max-w-7xl mx-auto px-6 mt-16">
  <div class="relative rounded-3xl overflow-hidden shadow-glow group">
    <img src="https://cdn.pixabay.com/photo/2022/12/12/02/39/doctor-7650023_640.png"
         class="w-full h-[460px] object-cover transition-transform duration-1000 group-hover:scale-105">

    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent pointer-events-none"></div>
  </div>
</section>

<!-- 🏥 MAIN CONTENT -->
<section class="max-w-6xl mx-auto mt-16 px-6 space-y-12">

  <div class="glass-card reveal">
    <h2 class="section-title">Our Facilities</h2>
    <p class="section-text">
      MediCare Plus combines modern medical facilities with cutting-edge technology to deliver exceptional healthcare services.
      By bringing together skilled doctors, advanced medical equipment, and user-friendly digital tools, we ensure every patient receives accurate diagnoses,
      effective treatments, and a seamless healthcare experience tailored to their needs.
    </p>
  </div>

  <div class="glass-card reveal delay">
    <h2 class="section-title">Advanced Equipment</h2>
    <p class="section-text">
      Our facilities use advanced technology, from diagnostic imaging to lab testing, to provide precise and reliable assessments of patient health.
      This ensures accurate monitoring, faster results, and better-informed medical decisions, allowing patients to receive timely, high-quality care with confidence.
    </p>
  </div>

  <div class="glass-card reveal delay2">
    <h2 class="section-title">Specialized Departments</h2>
    <p class="section-text">
      We have dedicated departments for cardiology, neurology, pediatrics, and more, each staffed with skilled and experienced professionals.
      Our teams are committed to providing personalized care, addressing a wide range of health concerns, and ensuring that every patient receives expert attention tailored to their unique needs.
    </p>
  </div>

  <div class="glass-card reveal delay3">
    <h2 class="section-title">Specialized Doctors</h2>
    <ul class="section-text list-disc list-inside space-y-2 text-slate-200">
      <li>Dr. Savinthi Fonseka (Cardiology)</li>
      <li>Dr. Nihal Perera (Pediatrics)</li>
      <li>Dr. Kasun Jayasinghe (Blood Test)</li>
      <li>Dr. Anusha Fernando (X-ray)</li>
      <li>Dr. Sampath de Silva (General Consultation)</li>
      <li>And more...</li>
    </ul>
  </div>

  <!-- ✅ BACK BUTTON -->
  <div class="text-center reveal delay4">
    <a href="index.php" class="f-btn">← Back to Home</a>
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
  padding: 2.5rem;
  transition: all 0.6s ease;
}
.glass-card:hover{
  transform: translateY(-10px) scale(1.02);
  box-shadow: 0 0 60px rgba(56,189,248,0.6);
}

.section-title{
  @apply text-3xl md:text-4xl font-bold text-sky-300 mb-4;
}

.section-text{
  @apply text-slate-300 text-lg leading-relaxed;
}

.f-btn{
  @apply px-10 py-4 rounded-full bg-sky-400 text-black font-bold hover:scale-110 hover:shadow-glow transition duration-500;
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
.delay3{transition-delay:.9s}
.delay4{transition-delay:1.2s}

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
