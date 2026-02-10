<?php
session_start();
include 'includes/db.php';

// Redirect if user is not a logged-in doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

$doctorId = intval($_SESSION['user_id']);
$feedbackMessage = "";

// Fetch doctor info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$doctorId]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$doctor) { session_unset(); session_destroy(); header("Location: login.php"); exit; }

// Fetch dashboard counts
$totalApptsStmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id = ?");
$totalApptsStmt->execute([$doctorId]);
$totalAppointments = $totalApptsStmt->fetchColumn();

$newMessageStmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read=0");
$newMessageStmt->execute([$doctorId]);
$newMessageCount = $newMessageStmt->fetchColumn();

$pendingReceiptStmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id=? AND receipt_status='Pending'");
$pendingReceiptStmt->execute([$doctorId]);
$pendingReceipts = $pendingReceiptStmt->fetchColumn();

// Upcoming appointments
$apptStmt = $pdo->prepare("
    SELECT a.*, u.fullname AS patient_name, s.name AS service_name 
    FROM appointments a
    JOIN users u ON a.patient_id=u.id
    JOIN services s ON a.service_id=s.id
    WHERE a.doctor_id=?
    ORDER BY a.appointment_date ASC
    LIMIT 5
");
$apptStmt->execute([$doctorId]);
$appointments = $apptStmt->fetchAll(PDO::FETCH_ASSOC);

// Doctor list (for feedback modal)
$doctorsStmt = $pdo->prepare("SELECT id, fullname FROM users WHERE role='doctor' ORDER BY fullname");
$doctorsStmt->execute();
$doctors = $doctorsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Medicare Plus - Doctor Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        neon: '#38bdf8',
        deep: '#020617',
        glowpink: '#ff5c8d',
        glowblue: '#38bdf8'
      },
      boxShadow: {
        glow: '0 0 25px rgba(56,189,248,0.7)',
        glowpink: '0 0 25px rgba(255,92,141,0.7)'
      },
      keyframes: {
        fadeUp: { '0%': { opacity: '0', transform: 'translateY(50px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } }
      },
      animation: { fadeUp: 'fadeUp 0.8s ease forwards' }
    }
  }
}
</script>
</head>
<body class="bg-gradient-to-br from-deep via-slate-900 to-blue-950 text-white min-h-screen">

<!-- HEADER -->
<div class="text-center py-12">
    <div class="mx-auto w-28 h-28 bg-gradient-to-tr from-glowpink to-glowblue rounded-full flex items-center justify-center shadow-glow">
        <span class="text-5xl font-bold text-white">+</span>
    </div>
    <h1 class="text-5xl mt-4 font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-glowblue to-glowpink">Medicare Plus</h1>
    <p class="text-slate-300 mt-2">Welcome, Dr. <?= htmlspecialchars($doctor['fullname']) ?></p>
</div>

<?php if($feedbackMessage): ?>
<p class="text-center font-bold my-4 <?= strpos($feedbackMessage,'success')!==false ? 'text-green-400':'text-red-400' ?>">
    <?= htmlspecialchars($feedbackMessage) ?>
</p>
<?php endif; ?>

<!-- DASHBOARD CARDS -->
<div class="max-w-7xl mx-auto grid sm:grid-cols-2 md:grid-cols-3 gap-8 px-6">
    <?php
    $cards = [
        ['title'=>'My Profile','desc'=>'Update your info','link'=>'profile_doctor.php','icon'=>'👤','color'=>'from-glowpink to-glowblue'],
        ['title'=>'Appointments','desc'=>"Total: $totalAppointments",'link'=>'appointments.php','icon'=>'🩺','color'=>'from-glowblue to-glowpink'],
        ['title'=>'Messages','desc'=>($newMessageCount>0?"$newMessageCount new messages":"No new messages"),'link'=>'chat.php','icon'=>'✉️','color'=>'from-glowpink to-glowblue'],
        ['title'=>'Pending Receipts','desc'=>($pendingReceipts>0?"$pendingReceipts pending":"All confirmed"),'link'=>'confirm_receipts.php','icon'=>'💳','color'=>'from-glowblue to-glowpink']
    ];
    foreach($cards as $card): ?>
    <div class="bg-white/10 backdrop-blur-lg p-6 rounded-3xl shadow-glow hover:scale-105 transition animate-fadeUp cursor-pointer">
        <div class="text-4xl mb-3"><?= $card['icon'] ?></div>
        <h3 class="text-2xl font-bold mb-2 bg-clip-text text-transparent bg-gradient-to-r <?= $card['color'] ?>"><?= $card['title'] ?></h3>
        <p class="text-slate-200"><?= $card['desc'] ?></p>
        <a href="<?= $card['link'] ?>" class="mt-4 inline-block px-6 py-2 rounded-full bg-gradient-to-r <?= $card['color'] ?> text-black font-semibold hover:shadow-glow transition">Open</a>
    </div>
    <?php endforeach; ?>
</div>

<!-- UPCOMING APPOINTMENTS TABLE -->
<h2 class="text-center text-3xl mt-12 mb-6 text-glowblue font-bold">Upcoming Appointments</h2>
<div class="overflow-x-auto px-6">
<table class="min-w-full border-collapse border border-gray-500 text-black bg-white/10 backdrop-blur-lg rounded-xl">
    <thead class="bg-gradient-to-r from-glowpink to-glowblue text-white">
        <tr>
            <th class="p-3">Date & Time</th>
            <th class="p-3">Patient</th>
            <th class="p-3">Service</th>
            <th class="p-3">Upload Report</th>
        </tr>
    </thead>
    <tbody>
    <?php if($appointments): foreach($appointments as $a): ?>
        <tr class="hover:bg-white/20 transition">
            <td class="p-3"><?= date('F j, g:i A', strtotime($a['appointment_date'])) ?></td>
            <td class="p-3"><?= htmlspecialchars($a['patient_name']) ?></td>
            <td class="p-3"><?= htmlspecialchars($a['service_name']) ?></td>
            <td class="p-3"><a class="px-4 py-1 rounded-full bg-gradient-to-r from-glowpink to-glowblue text-black font-bold hover:shadow-glow transition" href="upload_report.php?patient_id=<?= $a['patient_id'] ?>">Upload</a></td>
        </tr>
    <?php endforeach; else: ?>
        <tr><td colspan="4" class="text-center p-3">No upcoming appointments.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<!-- LOGOUT -->
<div class="text-center my-10">
    <a href="logout.php" class="px-8 py-3 bg-red-700 rounded-full font-bold hover:scale-105 transition">Logout</a>
</div>

<!-- FEEDBACK MODAL (kept if needed) -->
<div id="feedbackModal" class="fixed inset-0 bg-black/60 flex items-center justify-center hidden z-50">
    <div class="bg-gradient-to-tr from-glowpink to-glowblue p-6 rounded-3xl w-96 relative shadow-glow">
        <span class="absolute top-2 right-4 text-3xl font-bold cursor-pointer text-white" onclick="closeFeedback()">&times;</span>
        <h2 class="text-center text-white font-bold text-2xl mb-4">Feedback & Rating</h2>
        <form method="post" class="space-y-3">
            <label class="text-white">Select Doctor:</label>
            <select name="doctor_id" required class="w-full p-2 rounded bg-white text-black">
                <?php foreach($doctors as $doc): ?>
                <option value="<?= $doc['id'] ?>"><?= htmlspecialchars($doc['fullname']) ?></option>
                <?php endforeach; ?>
            </select>
            <label class="text-white">Feedback:</label>
            <textarea name="comment" placeholder="Type your feedback..." required class="w-full p-2 rounded text-black"></textarea>
            <label class="text-white">Rating (1 to 5):</label>
            <input type="number" name="rating" min="1" max="5" required class="w-full p-2 rounded text-black">
            <button type="submit" class="w-full py-2 rounded-full bg-white text-neon font-bold hover:scale-105 transition">Submit</button>
        </form>
    </div>
</div>

<script>
function openFeedback(){ document.getElementById('feedbackModal').classList.remove('hidden'); }
function closeFeedback(){ document.getElementById('feedbackModal').classList.add('hidden'); }
window.onclick = function(e){ if(e.target.id==='feedbackModal') closeFeedback(); }
</script>

</body>
</html>
