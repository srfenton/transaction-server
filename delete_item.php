<?php

$loginData = json_decode(file_get_contents("login.json"), true);
$servername = $loginData["host"];
$username = $loginData["user"];
$password = $loginData["password"];
$dbname = $loginData["database"];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli($servername, $username, $password, $dbname);

$stmt = $mysqli->prepare("DELETE FROM transactions WHERE id = ?");
$stmt->bind_param('d',$id);
if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}


$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

if ($id === false) {
    die("Invalid cost value.");
}

$stmt->execute();

printf("%d row deleted.\n", $stmt->affected_rows);


header('Location: '.'/');

?>