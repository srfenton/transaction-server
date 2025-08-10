<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>
        #transactions {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            margin-left: auto;
            margin-right: auto;
            margin: 20px auto;
            width: 80%; /* Centered table with adjustable width */
        }

        #transactions td, #transactions th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #transactions tr:nth-child(even) {background-color: #f2f2f2;}

        #transactions tr:hover {background-color: #ddd;}

        #transactions th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #000036;
            color: white;
        }

        #submitBtn {
            margin: 20px auto;
            display: block;
            padding: 10px 20px;
            background-color: #000036;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        #submitBtn:hover {
            background-color: #000050;
        }
    </style>
</head>
<body>

<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_transactions'])) {
    $loginData = json_decode(file_get_contents("login.json"), true);
    $servername = $loginData["host"];
    $username = $loginData["user"];
    $password = $loginData["password"];
    $dbname = $loginData["database"];

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $insertCount = 0;
    
    foreach ($_POST['transactions'] as $id => $transaction) {
        // Skip if exclude is 'yes', but still delete from confirmations
        if ($transaction['exclude'] === 'yes') {
            // Delete excluded transaction from confirmations table
            $deleteStmt = $conn->prepare("DELETE FROM confirmations WHERE id = ?");
            $deleteStmt->bind_param("i", $id);
            $deleteStmt->execute();
            continue;
        }

        $cost = floatval($transaction['cost']);
        
        // If payment method is 'Shared Expenses', split cost in half
        if ($transaction['payment_method'] === 'Shared Expenses') {
            $cost = $cost / 2;
        }

        // Insert into transactions table (hardcoded to user ID 1)
        $user_id = 1;
        $stmt = $conn->prepare("INSERT INTO transactions (item, cost, date, category, payment_method, notes, user) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsssss", 
            $transaction['item'],
            $cost,
            $transaction['date'],
            $transaction['category'],
            $transaction['payment_method'],
            $transaction['notes'],
            $user_id
        );
        
        if ($stmt->execute()) {
            $insertCount++;
            // Delete from confirmations table after successful insert
            $deleteStmt = $conn->prepare("DELETE FROM confirmations WHERE id = ?");
            $deleteStmt->bind_param("i", $id);
            $deleteStmt->execute();
        }
    }
    
    $conn->close();
    
    // Check if there are any remaining transactions in confirmations table
    $conn2 = new mysqli($servername, $username, $password, $dbname);
    $checkStmt = $conn2->prepare("SELECT COUNT(*) as remaining FROM confirmations");
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $remainingCount = $checkResult->fetch_assoc()['remaining'];
    $conn2->close();
    
    if ($remainingCount == 0) {
        // No more transactions to confirm, redirect to home
        header("Location: /");
        exit();
    }
    
    echo "<div class='alert alert-success' style='width: 80%; margin: 20px auto;'>Successfully inserted $insertCount transactions!</div>";
}
?>

<form method="POST" action="">
<!-- Transactions Table -->
<table id="transactions">
    <tr>
        <th>ID</th>
        <th>Item</th>
        <th>Cost</th>
        <th>Date</th>
        <th>Category</th>
        <th>Payment Method</th>
        <th>Notes</th>
        <th>Exclude?</th>
        <th>Shared with?</th>
    </tr>

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

$stmt = $conn->prepare("SELECT id, item, cost, date, shared_with FROM confirmations ORDER BY date DESC");
$stmt->execute();
$result = $stmt->get_result();

// Fetch usernames
$stmt2 = $conn->prepare("SELECT username FROM users ORDER BY username DESC");
$stmt2->execute();
$result2 = $stmt2->get_result();

// Store usernames in an array
$usernames = [];
while ($row = $result2->fetch_assoc()) {
    $usernames[] = $row['username'];
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["item"] . "</td>";
        echo "<td>" . '$' . $row["cost"] . "</td>";
        echo "<td>" . $row["date"] . "</td>";
        
        // Hidden fields for item, cost, and date
        echo "<input type='hidden' name='transactions[" . $row["id"] . "][item]' value='" . htmlspecialchars($row["item"]) . "'>";
        echo "<input type='hidden' name='transactions[" . $row["id"] . "][cost]' value='" . $row["cost"] . "'>";
        echo "<input type='hidden' name='transactions[" . $row["id"] . "][date]' value='" . $row["date"] . "'>";
        
        echo "<td>" . 
        "<select name='transactions[" . $row["id"] . "][category]' required>
            <option value='' selected disabled hidden>category</option>
            <option value='Cash Withdrawal'>Cash Withdrawal</option>
            <option value='Dining'>Dining</option>
            <option value='Education'>Education</option>
            <option value='Fees'>Fees</option>
            <option value='Fun'>Fun</option>
            <option value='Gifts'>Gifts</option>
            <option value='Groceries'>Groceries</option>
            <option value='Health & Beauty'>Health & Beauty</option>
            <option value='Household'>Household</option>
            <option value='Housing'>Housing</option>
            <option value='Tax'>Tax</option>
            <option value='Transportation'>Transportation</option>
            <option value='Travel'>Travel</option>
            <option value='Utilities'>Utilities</option>
        </select>" . "</td>";

        echo "<td>" . 
        "<select name='transactions[" . $row["id"] . "][payment_method]' required>
        <option value='' selected disabled hidden>payment method</option>
        <option value='Cash'>Cash</option>
        <option value='Credit'>Credit</option>
        <option value='Shared Expenses'>Shared Expenses</option>
    </select>" . "</td>";
        
        echo "<td>" . "<textarea name='transactions[" . $row["id"] . "][notes]'></textarea>" .  "</td>";
        
        echo "<td>"  . 
            "<select name='transactions[" . $row["id"] . "][exclude]'>
            <option value='no' selected>no</option>
            <option value='yes'>yes</option>
            </select>"
        .  "</td>";
        
        echo "<td><select name='transactions[" . $row["id"] . "][shared_with]'>";
        
        // Default to current shared_with value or empty if not set
        $currentSharedWith = $row["shared_with"];
        
        echo "<option value=''" . (empty($currentSharedWith) ? " selected" : "") . ">not shared</option>";
        
        foreach ($usernames as $username) {
            $selected = ($currentSharedWith === $username) ? " selected" : "";
            echo "<option value='" . htmlspecialchars($username) . "'$selected>" . htmlspecialchars($username) . "</option>";
        }
        echo "</select></td>";
        echo "</tr>";
    }
} else {
    header("Location: /");
}
$conn->close();
?>  
</table>

<button type="submit" name="submit_transactions" id="submitBtn">Submit All Transactions</button>
</form>

</body>
</html>
