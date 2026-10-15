<?php
require 'config.php';
$page_title = "Home";
require 'includes/header.php';

$search = trim($_GET['q'] ?? '');
$search_results = [];

if ($search !== '') {
    $stmt = $db->prepare("SELECT crop, price_per_kg, location, '' AS contact, 'Government' AS source 
                          FROM market_prices 
                          WHERE crop LIKE ? 
                          ORDER BY crop");
    $stmt->execute(['%' . $search . '%']);
    $gov = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $db->prepare("SELECT crop, ROUND(amount_mk / quantity, 0) AS price_per_kg, location, contact, 'Buyer Offer' AS source 
                           FROM outside_markets 
                           WHERE crop LIKE ? 
                           ORDER BY crop");
    $stmt2->execute(['%' . $search . '%']);
    $buyer = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $search_results = array_merge($gov, $buyer);
}
?>

<style>
.hero {
    background: linear-gradient(135deg, #2e7d32, #66bb6a);
    color: white;
    text-align: center;
    padding: 50px 20px;
    border-radius: 16px;
    margin: 25px 0 30px;
}
.hero h1 { margin: 0 0 12px; font-size: 2.1rem; }
.hero p { margin: 0 0 22px; font-size: 1.05rem; opacity: 0.95; }
.hero-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}
.hero-buttons .btn {
    padding: 12px 26px;
    font-size: 1rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
}
.btn-light {
    background: #e8f5e9;
    color: #1b5e20;
    border: none;
}
.btn-outline-light {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.search-box {
    background: white;
    border-radius: 14px;
    padding: 22px 24px;
    box-shadow: 0 4px 18px rgba(46, 125, 50, 0.10);
    margin-bottom: 35px;
    border-top: 4px solid #8bc34a;
}
.search-box h2 {
    margin: 0 0 12px;
    color: #1b5e20;
    font-size: 1.2rem;
}
.search-form {
    display: flex;
    gap: 10px;
}
.search-form input {
    flex: 1;
    padding: 12px 14px;
    border: 1.5px solid #a5d6a7;
    border-radius: 8px;
    font-size: 0.95rem;
}
.search-form button {
    padding: 12px 22px;
    background: #2e7d32;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}
.search-form button:hover { background: #1b5e20; }

.search-results {
    margin-top: 18px;
}
.search-results table {
    width: 100%;
}

.quick-access { margin-bottom: 50px; }
.quick-access h2 { color: #1b5e20; margin-bottom: 25px; }
.qa-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 22px;
}
.qa-card {
    background: white;
    border-radius: 14px;
    padding: 26px 22px;
    box-shadow: 0 4px 18px rgba(46, 125, 50, 0.10);
    border-left: 5px solid #8bc34a;
    display: flex;
    flex-direction: column;
}
.qa-card h3 { margin: 0 0 10px; color: #1b5e20; font-size: 1.2rem; }
.qa-card p { flex: 1; color: #555; margin: 0 0 18px; line-height: 1.5; }
.qa-card .btn {
    align-self: flex-start;
    padding: 10px 20px;
    font-size: 0.95rem;
    border-radius: 7px;
    font-weight: 600;
    text-decoration: none;
    background: #2e7d32;
    color: white;
    border: none;
}
.qa-card .btn:hover { background: #1b5e20; }
</style>

<div class="hero">
    <h1>Agricultural Market Price Tracker</h1>
    <p>Accurate, timely market prices for smallholder farmers in Malawi — especially Lunzu, Blantyre</p>
    <div class="hero-buttons">
        <a href="market-prices.php" class="btn btn-light">View Current Prices</a>
        <a href="contracts.php" class="btn btn-outline-light">See Contract Farming</a>
    </div>
</div>

<div class="search-box">
    <h2>Search Crop Prices</h2>
    <form class="search-form" method="GET" action="index.php">
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" 
               placeholder="Type crop name (Maize, Tobacco, Beans...)" required>
        <button type="submit">Search</button>
    </form>

    <?php if ($search !== ''): ?>
    <div class="search-results">
        <?php if (empty($search_results)): ?>
            <p>No prices found for "<?= htmlspecialchars($search) ?>".</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Crop</th>
                        <th>Price (MK / kg)</th>
                        <th>Location</th>
                        <th>Phone Number</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($search_results as $r): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($r['crop']) ?></strong></td>
                        <td class="price">MK <?= number_format($r['price_per_kg'], 0) ?></td>
                        <td><?= htmlspecialchars($r['location'] ?? '-') ?></td>
                        <td><?= !empty($r['contact']) ? htmlspecialchars($r['contact']) : '-' ?></td>
                        <td><?= htmlspecialchars($r['source']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<section class="quick-access">
    <h2>Quick Access</h2>
    <div class="qa-grid">
        <div class="qa-card">
            <h3>Market Prices</h3>
            <p>Current market prices including official government prices for major crops in Malawi.</p>
            <a href="market-prices.php" class="btn">View Prices</a>
        </div>
        <div class="qa-card">
            <h3>Contract Farming</h3>
            <p>Companies offering contracts. Apply and secure better prices for your produce.</p>
            <a href="contracts.php" class="btn">Browse Contracts</a>
        </div>
        <div class="qa-card">
            <h3>Outside Markets</h3>
            <p>Buyers ready to buy produce you already have harvested.</p>
            <a href="outside-markets.php" class="btn">See Buyers</a>
        </div>
        <div class="qa-card">
            <h3>Trending Crops</h3>
            <p>See which crops currently have high demand to help you plan better.</p>
            <a href="trending.php" class="btn">View Trends</a>
        </div>
    </div>
</section>

<?php require 'includes/footer.php'; ?>