<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $loginData = json_decode(file_get_contents("login.json"), true);
    $conn = new mysqli($loginData["host"], $loginData["user"], $loginData["password"], $loginData["database"]);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'item_') === 0) {
            $id = str_replace('item_', '', $key);
            $item = $conn->real_escape_string($value);
            $cost = $conn->real_escape_string($_POST["cost_$id"]);
            $date = $conn->real_escape_string($_POST["date_$id"]);
            $category = $conn->real_escape_string($_POST["category_$id"]);
            $payment_method = $conn->real_escape_string($_POST["payment_method_$id"]);
            $notes = $conn->real_escape_string($_POST["notes_$id"]);

            $sql = "UPDATE transactions SET 
                        item='$item', 
                        cost='$cost', 
                        date='$date', 
                        category='$category', 
                        payment_method='$payment_method', 
                        notes='$notes' 
                    WHERE id='$id'";
            $conn->query($sql);
        }
    }
    $conn->close();
    header("Location: transactions_page.php"); // Redirect back to the transactions page
}
?>
