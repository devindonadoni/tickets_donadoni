<?php
session_start();
header("Content-Type: application/json");
require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Verifica che l'utente sia autenticato
if (!isset($_SESSION['idUtente'])) {
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato.']);
    exit;
}

$idUtente = $_SESSION['idUtente']; // ID utente dalla sessione

// Query per ottenere le prenotazioni dal carrello
$sql = "
    SELECT c.idCarrello, c.idPrenotazione 
    FROM tcarrello c 
    WHERE c.idUtente = :idUtente AND c.pagata = 0 AND c.disponibile = 1
";
$stmt = $conn->prepare($sql);
$stmt->execute(['idUtente' => $idUtente]);
$prenotazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$prenotazioni) {
    echo json_encode(['success' => false, 'message' => 'Nessuna prenotazione trovata nel carrello.']);
    exit;
}

foreach ($prenotazioni as $carrello) {
    $idCarrello = $carrello['idCarrello'];
    $idPrenotazione = $carrello['idPrenotazione'];

    // Recuperare il prezzo dalla tabella tprenotazione
    $sql = "
        SELECT prezzo 
        FROM tprenotazione 
        WHERE idPrenotazione = :idPrenotazione
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['idPrenotazione' => $idPrenotazione]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Errore nel recupero del prezzo della prenotazione.']);
        exit;
    }

    $importo = $result['prezzo']; // Prezzo della prenotazione
    $commissioni = $importo * 0.1; // 10% di commissioni
    $importoFinale = $importo + $commissioni; // Calcolo del totale per singola prenotazione

    // Inserire i dati nella tabella tpagamento
    $sql = "
        INSERT INTO tpagamento (idPrenotazione, idCarrello, importoFinale, dataOraPagamento) 
        VALUES (:idPrenotazione, :idCarrello, :importoFinale, NOW())
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'idPrenotazione' => $idPrenotazione,
        'idCarrello' => $idCarrello,
        'importoFinale' => $importoFinale
    ]);

    // Aggiornare i campi pagata e disponibile in tcarrello
    $sql = "
        UPDATE tcarrello 
        SET pagata = 1, disponibile = 0 
        WHERE idCarrello = :idCarrello
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['idCarrello' => $idCarrello]);

    // Aggiornare il campo statoPrenotazione in tprenotazione
    $sql = "
        UPDATE tprenotazione 
        SET statoPrenotazione = 'confermata' 
        WHERE idPrenotazione = :idPrenotazione
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['idPrenotazione' => $idPrenotazione]);
}

echo json_encode([
    'success' => true,
    'message' => 'Le prenotazioni sono state confermate e aggiornate con successo.'
]);
?>
