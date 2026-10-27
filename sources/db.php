<?php
function getDb(): PDO {
    $dbDir = __DIR__ . '/databases';
    $dbFile = $dbDir . '/main_database.sq3';
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON;');
    initializeSchema($pdo);
    return $pdo;
}

function columnExists(PDO $pdo, string $table, string $column): bool {
    $stmt = $pdo->query("PRAGMA table_info('" . $table . "')");
    $columns = $stmt->fetchAll();

    foreach ($columns as $col) {
        if (($col['name'] ?? '') === $column) {
            return true;
        }
    }

    return false;
}

function initializeSchema(PDO $pdo): void {
    $sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    firstname TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    phone_number TEXT,
    birthdate TEXT,
    account_creation_date TEXT NOT NULL DEFAULT (date('now')),
    account_status INTEGER NOT NULL DEFAULT 0 CHECK (account_status IN (0, 1)),
    is_admin INTEGER NOT NULL DEFAULT 0 CHECK (is_admin IN (0, 1)),
    profile_picture TEXT
);

CREATE TABLE IF NOT EXISTS vehicles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    owner_id INTEGER NOT NULL,
    brand TEXT NOT NULL,
    model TEXT NOT NULL,
    registration_number TEXT NOT NULL UNIQUE,
    seat_count INTEGER NOT NULL CHECK (seat_count > 0),
    color TEXT,
    year INTEGER,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS trips (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    driver_id INTEGER NOT NULL,
    vehicle_id INTEGER NOT NULL,
    departure_city TEXT NOT NULL,
    destination_city TEXT NOT NULL,
    departure_date TEXT NOT NULL,
    departure_time TEXT NOT NULL,
    seats_available INTEGER NOT NULL CHECK (seats_available >= 0),
    price_per_seat REAL NOT NULL CHECK (price_per_seat >= 0),
    status TEXT NOT NULL DEFAULT 'Open' CHECK (status IN ('Open', 'Full', 'Cancelled', 'Completed')),
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    trip_id INTEGER NOT NULL,
    passenger_id INTEGER NOT NULL,
    seats_booked INTEGER NOT NULL CHECK (seats_booked > 0),
    booking_date TEXT NOT NULL DEFAULT (date('now')),
    total_price REAL NOT NULL CHECK (total_price >= 0),
    status TEXT NOT NULL DEFAULT 'Pending' CHECK (status IN ('Pending', 'Confirmed', 'Cancelled', 'Completed')),
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (passenger_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    booking_id INTEGER NOT NULL,
    amount REAL NOT NULL CHECK (amount >= 0),
    payment_date TEXT NOT NULL DEFAULT (date('now')),
    payment_method TEXT NOT NULL DEFAULT 'Card',
    payment_status TEXT NOT NULL DEFAULT 'Pending' CHECK (payment_status IN ('Pending', 'Paid', 'Refunded', 'Failed')),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    trip_id INTEGER NOT NULL,
    passenger_id INTEGER NOT NULL,
    driver_id INTEGER NOT NULL,
    rating INTEGER NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (passenger_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE
);
SQL;

    $pdo->exec($sql);

    if (!columnExists($pdo, 'users', 'is_admin')) {
        $pdo->exec('ALTER TABLE users ADD COLUMN is_admin INTEGER NOT NULL DEFAULT 0');
    }
}
