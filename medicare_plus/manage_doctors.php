<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}
include 'includes/db.php';

// Add Doctor
if(isset($_POST['add_doctor'])){
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $specialization = trim($_POST['specialization']);
    $experience = trim($_POST['experience']);

    $stmt = $pdo->prepare("INSERT INTO users (role, fullname, email, password_hash, specialization, experience) VALUES ('doctor', ?, ?, ?, ?, ?)");
    $stmt->execute([$fullname, $email, $password, $specialization, $experience]);
    $msg = "Doctor added successfully.";
}

// Edit Doctor
if(isset($_POST['edit_doctor'])){
    $id = $_POST['doctor_id'];
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $specialization = trim($_POST['specialization']);
    $experience = trim($_POST['experience']);

    $stmt = $pdo->prepare("UPDATE users SET fullname=?, email=?, specialization=?, experience=? WHERE id=? AND role='doctor'");
    $stmt->execute([$fullname, $email, $specialization, $experience, $id]);
    $msg = "Doctor updated successfully.";
}

// Delete Doctor
if(isset($_POST['delete_doctor'])){
    $id = $_POST['doctor_id'];

    $check = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE doctor_id=?");
    $check->execute([$id]);
    $count = $check->fetchColumn();

    if($count == 0){
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role='doctor'");
        $stmt->execute([$id]);
        $msg = "Doctor deleted successfully.";
    } else {
        $msg = "Cannot delete doctor. Doctor has existing appointments.";
    }
}

// Fetch Doctors
$doctors = $pdo->query("SELECT * FROM users WHERE role='doctor' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Dashboard-style colors
$cardColors = ['#e74c3c','#3498db','#2ecc71','#f39c12','#9b59b6','#1abc9c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Doctors</title>
<style>
body{
    margin:0;font-family:'Segoe UI',
    Arial,sans-serif;
    background:#f0f2f5;}
.header{
    text-align:center;
    padding:40px 20px;
    background:white;
    border-bottom:4px solid #e60000;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.logo{
    width:100px;
    height:100px;
    background:#e60000;
    display:flex;
    justify-content:center;
    align-items:center;
    border-radius:50%;
    margin:0 auto;
    box-shadow:0 4px 15px rgba(0,0,0,0.2);}
.logo span{
    font-size:50px;
    font-weight:bold;
    color:white;}
.header h1{
    margin-top:15px;
    font-size:36px;
    color:#e60000;}
.cards-container{
    max-width:1200px;
    margin:40px auto;
    display:flex;
    flex-wrap:wrap;
    gap:25px;
    justify-content:center;}
.card{
    padding:25px;
    width:250px;
    border-radius:18px;
    text-align:center;
    box-shadow:0 6px 20px rgba(0,0,0,0.15);
    transition:0.3s;
    color:white;}
.card h3{
    margin-bottom:10px;
    font-size:22px;
    padding:5px 0;
    border-radius:10px;}
.card p{
    font-size:15px;
    margin-bottom:12px;}
.card button{
    display:inline-block;
    padding:8px 15px;
    margin:5px;
    border:none;
    border-radius:6px;
    font-weight:bold;
    cursor:pointer;transition:0.3s;}
.card button.edit-btn{
    background:white;
    color:black;}
.card button.edit-btn:hover{
    background:rgba(255,255,255,0.85);}
.card button.delete-btn{
    background:white;
    color:red;}
.card button.delete-btn:hover{
    background:red;
    color:white;}

.add-button{
    text-align:center;
    margin:20px;}
.add-button a{
    padding:12px 25px;
    background:#e60000;
    color:white;
    text-decoration:none;
    border-radius:30px;
    font-size:18px;
    display:inline-block;}
.add-button a:hover{
    background:#b30000;}
.logout-button{
    text-align:center;
    margin:40px 0;}
.logout-button a{
    padding:12px 30px;
    background:#444;
    color:white;font-size:18px;
    text-decoration:none;
    border-radius:30px;}
.logout-button a:hover{
    background:black;}

/* Modal */
.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:99999;}
.modal-content{background:white;width:400px;padding:25px;margin:8% auto;border-radius:15px;animation:fadeIn .3s ease;}
@keyframes fadeIn{from{opacity:0;transform:scale(.9);}to{opacity:1;transform:scale(1);}}
.close{float:right;cursor:pointer;font-size:28px;color:#e60000;}
.modal-content h2{color:#e60000;text-align:center;margin-bottom:20px;}
.modal-content input{width:100%;padding:10px;margin-bottom:12px;border-radius:8px;border:1px solid #ccc;font-size:15px;}
.modal-content button{width:100%;padding:10px;background:#e60000;color:white;font-size:17px;border:none;border-radius:8px;cursor:pointer;}
.modal-content button:hover{background:#b30000;}
.message-box{text-align:center;margin:15px;color:green;font-weight:bold;}
</style>
</head>
<body>

<div class="header">
    <div class="logo"><span>+</span></div>
    <h1>Manage Doctors</h1>
</div>

<?php if(isset($msg)): ?>
<div class="message-box"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="add-button">
    <a href="#" onclick="openAddModal()">+ Add New Doctor</a>
</div>

<div class="cards-container">
<?php if(count($doctors) > 0): ?>
<?php foreach($doctors as $index => $doc): 
    $color = $cardColors[$index % count($cardColors)]; 
?>
    <div class="card" style="background:<?= $color ?>;">
        <h3><?= htmlspecialchars($doc['fullname']) ?></h3>
        <p><strong>Email:</strong> <?= htmlspecialchars($doc['email']) ?></p>
        <p><strong>Specialization:</strong> <?= htmlspecialchars($doc['specialization'] ?? '-') ?></p>
        <p><strong>Experience:</strong> <?= htmlspecialchars($doc['experience'] ?? '-') ?></p>
        <button class="edit-btn" onclick="openEditModal(<?= $doc['id'] ?>,'<?= htmlspecialchars($doc['fullname'],ENT_QUOTES) ?>','<?= htmlspecialchars($doc['email'],ENT_QUOTES) ?>','<?= htmlspecialchars($doc['specialization'],ENT_QUOTES) ?>','<?= htmlspecialchars($doc['experience'],ENT_QUOTES) ?>')">Edit</button>
        <button class="delete-btn" onclick="openDeleteModal(<?= $doc['id'] ?>)">Delete</button>
    </div>
<?php endforeach; ?>
<?php else: ?>
<p style="text-align:center;color:#555;font-size:18px;">No doctors found.</p>
<?php endif; ?>
</div>

<div class="logout-button">
    <a href="dashboard_admin.php">Back to Dashboard</a>
</div>

<!-- Add Doctor Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAddModal()">&times;</span>
        <h2>Add New Doctor</h2>
        <form method="post">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="specialization" placeholder="Specialization">
            <input type="text" name="experience" placeholder="Experience">
            <button type="submit" name="add_doctor">Add Doctor</button>
        </form>
    </div>
</div>

<!-- Edit Doctor Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Doctor</h2>
        <form method="post">
            <input type="hidden" id="edit_doctor_id" name="doctor_id">
            <input type="text" id="edit_fullname" name="fullname" placeholder="Full Name" required>
            <input type="email" id="edit_email" name="email" placeholder="Email" required>
            <input type="text" id="edit_specialization" name="specialization" placeholder="Specialization">
            <input type="text" id="edit_experience" name="experience" placeholder="Experience">
            <button type="submit" name="edit_doctor">Update Doctor</button>
        </form>
    </div>
</div>

<!-- Delete Doctor Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeDeleteModal()">&times;</span>
        <h2>Confirm Delete</h2>
        <form method="post">
            <input type="hidden" id="delete_doctor_id" name="doctor_id">
            <button type="submit" name="delete_doctor">Delete Doctor</button>
        </form>
    </div>
</div>

<script>
function openAddModal(){ document.getElementById("addModal").style.display="block"; }
function closeAddModal(){ document.getElementById("addModal").style.display="none"; }

function openEditModal(id,name,email,spec,exp){
    document.getElementById("edit_doctor_id").value=id;
    document.getElementById("edit_fullname").value=name;
    document.getElementById("edit_email").value=email;
    document.getElementById("edit_specialization").value=spec;
    document.getElementById("edit_experience").value=exp;
    document.getElementById("editModal").style.display="block";
}
function closeEditModal(){ document.getElementById("editModal").style.display="none"; }

function openDeleteModal(id){
    document.getElementById("delete_doctor_id").value=id;
    document.getElementById("deleteModal").style.display="block";
}
function closeDeleteModal(){ document.getElementById("deleteModal").style.display="none"; }

window.onclick=function(e){
    ['addModal','editModal','deleteModal'].forEach(id=>{
        let modal=document.getElementById(id);
        if(e.target===modal){ modal.style.display='none'; }
    });
}
</script>

</body>
</html>