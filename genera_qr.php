<?php
require "vendor/autoload.php";
require_once 'api/config/database.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

// Recupera l'ID della prenotazione
$idPrenotazione = $_GET["idPrenotazione"] ?? null;

if (!$idPrenotazione) {
    die("ID prenotazione mancante.");
}

// Segreto per l'hash
$secretKey = "GEFELIXKING";

// Crea un hash sicuro basato sull'ID della prenotazione e il segreto
$hash = hash_hmac('sha256', $idPrenotazione, $secretKey);

// Connessione al database
$conn = new mysqli("localhost", "root", "", "tickets_donadoni");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Salva l'hash nel database
$sql = "UPDATE tprenotazione SET qr_hash = ? WHERE idPrenotazione = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $hash, $idPrenotazione);
$stmt->execute();
$stmt->close();

// Genera il link di verifica con l'hash
$urlVerifica = "localhost/qr/verifica_qr.php?hash=" . urlencode($hash);

// Crea il QR Code
$qrCode = new QrCode($urlVerifica);
$qrCode->setSize(600);
$qrCode->setMargin(5);
$qrCode->setForegroundColor(new Color(0, 0, 0)); // Nero
$qrCode->setBackgroundColor(new Color(255, 255, 255)); // Bianco
$qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh());

$writer = new PngWriter();
$result = $writer->write($qrCode);

// Imposta l'header per mostrare l'immagine PNG
header("Content-Type: " . $result->getMimeType());
echo $result->getString();

$qrPath = "qrcodes/qr_$idPrenotazione.png";

// Salva il QR Code su file
$result->saveToFile($qrPath);

$sql = "UPDATE tprenotazione SET qr_image_path = ? WHERE idPrenotazione = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $qrPath, $idPrenotazione);
$stmt->execute();
$stmt->close();

$conn->close();
?>
