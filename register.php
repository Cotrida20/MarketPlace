<?php
require 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone  = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'farmer';

    if (empty($fullname) || empty($phone) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $db->prepare("INSERT INTO users (fullname, phone, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$fullname, $phone, $hashed, $role]);
            $success = "Registration successful! You can now login.";
        } catch (PDOException $e) {
            $error = "Phone number already registered.";
        }
    }
}

$page_title = "Register";
require 'includes/header.php';
?>

<div class="form-box">
    <h1>Create Account</h1>
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="fullname" required placeholder="Enter your full name">

        <label>Phone Number</label>
        <input type="text" name="phone" placeholder="0888 123 456" required>

        <label>I am a:</label>
        <select name="role" required style="width:100%; padding:12px; margin:8px 0 18px; border:1.5px solid #a5d6a7; border-radius:7px;">
            <option value="farmer">Farmer</option>
            <option value="buyer">Buyer / Trader</option>
            <option value="officer">Agricultural Extension Officer</option>
        </select>

        <label>Password</label>
        <input type="password" name="password" required placeholder="Create a password">

        <button type="submit" class="btn" style="width:100%">Register</button>
    </form>
    <p style="margin-top:18px; text-align:center;">
        Already have an account? <a href="login.php" style="color:#2e7d32; font-weight:600;">Login here</a>
    </p>
</div>

<?php require 'includes/footer.php'; ?>