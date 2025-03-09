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
    </style>
</head>
<body>

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
        echo "<td>" . 
        "<select id='category' name='category' size='1' required>
            <option value='none' selected disabled hidde    n> category </option>
            <option value='Cash Withdrawal'>Cash Withdrawal</option>
            <option value='Dining'>Dining</option>
            <option value='Education'>Utilities</option>
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
        "<select id='payment_method' name='payment_method' size='1' required>
        <option value='none' selected disabled hidden > payment method </option>
        <option value='Cash'>Cash</option>
        <option value='Credit'>Credit</option>
        <option value='Shared Expenses'>Shared Expenses</option>
    </select>" . "</td>";
        echo "<td>" . "<textarea id = 'notes' ></textarea>" .  "</td>";
        echo "<td>"  . 
            "<select id='exclude_selection'>
            <option value='yes'>yes</option>
            <option selected = 'selected' value='database_selection'>no</option>
            </select>"
        .  "</td>";
        echo "<td><select id='shared_selection'>";
        echo "<option value='current_share_selection' selected='selected'>" . $row["shared_with"] . "</option>";
        foreach ($usernames as $username) {
            echo "<option value='" . htmlspecialchars($username) . "'>" . htmlspecialchars($username) . "</option>";
        }
        echo "</select></td>";
        echo "</tr>";
    }
} else {
    header("Location: /");
}
$conn->close();
?>  