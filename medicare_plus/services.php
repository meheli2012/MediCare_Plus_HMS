<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}
include 'includes/db.php';
$user_id = $_SESSION['user_id'];

// Handle feedback submission
if($_SERVER['REQUEST_METHOD']=='POST' && $_SESSION['role']=='patient'){
    $doctor_id = $_POST['doctor_id'];
    $comment = $_POST['comment'];
    $rating = $_POST['rating'];
    $stmt = $pdo->prepare("INSERT INTO feedback(doctor_id, patient_id, comment, rating) VALUES(?,?,?,?)");
    $stmt->execute([$doctor_id,$user_id,$comment,$rating]);
    $success = "Feedback submitted successfully!";
}

// Fetch services
$services = $pdo->query("SELECT * FROM services")->fetchAll();

// Fetch feedback
$feedbacks = $pdo->query("SELECT f.*, u.fullname as patient_name FROM feedback f JOIN users u ON f.patient_id=u.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Services - MediCare Plus</title>
<style>
body{
    font-family:Arial,sans-serif;
    margin:0;
    padding:0;
    background:#f0f2f5;}
header{
    background:#2a9df4;
    color:#fff;
    padding:20px;
    text-align:center;
    position:relative;}
header a{
    color:#fff;
    position:absolute;
    right:20px;
    top:25px;
    text-decoration:none;
    font-weight:bold;}
header a:hover{
    text-decoration:underline;}
.container{
    padding:20px;}
h2{
    color:#2a9df4;
    margin-top:30px;}
.service-card{
    background:#fff;
    padding:15px;
    margin:10px 0;
    box-shadow:0 0 5px rgba(0,0,0,0.1);
    border-radius:5px;}
form textarea, input[type=number], select, button{
    width:100%;
    padding:10px;
    margin:5px 0;
    border-radius:5px;
    border:1px solid #ccc;}
button{
    background:#2a9df4;
    color:#fff;
    border:none;
    cursor:pointer;}
button:hover{
    background:#1e7cd4;}
.success{
    color:green;}
</style>
</head>
<body>
<header>
<h1>Our Services</h1>
<a href="logout.php">Logout</a>
</header>
<div class="container">

<?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>

<h2>Services</h2>
<?php foreach($services as $s){ ?>
<div class="service-card">
<h3><?= $s['name'] ?></h3>
<p><?= $s['description'] ?></p>
</div>
<?php } ?>

<?php if($_SESSION['role']=='patient'){ ?>
<h2>Leave Feedback</h2>
<form method="POST">
<select name="doctor_id" required>
<option value="">Select Doctor</option>
<?php
$doctors = $pdo->query("SELECT * FROM users WHERE role='doctor'")->fetchAll();
foreach($doctors as $d){ echo "<option value='".$d['id']."'>".$d['fullname']."</option>"; }
?>
</select>
<textarea name="comment" placeholder="Your feedback..." required></textarea>
<input type="number" name="rating" min="1" max="5" placeholder="Rating (1-5)" required>
<button type="submit">Submit Feedback</button>
</form>
<?php } ?>

<h2>All Feedback</h2>
<?php foreach($feedbacks as $f){ ?>
<p><strong><?= $f['patient_name'] ?>:</strong> <?= $f['comment'] ?> <em>(Rating: <?= $f['rating'] ?>)</em></p>
<?php } ?>

</div>
</body>
</html>