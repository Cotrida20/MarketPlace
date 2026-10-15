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
            contact TEXT
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

    $count = $db->query("SELECT COUNT(*) FROM market_prices")->fetchColumn();
    if ($count == 0) {
        $db->exec("
            INSERT INTO market_prices (crop, price_per_kg) VALUES
            ('Maize', 450),
            ('Tobacco', 2800),
            ('Beans', 1200),
            ('Groundnuts', 950),
            ('Rice', 780),
            ('Soybean', 1100),
            ('Tomato', 600),
            ('Onion', 550);

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