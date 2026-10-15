<?php
require 'config.php';

$page_title = "Contracts";
$hide_main_nav = isset($_SESSION['user_id']);
require 'includes/header.php';

$contracts = $db->query("SELECT * FROM contracts ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
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
.btn-apply {
    background: #2e7d32;
    color: white;
    padding: 7px 14px;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
}
.btn-apply:hover {
    background: #1b5e20;
    color: white;
}
</style>

<?php if (isset($_SESSION['user_id'])): ?>
<div class="page-layout">

    <!-- Same Quick Menu -->
    <div class="side-menu">
        <div class="side-header">Quick Menu</div>
        <?php if (in_array($_SESSION['role'] ?? '', ['officer', 'admin'])): ?>
            <a href="index.php">Admin Dashboard</a>
            <a href="admin.php?section=prices">Manage Prices</a>
            <a href="admin.php?section=contracts">View All Contracts</a>
        <?php endif; ?>
        <?php if (in_array($_SESSION['role'] ?? '', ['buyer', 'officer', 'admin'])): ?>
            <a href="buyer-add-price.php">Post Price Offer</a>
            <a href="outside-markets.php">View Buyer Offers</a>
        <?php endif; ?>
        <a href="market-prices.php">Current Market Prices</a>
        <a href="contracts.php" class="active">Browse Contracts</a>
        <a href="trending.php">Trending Crops</a>
        <a href="logout.php" style="color:#c62828;">Logout</a>
    </div>

    <div class="main-content">
        <h1>Contract Farming</h1>
        <table>
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Crop</th>
                    <th>Quantity</th>
                    <th>Amount (MK)</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contracts)): ?>
                    <tr><td colspan="7">No contracts available.</td></tr>
                <?php else: ?>
                    <?php foreach ($contracts as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['company']) ?></td>
                        <td><?= htmlspecialchars($c['crop']) ?></td>
                        <td><?= number_format($c['quantity']) ?> kg</td>
                        <td>MK <?= number_format($c['amount_mk'], 0) ?></td>
                        <td><?= htmlspecialchars($c['deadline']) ?></td>
                        <td><?= ucfirst($c['status']) ?></td>
                        <td>
                            <?php if (strtolower($c['status']) === 'open'): ?>
                                <a href="apply-contract.php?id=<?= $c['id'] ?>" class="btn-apply">Apply</a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<!-- Not logged in -->
<h1>Contract Farming</h1>
<table>
    <thead>
        <tr>
            <th>Company</th>
            <th>Crop</th>
            <th>Quantity</th>
            <th>Amount (MK)</th>
            <th>Deadline</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($contracts as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['company']) ?></td>
            <td><?= htmlspecialchars($c['crop']) ?></td>
            <td><?= number_format($c['quantity']) ?> kg</td>
            <td>MK <?= number_format($c['amount_mk'], 0) ?></td>
            <td><?= htmlspecialchars($c['deadline']) ?></td>
            <td><?= ucfirst($c['status']) ?></td>
            <td>
                <?php if (strtolower($c['status']) === 'open'): ?>
                    <a href="login.php" class="btn-apply">Login to Apply</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require 'includes/footer.php'; ?>