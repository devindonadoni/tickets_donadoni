<?php
session_start();

$_SESSION = [];

// Distruggi la sessione
session_destroy();

// Reindirizza alla pagina di login o homepage
header("Location: index.php");
exit;

?>