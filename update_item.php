<?php

$loginData = json_decode(file_get_contents("login.json"), true);
$servername = $loginData["host"];
$username = $loginData["user"];
$password = $loginData["password"];
$dbname = $loginData["database"];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli($servername, $username, $password, $dbname);

$stmt = $mysqli->prepare("Update transactions set item = ?, cost = ?, category = ?, payment_method =?, date = ?, notes = ? WHERE id = ? ");
$stmt->bind_param('sdssssd', $item, $cost, $category, $payment_method, $date, $notes, $id);
if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$id = htmlspecialchars($_POST["id"]);
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



header('Location: '.'/');

?>