<?php
if (!isset($page_title)) $page_title = "Agri Market Price Tracker";
$hide_main_nav = $hide_main_nav ?? false;
$current_page = basename($_SERVER['PHP_SELF']);

// Helper: adds class="active" to the link of the page being viewed
function nav_active($file, $current) {
    return $file === $current ? ' class="active" aria-current="page"' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1e5631">
    <title><?= htmlspecialchars($page_title) ?> | Malawi Farmers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<?php if (!$hide_main_nav): ?>
    <header class="site-header">
        <div class="container nav">
            <div class="logo">
                <a href="index.php">&#127806; AgriPrice Malawi</a>
            </div>

            <button type="button" class="nav-toggle" id="navToggle"
                    aria-label="Toggle navigation" aria-expanded="false" aria-controls="navPanel">&#9776;</button>

            <div class="nav-panel" id="navPanel">
                <nav aria-label="Main navigation">
                    <a href="index.php"<?= nav_active('index.php', $current_page) ?>>Home</a>
                    <a href="market-prices.php"<?= nav_active('market-prices.php', $current_page) ?>>Market Prices</a>
                    <a href="contracts.php"<?= nav_active('contracts.php', $current_page) ?>>Contracts</a>
                    <a href="outside-markets.php"<?= nav_active('outside-markets.php', $current_page) ?>>Outside Markets</a>
                    <a href="trending.php"<?= nav_active('trending.php', $current_page) ?>>Trending Crops</a>
                </nav>
                <div class="auth">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="login.php" class="btn">Login</a>
                        <a href="register.php" class="btn btn-outline">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
<?php endif; ?>

<?php if (isset($_SESSION['user_id'])): ?>
<div class="user-bar">
    <div class="container user-bar-inner">
        <div class="menu-dropdown" id="userMenu">
            <button type="button" class="hamburger-btn" id="userMenuBtn"
                    aria-label="Quick actions menu" aria-expanded="false">&#9776;</button>
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
                <a href="logout.php" class="logout-link">Logout</a>
            </div>
        </div>

        <span class="user-name">
            <?= htmlspecialchars($_SESSION['fullname']) ?>
            (<?= ucfirst($_SESSION['role'] ?? 'farmer') ?>)
        </span>
    </div>
</div>
<?php endif; ?>

<main class="container" id="main-content">

<script>
(function () {
    // Main site navigation (mobile)
    var navToggle = document.getElementById('navToggle');
    var navPanel  = document.getElementById('navPanel');
    if (navToggle && navPanel) {
        navToggle.addEventListener('click', function () {
            var open = navPanel.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // Logged-in quick actions dropdown
    var userMenu = document.getElementById('userMenu');
    var userBtn  = document.getElementById('userMenuBtn');
    if (userMenu && userBtn) {
        userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = userMenu.classList.toggle('show');
            userBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        document.addEventListener('click', function (e) {
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove('show');
                userBtn.setAttribute('aria-expanded', 'false');
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                userMenu.classList.remove('show');
                userBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }
})();
</script>