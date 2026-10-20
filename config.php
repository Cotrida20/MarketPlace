<?php
session_start();

try {
    $db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            fullname TEXT NOT NULL,
            phone TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'farmer',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS market_prices (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            crop TEXT NOT NULL,
            price_per_kg REAL NOT NULL,
            unit TEXT DEFAULT 'kg',
            location TEXT DEFAULT 'National (Govt)',
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS market_price_history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            crop TEXT NOT NULL,
            price_per_kg REAL NOT NULL,
            location TEXT DEFAULT 'National (Govt)',
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS contracts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company TEXT NOT NULL,
            crop TEXT NOT NULL,
            quantity INTEGER NOT NULL,
            amount_mk REAL NOT NULL,
            deadline TEXT,
            description TEXT,
            status TEXT DEFAULT 'open'
        );

        CREATE TABLE IF NOT EXISTS outside_markets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company TEXT NOT NULL,
            crop TEXT NOT NULL,
            quantity INTEGER NOT NULL,
            amount_mk REAL NOT NULL,
            location TEXT,
            contact TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS trending_crops (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            crop TEXT NOT NULL,
            reason TEXT,
            demand_level TEXT
        );

        CREATE TABLE IF NOT EXISTS applications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            contract_id INTEGER,
            quantity INTEGER,
            status TEXT DEFAULT 'pending',
            applied_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    try {
        $db->exec("ALTER TABLE outside_markets ADD COLUMN created_at DATETIME DEFAULT CURRENT_TIMESTAMP");
    } catch (PDOException $e) {
        // Column already exists; ignore.
    }

    $count = $db->query("SELECT COUNT(*) FROM market_prices")->fetchColumn();
    if ($count == 0) {
        $db->exec("
            INSERT INTO market_prices (crop, price_per_kg, updated_at) VALUES
            ('Maize', 450, datetime('now','-12 days')),
            ('Tobacco', 2800, datetime('now','-9 days')),
            ('Beans', 1200, datetime('now','-8 days')),
            ('Groundnuts', 950, datetime('now','-7 days')),
            ('Rice', 780, datetime('now','-6 days')),
            ('Soybean', 1100, datetime('now','-5 days')),
            ('Tomato', 600, datetime('now','-4 days')),
            ('Onion', 550, datetime('now','-3 days'));

            INSERT INTO market_price_history (crop, price_per_kg, location, updated_at) VALUES
            ('Maize', 420, 'National (Govt)', datetime('now','-12 days')),
            ('Maize', 440, 'National (Govt)', datetime('now','-9 days')),
            ('Maize', 450, 'National (Govt)', datetime('now','-3 days')),
            ('Tobacco', 2600, 'National (Govt)', datetime('now','-10 days')),
            ('Tobacco', 2700, 'National (Govt)', datetime('now','-6 days')),
            ('Tobacco', 2800, 'National (Govt)', datetime('now','-2 days')),
            ('Beans', 1100, 'National (Govt)', datetime('now','-9 days')),
            ('Beans', 1180, 'National (Govt)', datetime('now','-5 days')),
            ('Beans', 1200, 'National (Govt)', datetime('now','-1 day')),
            ('Groundnuts', 900, 'National (Govt)', datetime('now','-11 days')),
            ('Groundnuts', 920, 'National (Govt)', datetime('now','-6 days')),
            ('Groundnuts', 950, 'National (Govt)', datetime('now','-2 days')),
            ('Rice', 720, 'National (Govt)', datetime('now','-12 days')),
            ('Rice', 760, 'National (Govt)', datetime('now','-7 days')),
            ('Rice', 780, 'National (Govt)', datetime('now','-1 day')),
            ('Soybean', 1000, 'National (Govt)', datetime('now','-10 days')),
            ('Soybean', 1050, 'National (Govt)', datetime('now','-5 days')),
            ('Soybean', 1100, 'National (Govt)', datetime('now','-1 day')),
            ('Tomato', 560, 'National (Govt)', datetime('now','-9 days')),
            ('Tomato', 580, 'National (Govt)', datetime('now','-4 days')),
            ('Tomato', 600, 'National (Govt)', datetime('now','-1 day')),
            ('Onion', 500, 'National (Govt)', datetime('now','-10 days')),
            ('Onion', 530, 'National (Govt)', datetime('now','-5 days')),
            ('Onion', 550, 'National (Govt)', datetime('now','-2 days'));

            INSERT INTO contracts (company, crop, quantity, amount_mk, deadline, description) VALUES
            ('Illovo Sugar Malawi', 'Sugarcane', 5000, 3500000, '2026-06-30', 'Contract farming for sugarcane - transport provided'),
            ('Alliance One Tobacco', 'Tobacco', 2000, 6500000, '2026-05-15', 'Flue-cured tobacco contract'),
            ('Export Trading Group', 'Soybean', 3000, 4200000, '2026-07-01', 'Export quality soybean'),
            ('ADMARC', 'Maize', 10000, 4800000, '2026-08-20', 'Government maize purchase programme');

            INSERT INTO outside_markets (company, crop, quantity, amount_mk, location, contact) VALUES
            ('Bakhresa Grain Milling', 'Maize', 8000, 3800000, 'Blantyre', '0888123456'),
            ('Sunseed Oil', 'Groundnuts', 2500, 2600000, 'Lilongwe', '0999987654'),
            ('Rab Processors', 'Soybean', 4000, 5100000, 'Blantyre', '0881222333'),
            ('Transglobe Produce', 'Beans', 1500, 1950000, 'Lilongwe', '0992444555');

            INSERT INTO trending_crops (crop, reason, demand_level) VALUES
            ('Soybean', 'High export demand and good prices', 'Very High'),
            ('Groundnuts', 'Oil factories buying aggressively', 'High'),
            ('Maize', 'Stable government purchase prices', 'Medium'),
            ('Tomato', 'Urban market demand increasing', 'High'),
            ('Tobacco', 'Traditional cash crop with contracts', 'Medium');
        ");
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
