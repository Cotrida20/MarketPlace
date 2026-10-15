<?php
require 'config.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['officer', 'admin'])) {
    header("Location: login.php");
    exit;
}

$message = '';
$section = $_GET['section'] ?? 'prices';

if (isset($_POST['update_price'])) {
    $id = (int)$_POST['id'];
    $price = (float)$_POST['price'];

    if ($id > 0 && $price > 0) {
        $stmt = $db->prepare("UPDATE market_prices 
                            SET price_per_kg = ?, updated_at = CURRENT_TIMESTAMP 
                            WHERE id = ?");
        $stmt->execute([$price, $id]);

        $cropStmt = $db->prepare("SELECT crop FROM market_prices WHERE id = ?");
        $cropStmt->execute([$id]);
        $cropName = $cropStmt->fetchColumn();

        header("Location: admin.php?section=prices&ok=1&crop=" . urlencode($cropName) . "&price=" . $price);
        exit;
    }
}

if (isset($_POST['add_admin'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = preg_replace('/\D/', '', $_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($fullname === '' || strlen($phone) !== 10 || $password === '') {
        $message = "Error: All fields are required and phone must be 10 digits.";
        $section = 'admins';
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $db->prepare("INSERT INTO users (fullname, phone, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$fullname, $phone, $hashed]);
            header("Location: admin.php?section=admins&added=1");
            exit;
        } catch (PDOException $e) {
            $message = "Error: Phone number already registered.";
            $section = 'admins';
        }
    }
}

if (isset($_GET['ok']) && $_GET['ok'] == 1) {
    $crop = htmlspecialchars($_GET['crop'] ?? 'Crop');
    $price = number_format((float)($_GET['price'] ?? 0), 0);
    $message = "$crop has been updated to MK $price. It is now visible on Current Market Prices.";
    $section = 'prices';
}

if (isset($_GET['added']) && $_GET['added'] == 1) {
    $message = "New admin account has been created successfully.";
    $section = 'admins';
}

$prices = $db->query("SELECT * FROM market_prices ORDER BY crop")->fetchAll(PDO::FETCH_ASSOC);
$contracts = $db->query("SELECT * FROM contracts ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$admin_users = $db->query("SELECT id, fullname, phone, role, created_at FROM users WHERE role IN ('admin', 'officer') ORDER BY fullname")->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Admin Panel";
$hide_main_nav = true;
require 'includes/header.php';
?>

<div class="page-layout">

    <div class="side-menu">
        <div class="side-header">Quick Menu</div>
        <a href="index.php">Admin Dashboard</a>
        <a href="admin.php?section=prices" class="<?= $section == 'prices' ? 'active' : '' ?>">Manage Prices</a>
        <a href="admin.php?section=contracts" class="<?= $section == 'contracts' ? 'active' : '' ?>">View All Contracts</a>
        <a href="admin.php?section=admins" class="<?= $section == 'admins' ? 'active' : '' ?>">Manage Admins</a>
        <a href="buyer-add-price.php">Post Price Offer</a>
        <a href="outside-markets.php">View Buyer Offers</a>
        <a href="market-prices.php">Current Market Prices</a>
        <a href="contracts.php">Browse Contracts</a>
        <a href="trending.php">Trending Crops</a>
        <a href="logout.php" style="color:#c62828;">Logout</a>
    </div>

    <div class="main-content">

        <?php if ($message): ?>
            <div class="alert-success"><?= htmlspecialchars($message) ?></div>
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

        <?php elseif ($section == 'admins'): ?>
            <h2>Manage Admins & Officers</h2>
            
            <div class="form-box" style="margin-bottom:30px; background:#fff; padding:20px; border-radius:8px;">
                <h3>Add New Admin / Officer</h3>
                <form method="POST">
                    <label>Full Name</label>
                    <input type="text" name="fullname" required style="width:100%; padding:8px; margin-bottom:10px;">

                    <label>Phone Number (10 digits)</label>
                    <input type="text" name="phone" required maxlength="10" pattern="[0-9]{10}" placeholder="0888123456" style="width:100%; padding:8px; margin-bottom:10px;">

                    <label>Password</label>
                    <input type="password" name="password" required style="width:100%; padding:8px; margin-bottom:15px;">

                    <button type="submit" name="add_admin" class="btn">Create Admin Account</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admin_users as $adm): ?>
                    <tr>
                        <td><?= htmlspecialchars($adm['fullname']) ?></td>
                        <td><?= htmlspecialchars($adm['phone']) ?></td>
                        <td><?= ucfirst(htmlspecialchars($adm['role'])) ?></td>
                        <td><?= htmlspecialchars($adm['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    </div>
</div>

<?php require 'includes/footer.php'; ?>