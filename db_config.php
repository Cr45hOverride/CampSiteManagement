<?php
// db_config.php
// Database configuration and connection setup using PDO

$host = 'localhost'; // Database host
$dbname = 'sia_campsite'; // Database name
$username = 'root'; // Database username (default for XAMPP is 'root')
$password = ''; // Database password (default for XAMPP is empty '')

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set the PDO error mode to exception for robust error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array for easier data retrieval
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Handle connection error safely without exposing sensitive data in production
    // For development, it's okay to see the error, but in production we'd log this.
    die("Database Connection failed: " . $e->getMessage());
}
?>
