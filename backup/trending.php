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

<style>
.page-layout {
    display: flex;
    gap: 20px;
    margin-top: 20px;
    align-items: flex-start;
}
.side-menu {
    width: 230px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-top: 5px solid #2e7d32;
    position: sticky;
    top: 20px;
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
}
.side-menu a:hover,
.side-menu a.active {
    background: #e8f5e9;
    color: #1b5e20;
    font-weight: 500;
}
.main-content {
    flex: 1;
    background: white;
    border-radius: 12px;
    padding: 25px 30px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    border-top: 5px solid #8bc34a;
}
.main-content h1 {
    margin-top: 0;
    color: #1b5e20;
}
.trend-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}
.trend-card {
    background: #f1f8e9;
    border-radius: 10px;
    padding: 20px;
    border-left: 5px solid #2e7d32;
}
.trend-card h3 {
    margin: 0 0 8px;
    color: #1b5e20;
}
</style>

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