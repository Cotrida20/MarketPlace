<?php
require 'config.php';

$page_title = "Market Prices";
$hide_main_nav = isset($_SESSION['user_id']);
require 'includes/header.php';

$role = $_SESSION['role'] ?? 'farmer';

$gov_prices = $db->query("SELECT crop, price_per_kg, location, '' AS contact, 'Government' AS source 
                          FROM market_prices ORDER BY crop")->fetchAll(PDO::FETCH_ASSOC);

$buyer_prices = $db->query("SELECT crop, ROUND(amount_mk / quantity, 0) AS price_per_kg, 
                                   location, contact, 'Buyer Offer' AS source
                            FROM outside_markets ORDER BY crop")->fetchAll(PDO::FETCH_ASSOC);

$all_prices = array_merge($gov_prices, $buyer_prices);
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
.source-gov { color: #2e7d32; font-weight: 600; }
.source-buyer { color: #1565c0; font-weight: 600; }
</style>

<?php if (isset($_SESSION['user_id'])): ?>
<div class="page-layout">

    <div class="side-menu">
        <div class="side-header">Quick Menu</div>

        <?php if (in_array($role, ['officer', 'admin'])): ?>
            <a href="index.php">Admin Dashboard</a>
            <a href="admin.php?section=prices">Manage Prices</a>
            <a href="admin.php?section=contracts">View All Contracts</a>
        <?php endif; ?>

        <?php if (in_array($role, ['buyer', 'officer', 'admin'])): ?>
            <a href="buyer-add-price.php">Post Price Offer</a>
            <a href="outside-markets.php">View Buyer Offers</a>
        <?php endif; ?>

        <a href="market-prices.php" class="active">Current Market Prices</a>
        <a href="contracts.php">Browse Contracts</a>
        <a href="trending.php">Trending Crops</a>
        <a href="logout.php" style="color:#c62828;">Logout</a>
    </div>

    <div class="main-content">
        <h1>Market Prices</h1>
        <table>
            <thead>
                <tr>
                    <th>Crop</th>
                    <th>Price (MK / kg)</th>
                    <th>Location</th>
                    <th>Phone</th>
                    <th>Source</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($all_prices)): ?>
                    <tr><td colspan="5">No prices available yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($all_prices as $p): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($p['crop']) ?></strong></td>
                        <td class="price">MK <?= number_format($p['price_per_kg'], 0) ?></td>
                        <td><?= htmlspecialchars($p['location'] ?? '-') ?></td>
                        <td><?= !empty($p['contact']) ? htmlspecialchars($p['contact']) : '-' ?></td>
                        <td class="<?= $p['source'] === 'Government' ? 'source-gov' : 'source-buyer' ?>">
                            <?= htmlspecialchars($p['source']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<h1>Market Prices</h1>
<table>
    <thead>
        <tr>
            <th>Crop</th>
            <th>Price (MK / kg)</th>
            <th>Location</th>
            <th>Phone</th>
            <th>Source</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($all_prices as $p): ?>
        <tr>
            <td><strong><?= htmlspecialchars($p['crop']) ?></strong></td>
            <td class="price">MK <?= number_format($p['price_per_kg'], 0) ?></td>
            <td><?= htmlspecialchars($p['location'] ?? '-') ?></td>
            <td><?= !empty($p['contact']) ? htmlspecialchars($p['contact']) : '-' ?></td>
            <td class="<?= $p['source'] === 'Government' ? 'source-gov' : 'source-buyer' ?>">
                <?= htmlspecialchars($p['source']) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>