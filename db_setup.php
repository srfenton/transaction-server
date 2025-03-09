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

// Connect to the MySQL server
try {
    $conn = new mysqli($host, $user, $password);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "\n");
    }
    
    echo "Connected to MySQL server successfully.\n";
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS `$database`";
    if ($conn->query($sql) === TRUE) {
        echo "Database '$database' created or already exists.\n";
    } else {
        die("Error creating database: " . $conn->error . "\n");
    }
    
    // Select the database
    $conn->select_db($database);
    echo "Using database: $database\n";
    
    // Drop the table if it exists
    $sql = "DROP TABLE IF EXISTS transactions";
    if ($conn->query($sql) === TRUE) {
        echo "Table 'transactions' dropped (if it existed).\n";
    } else {
        die("Error dropping table: " . $conn->error . "\n");
    }
    
    // Create the transactions table
    $sql = "CREATE TABLE transactions (
        id INT AUTO_INCREMENT NOT NULL,
        item VARCHAR(255) NOT NULL,
        user NOT NULL INT,
        cost DECIMAL(10,2) NOT NULL,
        category VARCHAR(255) NOT NULL,
        payment_method VARCHAR(255) NOT NULL,
        date DATE NOT NULL,
        notes TEXT,
        PRIMARY KEY (id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Table 'transactions' created successfully.\n";
    } else {
        die("Error creating table: " . $conn->error . "\n");
    }
    
    // Verify the table structure
    echo "\nTable structure for 'transactions':\n";
    $result = $conn->query("DESCRIBE transactions");
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo $row['Field'] . " - " . $row['Type'] . " - " . ($row['Null'] === "NO" ? "NOT NULL" : "NULL") . 
                 ($row['Key'] === "PRI" ? " - PRIMARY KEY" : "") . 
                 ($row['Extra'] === "auto_increment" ? " - AUTO_INCREMENT" : "") . "\n";
        }
    } else {
        echo "Error describing table: " . $conn->error . "\n";
    }
    
    // Close the connection
    $conn->close();
    echo "\nDatabase setup completed successfully.\n";
    
} catch (Exception $e) {
    die("An error occurred: " . $e->getMessage() . "\n");
}
?>

///
this needs :

CREATE TABLE confirmations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user INT NOT NULL,
    item VARCHAR(255) NOT NULL,
    cost DECIMAL(10, 2) NOT NULL,
    date DATE NOT NULL,
    shared_with VARCHAR(255)
);
///

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username varchar(255) NOT NULL
);
///