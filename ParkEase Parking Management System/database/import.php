<?php 
// =============================================
// Setup Script - Run once to configure everything
// Imports the database to port 3307 (MariaDB)
// =============================================

echo "=== ParkEase Setup Script (Port 3307) ===\n\n";

// Connect to MariaDB on 127.0.0.1:3307
$conn = @new mysqli('127.0.0.1', 'root', '', '', 3307);

if ($conn->connect_error) {
    echo "ERROR: Could not connect to MariaDB on port 3307.\n";
    echo "Make sure you started MySQL in XAMPP (configured for port 3307)!\n";
    echo "Error: " . $conn->connect_error . "\n";
    exit(1);
}

echo "[OK] Connected to MariaDB on port 3307\n";

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS parking_db");
$conn->select_db("parking_db");

// Import the database SQL file
$sqlFile = __DIR__ . '/parking_db.sql';
if (!file_exists($sqlFile)) {
    echo "ERROR: parking_db.sql not found!\n";
    exit(1);
}

$sql = file_get_contents($sqlFile);

if ($conn->multi_query($sql)) {
    // Process all result sets
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    
    if ($conn->error) {
        echo "[WARNING] " . $conn->error . "\n";
    } else {
        echo "[OK] Database 'parking_db' imported successfully!\n";
    }
} else {
    echo "Import error: " . $conn->error . "\n";
}

$conn->close();

echo "\n=== Setup Complete! ===\n";
echo "You can now open: http://localhost/parking-management-system/\n";
echo "Admin login: admin / admin123\n";
?>
