<?php

$loginData = json_decode(file_get_contents("login.json"), true);
$servername = $loginData["host"];
$username = $loginData["user"];
$password = $loginData["password"];
$dbname = $loginData["database"];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli($dbname, $username, $password, $dbname);

$stmt = $mysqli->prepare("INSERT INTO transactions (item, cost, category, payment_method, date, notes) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('sdssss', $item, $cost, $category, $payment_method, $date, $notes);

$item = '';
$cost = '';
$category = '';
$payment_method = '';
$date = '';
$notes = '';

$stmt->execute();

printf("%d row inserted.\n", $stmt->affected_rows);


echo 'hello world'

?>