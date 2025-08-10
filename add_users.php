<?php
// Read database credentials from login.json
$login_file = __DIR__ . '/login.json';

if (!file_exists($login_file)) {
    die("Error: login.json file not found in the current directory.\n");
}

$credentials = json_decode(file_get_contents($login_file), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error: Invalid JSON in login.json file.\n");
}

// Extract credentials
$host = $credentials['host'] ?? null;
$user = $credentials['user'] ?? null;
$password = $credentials['password'] ?? null;
$database = $credentials['database'] ?? null;

// Validate credentials
if (!$host || !$user || !isset($password) || !$database) {
    die("Error: Missing required credentials in login.json.\n");
}

// Initialize connection variable
$conn = null;

try {
    // Connect to the MySQL server
    $conn = new mysqli($host, $user, $password, $database);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "\n");
    }
    
    echo "Connected to MySQL server successfully.\n";

    // Insert users into the users table
    $sql = "INSERT INTO users (id, username) VALUES (1, 'sf'), (2, 'ms')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Users added successfully.\n";
    } else {
        die("Error adding users: " . $conn->error . "\n");
    }

} catch (Exception $e) {
    die("An error occurred: " . $e->getMessage() . "\n");
} finally {
    // Close the connection if it was established
    if ($conn) {
        $conn->close();
        echo "\nUser insertion completed successfully.\n";
    }
}
?>

