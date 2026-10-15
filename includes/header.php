<?php
if (!isset($page_title)) $page_title = "Agri Market Price Tracker";
$hide_main_nav = $hide_main_nav ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> | Malawi Farmers</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .user-bar {
            background: #e8f5e9;
            border-bottom: 1px solid #c8e6c9;
            padding: 8px 0;
        }
        .user-bar-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-name {
            font-weight: 600;
            color: #1b5e20;
            font-size: 0.95rem;
        }
        .menu-dropdown {
            position: relative;
            display: inline-block;
        }
        .hamburger-btn {
            background: #2e7d32;
            border: none;
            color: white;
            width: 40px;
            height: 36px;
            border-radius: 7px;
            cursor: pointer;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hamburger-btn:hover {
            background: #1b5e20;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            left: 0;
            top: 44px;
            background: white;
            min-width: 240px;
            box-shadow: 0 10px 28px rgba(0,0,0,0.15);
            border-radius: 10px;
            z-index: 999;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        .dropdown-content .dropdown-header {
            background: #1b5e20;
            color: white;
            padding: 12px 18px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .dropdown-content a {
            color: #333;
            padding: 12px 18px;
            text-decoration: none;
            display: block;
            font-size: 0.95rem;
            border-bottom: 1px solid #f0f0f0;
        }
        .dropdown-content a:hover {
            background: #f1f8e9;
            color: #1b5e20;
            padding-left: 22px;
        }
        .menu-dropdown.show .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>

<?php if (!$hide_main_nav): ?>
    <header>
        <div class="container nav">
            <div class="logo">
                <a href="index.php">🌾 AgriPrice Malawi</a>
            </div>
            <nav>
                <a href="index.php">Home</a>
                <a href="market-prices.php">Market Prices</a>
                <a href="contracts.php">Contracts</a>
                <a href="outside-markets.php">Outside Markets</a>
                <a href="trending.php">Trending Crops</a>
            </nav>
            <div class="auth">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="btn">Login</a>
                    <a href="register.php" class="btn btn-outline">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
<?php endif; ?>

<?php if (isset($_SESSION['user_id'])): ?>
<div class="user-bar">
    <div class="user-bar-inner">
        <div class="menu-dropdown" id="userMenu">
            <button type="button" class="hamburger-btn" onclick="toggleMenu()">☰</button>
            <div class="dropdown-content">
                <div class="dropdown-header">Quick Actions</div>

                <?php if (in_array($_SESSION['role'] ?? '', ['officer', 'admin'])): ?>
                    <!-- Admin Dashboard goes to Home page -->
                    <a href="index.php">Admin Dashboard</a>
                    <a href="admin.php?section=prices">Manage Prices</a>
                    <a href="admin.php?section=contracts">View All Contracts</a>
                <?php endif; ?>

                <?php if (in_array($_SESSION['role'] ?? '', ['buyer', 'officer', 'admin'])): ?>
                    <a href="buyer-add-price.php">Post Price Offer</a>
                    <a href="outside-markets.php">View Buyer Offers</a>
                <?php endif; ?>

                <a href="market-prices.php">Current Market Prices</a>
                <a href="contracts.php">Browse Contracts</a>
                <a href="trending.php">Trending Crops</a>
                <a href="logout.php" style="color:#c62828;">Logout</a>
            </div>
        </div>

        <span class="user-name">
            <?= htmlspecialchars($_SESSION['fullname']) ?> 
            (<?= ucfirst($_SESSION['role'] ?? 'farmer') ?>)
        </span>
    </div>
</div>
<?php endif; ?>

<main class="container">

<script>
function toggleMenu() {
    document.getElementById('userMenu').classList.toggle('show');
}
document.addEventListener('click', function(event) {
    const menu = document.getElementById('userMenu');
    if (menu && !menu.contains(event.target)) {
        menu.classList.remove('show');
    }
});
</script>