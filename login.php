<?php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = preg_replace('/\s+/', '', $_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Wrong phone number or password.";
    }
}

$page_title = "Login";
require 'includes/header.php';
?>

<div class="form-box">
    <h1>Login</h1>
    <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST">
        <label>Phone Number</label>
        <input type="text" name="phone" required placeholder="0888123456">

        <label>Password</label>
        <input type="password" name="password" required placeholder="Your password">

        <button type="submit" class="btn" style="width:100%">Login</button>
    </form>
    <p style="margin-top:18px; text-align:center;">No account yet? <a href="register.php" style="color:#2e7d32; font-weight:600;">Register here</a></p>
</div>

<?php require 'includes/footer.php'; ?>