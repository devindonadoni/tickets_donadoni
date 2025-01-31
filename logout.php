<?php
require_once('api/config/config.php');
session_start();


$idUtente = $_SESSION['idUtente'];
$_SESSION = [];
setcookie("remember_token", "", time() - 3600, "/"); // Cancella il cookie

$stmt = mysqli_prepare($db_remoto, "UPDATE tUtente SET remember_token = NULL WHERE idUtente = '$idUtente'");
mysqli_stmt_execute($stmt);
session_destroy();

// Reindirizza alla pagina di login o homepage
header("Location: index.php");
exit;

?>