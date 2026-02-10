<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

$patientId = intval($_SESSION['user_id']);
$feedbackMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'], $_POST['doctor_id'], $_POST['rating'])) {
    $doctorId = intval($_POST['doctor_id']);
    $comment  = trim($_POST['comment']);
    $rating   = intval($_POST['rating']);

    if ($doctorId > 0 && $comment !== '' && $rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO feedback (doctor_id, patient_id, comment, rating) VALUES (?, ?, ?, ?)");
        $stmt->execute([$doctorId, $patientId, $comment, $rating]);
        $feedbackMessage = "Feedback and rating submitted successfully!";
    } else {
        $feedbackMessage = "Error: Please fill all fields correctly.";
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$patientId]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$patient) { session_unset(); session_destroy(); header("Location: login.php"); exit; }

$stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$stmt->execute([$patientId]);
$newMessageCount = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM bills WHERE patient_id = ? AND (receipt IS NULL OR receipt = '')");
$stmt->execute([$patientId]);
$pendingReceiptCount = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT pr.id, pr.file_path, pr.uploaded_at, u.fullname AS doctor_name FROM patient_reports pr JOIN users u ON pr.doctor_id = u.id WHERE pr.patient_id = ? ORDER BY pr.uploaded_at DESC");
$stmt->execute([$patientId]);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
$reportsCount = count($reports); // ✅ Assign separately

$stmt = $pdo->prepare("SELECT id, fullname FROM users WHERE role='doctor' ORDER BY fullname");
$stmt->execute();
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Medicare Plus - Patient Dashboard</title>
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
        fadeUp: {
          '0%': { opacity: '0', transform: 'translateY(50px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' }
        }
      },
      animation: {
        fadeUp: 'fadeUp 0.8s ease forwards'
      }
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
    <p class="text-slate-300 mt-2">Welcome, <?= htmlspecialchars($patient['fullname']) ?></p>
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
        ['title'=>'My Profile','desc'=>'Update personal info','link'=>'profile.php','icon'=>'👤','color'=>'from-glowpink to-glowblue'],
        ['title'=>'Book Appointment','desc'=>'Schedule doctor visit','link'=>'book_appointment.php','icon'=>'📅','color'=>'from-glowblue to-glowpink'],
        ['title'=>'Appointments','desc'=>'Manage appointments','link'=>'appointments.php','icon'=>'🩺','color'=>'from-glowpink to-glowblue'],
        ['title'=>'Health Reports','desc'=>"Download reports ($reportsCount)",'link'=>'patient_reports.php','icon'=>'📄','color'=>'from-glowblue to-glowpink'],
        ['title'=>'Messages','desc'=>($newMessageCount>0?"$newMessageCount new messages":"No new messages"),'link'=>'chat.php','icon'=>'✉️','color'=>'from-glowpink to-glowblue'],
        ['title'=>'Payments','desc'=>($pendingReceiptCount>0?"$pendingReceiptCount receipts missing":"All receipts submitted"),'link'=>'billing.php','icon'=>'💳','color'=>'from-glowblue to-glowpink'],
        ['title'=>'Rate Doctor','desc'=>'Rate your doctor after appointment','link'=>'#','icon'=>'⭐','color'=>'from-glowpink to-glowblue','onclick'=>'openFeedback()']
    ];
    foreach($cards as $card): ?>
    <div class="bg-white/10 backdrop-blur-lg p-6 rounded-3xl shadow-glow hover:scale-105 transition animate-fadeUp cursor-pointer" <?= isset($card['onclick'])?"onclick=\"{$card['onclick']}\"":"" ?>>
        <div class="text-4xl mb-3"><?= $card['icon'] ?></div>
        <h3 class="text-2xl font-bold mb-2 bg-clip-text text-transparent bg-gradient-to-r <?= $card['color'] ?>"><?= $card['title'] ?></h3>
        <p class="text-slate-200"><?= $card['desc'] ?></p>
        <?php if(!isset($card['onclick'])): ?>
        <a href="<?= $card['link'] ?>" class="mt-4 inline-block px-6 py-2 rounded-full bg-gradient-to-r <?= $card['color'] ?> text-black font-semibold hover:shadow-glow transition">Open</a>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<!-- LOGOUT -->
<div class="text-center my-10">
    <a href="logout.php" class="px-8 py-3 bg-red-700 rounded-full font-bold hover:scale-105 transition">Logout</a>
</div>

<!-- FEEDBACK MODAL -->
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
