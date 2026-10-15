<?php
require 'config.php';

$page_title = "Trending Crops";
$hide_main_nav = isset($_SESSION['user_id']);
require 'includes/header.php';

$trending = [
    ['name' => 'Maize', 'reason' => 'High demand across the country'],
    ['name' => 'Tobacco', 'reason' => 'Strong export market'],
    ['name' => 'Groundnuts', 'reason' => 'Rising local and regional demand'],
    ['name' => 'Soybean', 'reason' => 'Growing industrial use'],
    ['name' => 'Rice', 'reason' => 'Steady consumption growth'],
    ['name' => 'Beans', 'reason' => 'Popular in local markets'],
];
?>

<?php if (isset($_SESSION['user_id'])): ?>
<div class="page-layout">

    <div class="side-menu">
        <div class="side-header">Quick Menu</div>
        <?php if (in_array($_SESSION['role'] ?? '', ['officer', 'admin'])): ?>
            <a href="admin.php">Admin Dashboard</a>
            <a href="admin.php?section=prices">Manage Prices</a>
            <a href="admin.php?section=contracts">View All Contracts</a>
        <?php endif; ?>
        <?php if (in_array($_SESSION['role'] ?? '', ['buyer', 'officer', 'admin'])): ?>
            <a href="buyer-add-price.php">Post Price Offer</a>
            <a href="outside-markets.php">View Buyer Offers</a>
        <?php endif; ?>
        <a href="market-prices.php">Current Market Prices</a>
        <a href="contracts.php">Browse Contracts</a>
        <a href="trending.php" class="active">Trending Crops</a>
        <a href="logout.php" style="color:#c62828;">Logout</a>
    </div>

    <div class="main-content">
        <h1>Trending Crops</h1>
        <div class="trend-grid">
            <?php foreach ($trending as $t): ?>
            <div class="trend-card">
                <h3><?= htmlspecialchars($t['name']) ?></h3>
                <p><?= htmlspecialchars($t['reason']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php else: ?>
<h1>Trending Crops</h1>
<div class="trend-grid">
    <?php foreach ($trending as $t): ?>
    <div class="trend-card">
        <h3><?= htmlspecialchars($t['name']) ?></h3>
        <p><?= htmlspecialchars($t['reason']) ?></p>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>