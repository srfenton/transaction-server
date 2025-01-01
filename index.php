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

        .add-button {
            display: block;
            background-color: #000036; /* Match header color */
            color: white;
            text-align: center;
            font-size: 24px;
            width: 80%; /* Match table width */
            margin: 0 auto 5px auto; /* Minimal space above the table */
            padding: 5px 0; /* Narrow button height */
            border: none;
            cursor: pointer;
        }

        .add-button i {
            font-size: 24px;
            vertical-align: middle;
        }

        .add-button:hover {
            background-color: #333366; /* Slightly lighter shade on hover */
        }
        .toggle-button {
            background-color: #000036; /* Match the table header color */
            color: white; /* Text color */
            font-size: 18px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 10px auto;
            text-align: center;
    }

        .toggle-button:hover {
            background-color: #333366; /* Slightly lighter shade on hover */
    }
        .my-button {
        background-color: transparent;
        border: none;
        padding: 0;
        margin: 0;
        cursor: pointer;
    }

    .my-button i {
        color: #000; /* Change the icon color */
        font-size: 16px; /* Adjust size as needed */
    }

    .my-button:hover i {
        color: red; /* Highlight color when hovered */
    }
    @media (max-width: 768px) {
        #transactions {
            width: 100%;
        }

        .add-button, .toggle-button {
            width: 100%;
        }
    }

    </style>
</head>
<body>

<!-- Add Button -->
 <form action="/add_item.html">
<button type ="submit" class="add-button">
    <i class="glyphicon glyphicon-plus"></i>
</button>
</form>

<!-- Collapse Button -->
<button class="toggle-button" style="display: block; margin: 0 auto; width: 80%;" data-toggle="collapse" data-target="#transactions-container">
    Transactions
</button>

<br><p id="confirmDeleteButton" style="text-align: center;"></p> <br>


<!-- Collapsible Transactions Table -->
<div id="transactions-container" class="collapse">
    <table id="transactions">
        <tr>
            <th>ID</th>
            <th>Item</th>
            <th>Cost</th>
            <th>Date</th>
            <th>Category</th>
            <th>Payment Method</th>
            <th>Notes</th>
            <th>Remove</th>
        </tr>


<script>
function confirmDelete(id) {
    if (confirm("Are you sure you want to delete item ID " + id + "?")) {
        document.getElementById("confirmDeleteButton").innerHTML = "You pressed OK for " + id;
        return true;
    } else {
        document.getElementById("confirmDeleteButton").innerHTML = "You pressed Cancel for: " + id;
        return false;
    }
}
</script>

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

$stmt = $conn->prepare("SELECT id, item, cost, date, category, payment_method, notes FROM transactions ORDER BY date DESC");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // output data of each row using while loop
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["item"] . "</td>";
        echo "<td>" . '$' . $row["cost"] . "</td>";
        echo "<td>" . $row["date"] . "</td>";
        echo "<td>" . $row["category"] . "</td>";
        echo "<td>" . $row["payment_method"] . "</td>";
        echo "<td>" . $row["notes"] . "</td>";
        echo "<td>
                <form method='POST' action='delete_item.php' onsubmit='return confirmDelete(" . $row["id"] . ")'>
                    <input type='hidden' name='id' value='" . $row["id"] . "' />
                    <button type='submit' class='my-button'>
                        <i class='glyphicon glyphicon-remove'></i>
                    </button>
                </form>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No results found</td></tr>";
}
$conn->close();
?>
    </table>
</form>
</div>

</body>
</html>
