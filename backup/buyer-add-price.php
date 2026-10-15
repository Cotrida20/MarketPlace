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

<style>
.page-layout {
    display: flex;
    gap: 15px;
    margin-top: 15px;
    align-items: stretch;
}
.side-menu {
    width: 230px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-top: 5px solid #2e7d32;
    display: flex;
    flex-direction: column;
}
.side-menu .side-header {
    background: #1b5e20;
    color: white;
    padding: 15px 18px;
    font-weight: 600;
}
.side-menu a {
    display: block;
    padding: 12px 18px;
    color: #333;
    text-decoration: none;
    border-bottom: 1px solid #f0f0f0;
    font-size: 0.95rem;
}
.side-menu a:hover,
.side-menu a.active {
    background: #e8f5e9;
    color: #1b5e20;
    font-weight: 500;
}
.side-menu .menu-fill { flex-grow: 1; }
.main-content {
    flex: 1;
    max-width: 520px;
}
.form-box {
    background: white;
    padding: 25px 28px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(46,125,50,0.10);
    border-top: 5px solid #8bc34a;
}
.form-box h1 {
    margin: 0 0 18px;
    color: #1b5e20;
    font-size: 1.4rem;
}
.form-box label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}
.form-box input {
    width: 100%;
    padding: 10px 12px;
    margin-bottom: 14px;
    border: 1.5px solid #a5d6a7;
    border-radius: 6px;
}
.form-box .btn {
    width: 100%;
    padding: 11px;
}
</style>

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