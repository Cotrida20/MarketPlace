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
        <h1>Market Prices & Gross Margin Calculator</h1>

        <?php if ($role === 'farmer'): ?>
        <div class="form-box" style="background:#f1f8e9; margin-bottom:30px; padding:20px; border-radius:8px;">
            <h3>Farmer Gross Margin Calculator</h3>
            <p style="font-size:0.9rem; color:#555;">Calculate your estimated profit per acre based on market prices and your input costs.</p>
            <form id="margin-calc" onsubmit="return false;" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px; margin-top:15px;">
                <div>
                    <label>Select Crop</label>
                    <select id="calc_crop" style="width:100%; padding:8px;" onchange="updateDefaultPrice()">
                        <?php foreach ($all_prices as $p): ?>
                            <option value="<?= $p['price_per_kg'] ?>"><?= htmlspecialchars($p['crop']) ?> (MK <?= number_format($p['price_per_kg'], 0) ?>/kg)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Expected Yield per Acre (kg)</label>
                    <input type="number" id="calc_yield" value="1000" style="width:100%; padding:8px;" min="1">
                </div>
                <div>
                    <label>Total Variable Costs per Acre (MK)</label>
                    <input type="number" id="calc_costs" value="150000" style="width:100%; padding:8px;" min="0">
                </div>
                <div style="display:flex; align-items:flex-end;">
                    <button type="button" class="btn" onclick="calculateMargin()" style="width:100%; padding:10px;">Calculate Gross Margin</button>
                </div>
            </form>
            <div id="calc_result" style="margin-top:15px; font-weight:bold; font-size:1.1rem; color:#1b5e20;"></div>
        </div>
        <script>
        function calculateMargin() {
            var price = parseFloat(document.getElementById('calc_crop').value) || 0;
            var yieldKg = parseFloat(document.getElementById('calc_yield').value) || 0;
            var costs = parseFloat(document.getElementById('calc_costs').value) || 0;
            var revenue = price * yieldKg;
            var margin = revenue - costs;
            document.getElementById('calc_result').innerHTML = 'Estimated Total Revenue: MK ' + revenue.toLocaleString() + '<br>Estimated Gross Margin (Profit): MK ' + margin.toLocaleString();
        }
        </script>
        <?php endif; ?>

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