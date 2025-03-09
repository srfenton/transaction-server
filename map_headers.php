<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>map headers</title>
</head>
<body>
    
<?php

$loginData = json_decode(file_get_contents("login.json"), true);
$servername = $loginData["host"];
$username = $loginData["user"];
$password = $loginData["password"];
$dbname = $loginData["database"];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function normalize_costs($arr) {
    $positives = [];
    $negatives = [];

    foreach ($arr as $x) {
        if ($x < 0) {
            $negatives[] = $x;
        } else {
            $positives[] = $x; 
        }
        
    }
    $normalized_array = [];
    echo "percentage of negatives: ";
    echo count($negatives) / count($arr);
    if (count($negatives) / count($arr) >= .5) {
        foreach ($arr as $x) {
            if ($x < 0) {
                $normalized_array[] = $x*-1;
            } else {
                $normalized_array = 'skip this one';
            }
        }
    } else {
        foreach ($arr as $x) {
            if ($x > 0) {
                $normalized_array[] = $x;
            } else {
                $normalized_array[] = 'skip this one';
            }
        }
    }

    return $normalized_array;

}




if (isset($_POST['db_column_mapping']) && isset($_POST['transactions'])) {
    $db_column_mapping = $_POST['db_column_mapping'];
    $transactions = json_decode($_POST['transactions'], true);

    // Collect all cost values
    $costs = [];
    foreach ($transactions as $transaction) {
        if (isset($transaction[array_search('cost', $db_column_mapping)])) {
            $costs[] = $transaction[array_search('cost', $db_column_mapping)];
        }
    }

    // Normalize the cost values
    $normalized_costs = normalize_costs($costs);
    
    $cost_index = 0;
    foreach ($transactions as $transaction) {
        $mapped_transaction = [];
        foreach ($db_column_mapping as $index => $db_col) {
            if (isset($transaction[$index])) {
                $mapped_transaction[$db_col] = $transaction[$index];
            }
        }

        $user = 1;
        $date_string = $mapped_transaction["date"];
        $date = DateTime::createFromFormat('m/d/Y', $date_string);
        $formatted_date = $date->format('Y-m-d');
        $item = $mapped_transaction["item"];
        
        // Use normalized cost
        $cost = $normalized_costs[$cost_index++];
        if ($cost == 'skip this one') continue;

        $stmt = $conn->prepare("INSERT INTO confirmations (date, item, cost, user,shared_with) VALUES (?, ?, ?, ?, 'not shared')");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssdd", $formatted_date, $item, $cost, $user);
        $stmt->execute();
        
    }
}

header('Location: '.'/confirm_transactions.php');


?>
</table>
<a href = '/'>home</a>
</body>
</html>
