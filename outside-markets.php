<?php
require 'config.php';

$page_title = "Buyer Offers";
$hide_main_nav = isset($_SESSION['user_id']);
require 'includes/header.php';

$offers = $db->query("SELECT * FROM outside_markets ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
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
    overflow: hidden;
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
    margin-bottom: 20px;
    color: #1b5e20;
}
</style>

<?php if (isset($_SESSION['user_id'])): ?>
<div class="page-layout">

    <!-- SAME QUICK MENU -->
    <div class="side-menu">
        <div class="side-header">Quick Menu</div>
        <?php if (in_array($_SESSION['role'] ?? '', ['officer', 'admin'])): ?>
            <a href="admin.php">Admin Dashboard</a>
            <a href="admin.php?section=prices">Manage Prices</a>
            <a href="admin.php?section=contracts">View All Contracts</a>
        <?php endif; ?>
        <?php if (in_array($_SESSION['role'] ?? '', ['buyer', 'officer', 'admin'])): ?>
            <a href="buyer-add-price.php">Post Price Offer</a>
            <a href="outside-markets.php" class="active">View Buyer Offers</a>
        <?php endif; ?>
        <a href="market-prices.php">Current Market Prices</a>
        <a href="contracts.php">Browse Contracts</a>
        <a href="trending.php">Trending Crops</a>
        <a href="logout.php" style="color:#c62828;">Logout</a>
    </div>

    <div class="main-content">
        <h1>Buyer Offers</h1>

        <table>
            <thead>
                <tr>
                    <th>Buyer Name</th>
                    <th>Crop</th>
                    <th>Quantity (kg)</th>
                    <th>Price / Amount (MK)</th>
                    <th>Location</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($offers)): ?>
                    <tr><td colspan="6">No buyer offers yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($offers as $o): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($o['company']) ?></strong></td>
                        <td><?= htmlspecialchars($o['crop']) ?></td>
                        <td><?= number_format($o['quantity']) ?></td>
                        <td class="price">MK <?= number_format($o['amount_mk'], 0) ?></td>
                        <td><?= htmlspecialchars($o['location'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($o['contact'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<h1>Buyer Offers</h1>
<table>
    <thead>
        <tr>
            <th>Buyer Name</th>
            <th>Crop</th>
            <th>Quantity (kg)</th>
            <th>Price / Amount (MK)</th>
            <th>Location</th>
            <th>Phone</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($offers)): ?>
            <tr><td colspan="6">No buyer offers yet.</td></tr>
        <?php else: ?>
            <?php foreach ($offers as $o): ?>
            <tr>
                <td><strong><?= htmlspecialchars($o['company']) ?></strong></td>
                <td><?= htmlspecialchars($o['crop']) ?></td>
                <td><?= number_format($o['quantity']) ?></td>
                <td class="price">MK <?= number_format($o['amount_mk'], 0) ?></td>
                <td><?= htmlspecialchars($o['location'] ?? '-') ?></td>
                <td><?= htmlspecialchars($o['contact'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>