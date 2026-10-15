<?php
require 'config.php';

$page_title = "Buyer Offers";
$hide_main_nav = isset($_SESSION['user_id']);
require 'includes/header.php';

$offers = $db->query("SELECT * FROM outside_markets ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

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