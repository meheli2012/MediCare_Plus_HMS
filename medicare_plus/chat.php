<?php
session_start();
if(!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['patient','doctor'])){
    header("Location: login.php");
    exit;
}

include 'includes/db.php';

$user_id = intval($_SESSION['user_id']);
$user_role = $_SESSION['role'];
$opposite_role = ($user_role === 'patient') ? 'doctor' : 'patient';

// Selected chat partner
$partner_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

// Prevent selecting user of the same role
if($partner_id > 0){
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id=?");
    $stmt->execute([$partner_id]);
    $role_check = $stmt->fetchColumn();
    if($role_check !== $opposite_role){
        $partner_id = 0; // reset if wrong role
    }
}

// Handle sending new message
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $partner_id > 0){
    $msg = trim($_POST['message']);
    if($msg !== ''){
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $partner_id, $msg]);
        header("Location: chat.php?user_id=$partner_id");
        exit;
    }
}

// Fetch selected partner info
$partner = null;
if($partner_id > 0){
    $stmt = $pdo->prepare("SELECT id, fullname FROM users WHERE id=? AND role=?");
    $stmt->execute([$partner_id, $opposite_role]);
    $partner = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch list of opposite-role users (with search)
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
if($search){
    $stmt = $pdo->prepare("SELECT id, fullname FROM users WHERE role=? AND fullname LIKE ? LIMIT 50");
    $stmt->execute([$opposite_role, "%$search%"]);
} else {
    $stmt = $pdo->prepare("SELECT id, fullname FROM users WHERE role=? LIMIT 50");
    $stmt->execute([$opposite_role]);
}
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch conversation
$messages = [];
if($partner){
    $stmt = $pdo->prepare("
        SELECT m.*, u.fullname AS sender_name
        FROM messages m
        JOIN users u ON u.id = m.sender_id
        WHERE (sender_id=? AND receiver_id=?) OR (sender_id=? AND receiver_id=?)
        ORDER BY sent_at ASC
    ");
    $stmt->execute([$user_id, $partner_id, $partner_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chat - Medicare Plus</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 to-purple-900 min-h-screen flex items-center justify-center p-6 font-sans">

<div class="flex w-full max-w-6xl h-[80vh] bg-white rounded-xl shadow-lg overflow-hidden">

    <!-- Sidebar: opposite-role users -->
    <div class="w-64 bg-gray-100 overflow-y-auto border-r border-gray-300">
        <div class="p-4 text-center font-bold text-xl text-pink-600 uppercase"><?= ucfirst($opposite_role) ?>s</div>
        <form method="GET" class="p-4">
            <input type="text" name="q" placeholder="Search..." value="<?= htmlspecialchars($search) ?>" class="w-full px-3 py-2 border rounded-lg">
        </form>
        <ul>
            <?php foreach($users as $u): ?>
            <li class="px-4 py-2 hover:bg-pink-50 border-b">
                <a href="chat.php?user_id=<?= $u['id'] ?>" class="block text-pink-600 font-semibold"><?= htmlspecialchars($u['fullname']) ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Chat area -->
    <div class="flex-1 flex flex-col relative p-4">
        <a href="logout.php" class="absolute top-4 right-4 px-4 py-2 bg-gray-700 text-white rounded hover:bg-black">Logout</a>

        <?php if($partner): ?>
        <h3 class="text-2xl font-bold text-pink-600 mb-4">
            Chat with <?= ($user_role==='patient'?'Dr. ':'') . htmlspecialchars($partner['fullname']) ?>
        </h3>

        <div class="flex-1 overflow-y-auto p-3 space-y-3 border rounded-lg bg-gray-50" id="messages">
            <?php foreach($messages as $msg): ?>
                <div class="max-w-[70%] px-4 py-2 rounded-lg <?= ($msg['sender_id']==$user_id)?'bg-pink-600 text-white ml-auto':'bg-gray-200 text-black mr-auto' ?>">
                    <strong><?= htmlspecialchars($msg['sender_name']) ?>:</strong><br>
                    <?= nl2br(htmlspecialchars($msg['message'])) ?>
                    <div class="text-xs text-gray-700 mt-1"><?= date('M j, g:i A', strtotime($msg['sent_at'])) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" class="mt-3 flex space-x-2">
            <textarea name="message" placeholder="Type your message..." required class="flex-1 px-3 py-2 border rounded-lg"></textarea>
            <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded hover:bg-pink-700">Send</button>
        </form>

        <?php else: ?>
            <p class="text-center text-gray-700 mt-10">Select a <?= $opposite_role ?> to start chatting.</p>
        <?php endif; ?>

    </div>

</div>

<script>
var msgBox = document.getElementById("messages");
if(msgBox) msgBox.scrollTop = msgBox.scrollHeight;
</script>

</body>
</html>
