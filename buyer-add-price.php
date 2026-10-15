<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['buyer', 'officer', 'admin'])) {
    header("Location: login.php");
    exit;
}

$message = '';
$error = '';

if (isset($_POST['add_price'])) {
    $crop = trim($_POST['crop'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    if ($crop === '' || $price <= 0) {
        $error = "Crop name and a valid price are required.";
    } elseif ($location === '') {
        $error = "Please enter the location.";
    } else {
        $stmt = $db->prepare("INSERT INTO outside_markets (company, crop, quantity, amount_mk, location, contact) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        $company = $_SESSION['fullname'];
        $quantity = 1000;
        $amount = $price * $quantity;
        $stmt->execute([$company, $crop, $quantity, $amount, $location, $contact]);
        $message = "Your price offer has been posted successfully!";
    }
}

$page_title = "Post Price Offer";
$hide_main_nav = true;
require 'includes/header.php';
?>

<div class="page-layout">

    <!-- SAME QUICK MENU -->
    <div class="side-menu">
        <div class="side-header">Quick Menu</div>
        <?php if (in_array($_SESSION['role'] ?? '', ['officer', 'admin'])): ?>
            <a href="admin.php">Admin Dashboard</a>
            <a href="admin.php?section=prices">Manage Prices</a>
            <a href="admin.php?section=contracts">View All Contracts</a>
        <?php endif; ?>
        <a href="buyer-add-price.php" class="active">Post Price Offer</a>
        <a href="outside-markets.php">View Buyer Offers</a>
        <a href="market-prices.php">Current Market Prices</a>
        <a href="contracts.php">Browse Contracts</a>
        <a href="trending.php">Trending Crops</a>
        <a href="logout.php" style="color:#c62828;">Logout</a>
        <div class="menu-fill"></div>
    </div>

    <div class="main-content">
        <div class="form-box">
            <h1>Post Price Offer</h1>

            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <label>Crop Name</label>
                <input type="text" name="crop" required>

                <label>Price per kg (MK)</label>
                <input type="number" name="price" required min="1">

                <label>Your Location</label>
                <input type="text" name="location" required>

                <label>Contact Phone</label>
                <input type="text" name="contact">

                <button type="submit" name="add_price" class="btn">Post Price Offer</button>
            </form>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>