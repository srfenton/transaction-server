<?php

$loginData = json_decode(file_get_contents("login.json"), true);
$servername = $loginData["host"];
$username = $loginData["user"];
$password = $loginData["password"];
$dbname = $loginData["database"];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli($servername, $username, $password, $dbname);

$stmt = $mysqli->prepare("INSERT INTO transactions (item, cost, category, payment_method, date, notes) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('sdssss', $item, $cost, $category, $payment_method, $date, $notes);
if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}


$item = htmlspecialchars($_POST["item"]);
$cost = filter_var($_POST["cost"], FILTER_VALIDATE_FLOAT);
$category = htmlspecialchars($_POST["category"]);
$payment_method = htmlspecialchars($_POST["payment_method"]);
$date = htmlspecialchars($_POST["date"]);
$notes = htmlspecialchars($_POST["notes"]);

if ($cost === false) {
    die("Invalid cost value.");
}

$stmt->execute();

printf("%d row inserted.\n", $stmt->affected_rows);


header('Location: '.'/');

?>