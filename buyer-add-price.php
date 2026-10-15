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
    $contact = preg_replace('/\D/', '', $_POST['contact'] ?? '');

    if ($crop === '' || $price <= 0) {
        $error = "Crop name and a valid price are required.";
    } elseif ($location === '') {
        $error = "Please enter the location.";
    } elseif (strlen($contact) !== 10) {
        $error = "Phone number must be exactly 10 digits.";
    } else {
        $stmt = $db->prepare("INSERT INTO outside_markets (company, crop, quantity, amount_mk, location, contact) 
                            VALUES (?, ?, ?, ?, ?, ?)");
        $company = $_SESSION['fullname'];
        $quantity = 1000;
        $amount = $price * $quantity;
        $stmt->execute([$company, $crop, $quantity, $amount, $location, $contact]);
        
        $stmt2 = $db->prepare("INSERT INTO market_prices (crop, price_per_kg, location) VALUES (?, ?, ?)");
        $stmt2->execute([$crop, $price, $location]);

        $message = "Your price offer has been posted successfully and is now visible in the admin panel and market prices!";
    }
}

if (isset($_POST['add_contract'])) {
    $crop = trim($_POST['crop'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 0);
    $amount_mk = (float)($_POST['amount_mk'] ?? 0);
    $deadline = trim($_POST['deadline'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($crop === '' || $quantity <= 0 || $amount_mk <= 0 || $deadline === '') {
        $error = "All contract fields are required.";
    } else {
        $company = $_SESSION['fullname'];
        $stmt = $db->prepare("INSERT INTO contracts (company, crop, quantity, amount_mk, deadline, description, status) VALUES (?, ?, ?, ?, ?, ?, 'open')");
        $stmt->execute([$company, $crop, $quantity, $amount_mk, $deadline, $description]);
        $message = "Contract created and posted successfully!";
    }
}

$page_title = "Buyer Portal";
$hide_main_nav = true;
require 'includes/header.php';
?>

<div class="page-layout">

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
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-box" style="margin-bottom: 40px;">
            <h1>Post Price Offer</h1>
            <form method="POST">
                <label>Crop Name</label>
                <input type="text" name="crop" required>

                <label>Price per kg (MK)</label>
                <input type="number" name="price" required min="1">

                <label>Your Location</label>
                <input type="text" name="location" required>

                <label>Contact Phone (10 digits)</label>
                <input type="text" name="contact" required maxlength="10" pattern="[0-9]{10}" placeholder="0888123456">

                <button type="submit" name="add_price" class="btn">Post Price Offer</button>
            </form>
        </div>

        <div class="form-box">
            <h1>Add Contract</h1>
            <form method="POST">
                <label>Crop Name</label>
                <input type="text" name="crop" required>

                <label>Quantity Needed (kg)</label>
                <input type="number" name="quantity" required min="1">

                <label>Total Amount (MK)</label>
                <input type="number" name="amount_mk" required min="1">

                <label>Deadline</label>
                <input type="date" name="deadline" required>

                <label>Description</label>
                <textarea name="description" rows="3" style="width:100%; padding:10px; border:1.5px solid #c8e6c9; border-radius:8px;"></textarea>

                <button type="submit" name="add_contract" class="btn" style="margin-top:15px;">Post Contract</button>
            </form>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>