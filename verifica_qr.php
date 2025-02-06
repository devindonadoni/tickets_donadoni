<?php
require_once 'api/config/database.php';

// Recupera l'hash dalla richiesta GET
$hash = $_GET['hash'] ?? null;

if (!$hash) {
    die("Accesso non autorizzato.");
}

// Segreto per l'hash
$secretKey = "LaTuaChiaveSegreta";  // Cambia questa chiave con una più sicura

// Connessione al database
$conn = new mysqli("localhost", "root", "", "tickets_donadoni");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Verifica se l'hash corrisponde a una prenotazione
$sql = "SELECT idPrenotazione, usato FROM tPrenotazione WHERE qr_hash = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $hash);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Verifica se il biglietto è già stato usato
    if ($row['usato']) {
        echo "<h1>Biglietto già usato!</h1>";
    } else {
        // Segna il biglietto come usato
        $sqlUpdate = "UPDATE tPrenotazione SET usato = 1 WHERE qr_hash = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("s", $hash);
        $stmtUpdate->execute();
        $stmtUpdate->close();
        
        echo "<h1>Biglietto valido! Accesso consentito.</h1>";
    }
} else {
    echo "<h1>Biglietto non valido!</h1>";
}

$stmt->close();
$conn->close();
?>
