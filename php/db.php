<?php
// db.php
$host = 'localhost';
$username = 'root';
$password = '';
$db_remoto = 'tickets_donadoni';

$conn = new mysqli($host, $username, $password, $db_remoto);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
