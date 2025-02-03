<?php
session_start();
require_once('api/config/config.php');

if (!isset($_SESSION['idUtente']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    // Cerca il token nel database
    $query = "SELECT idUtente, remember_token, email FROM tutente WHERE remember_token IS NOT NULL";
    $stmt = mysqli_prepare($db_remoto, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($token, $row['remember_token'])) {
            $_SESSION['idUtente'] = $row['idUtente'];
            $_SESSION['user'] = $row['email'];
            break;
        }
    }}
?>
