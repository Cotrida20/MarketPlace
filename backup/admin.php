<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['officer', 'admin'])) {
    header("Location: login.php");
    exit;
}

$message = '';
$section = $_GET['section'] ?? 'prices';

// ========== UPDATE PRICE ==========
if (isset($_POST['update_price'])) {
    $id    = (int)$_POST['id'];
    $price = (float)$_POST['price'];

    if ($id > 0 && $price > 0) {
        $stmt = $db->prepare("UPDATE market_prices 
                              SET price_per_kg = ?, updated_at = CURRENT_TIMESTAMP 
                              WHERE id = ?");
        $stmt->execute([$price, $id]);

        // Get crop name for message
        $cropStmt = $db->prepare("SELECT crop FROM market_prices WHERE id = ?");
        $cropStmt->execute([$id]);
        $cropName = $cropStmt->fetchColumn();

        // Redirect so the page reloads with new data
        header("Location: admin.php?section=prices&ok=1&crop=" . urlencode($cropName) . "&price=" . $price);
        exit;
    }
}

// Success message after redirect
if (isset($_GET['ok']) && $_GET['ok'] == 1) {
    $crop  = htmlspecialchars($_GET['crop'] ?? 'Crop');
    $price = number_format((float)($_GET['price'] ?? 0), 0);
    $message = "$crop has been updated to MK $price. It is now visible on Current Market Prices.";
    $section = 'prices';
}

$prices    = $db->query("SELECT * FROM market_prices ORDER BY crop")->fetchAll(PDO::FETCH_ASSOC);
$contracts = $db->query("SELECT * FROM contracts ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Admin Panel";
$hide_main_nav = true;
require 'includes/header.php';
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
.main-content h2 {
    margin-top: 0;
    color: #1b5e20;
}
.alert-success {
    background: #e8f5e9;
    color: #1b5e20;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 5px solid #43a047;
    font-weight: 500;
}
</style>

<div class="page-layout">

    <div class="side-menu">
        <div class="side-header">Quick Menu</div>
        <!-- Admin Dashboard now goes to Home page -->
        <a href="index.php">Admin Dashboard</a>
        <a href="admin.php?section=prices" class="<?= $section == 'prices' ? 'active' : '' ?>">Manage Prices</a>
        <a href="admin.php?section=contracts" class="<?= $section == 'contracts' ? 'active' : '' ?>">View All Contracts</a>
        <a href="buyer-add-price.php">Post Price Offer</a>
        <a href="outside-markets.php">View Buyer Offers</a>
        <a href="market-prices.php">Current Market Prices</a>
        <a href="contracts.php">Browse Contracts</a>
        <a href="trending.php">Trending Crops</a>
        <a href="logout.php" style="color:#c62828;">Logout</a>
    </div>

    <div class="main-content">

        <?php if ($message): ?>
            <div class="alert-success"><?= $message ?></div>
        <?php endif; ?>

        <?php if ($section == 'prices'): ?>
            <h2>Manage Prices</h2>
            <table>
                <thead>
                    <tr>
                        <th>Crop</th>
                        <th>Price (MK/kg)</th>
                        <th>Location</th>
                        <th>Last Updated</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prices as $p): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($p['crop']) ?></strong></td>
                        <td class="price">MK <?= number_format($p['price_per_kg'], 0) ?></td>
                        <td><?= htmlspecialchars($p['location'] ?? 'National (Govt)') ?></td>
                        <td><?= date('d M Y', strtotime($p['updated_at'])) ?></td>
                        <td>
                            <form method="POST" style="display:flex; gap:8px; align-items:center;">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <input type="number" name="price" value="<?= $p['price_per_kg'] ?>" 
                                       style="width:90px; padding:6px;" required min="1">
                                <button type="submit" name="update_price" class="btn" style="padding:6px 12px;">Update</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($section == 'contracts'): ?>
            <h2>View All Contracts</h2>
            <table>
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Crop</th>
                        <th>Quantity</th>
                        <th>Amount (MK)</th>
                        <th>Deadline</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contracts)): ?>
                        <tr><td colspan="6">No contracts found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($contracts as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['company']) ?></td>
                            <td><?= htmlspecialchars($c['crop']) ?></td>
                            <td><?= number_format($c['quantity']) ?> kg</td>
                            <td>MK <?= number_format($c['amount_mk'], 0) ?></td>
                            <td><?= htmlspecialchars($c['deadline']) ?></td>
                            <td><?= ucfirst($c['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</div>

<?php require 'includes/footer.php'; ?>