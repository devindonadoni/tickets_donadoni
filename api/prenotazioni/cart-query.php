<?php
session_start();
header("Content-Type: application/json");
require_once '../config/database.php';

$db = new Database();
$conn = $db->getConnection();

// Inizializzazione variabili
$idUtente = $_SESSION['idUtente']; // ID utente dalla sessione

// Query per ottenere i dati dal carrello, includendo la dataAggiunta
$sql = "
    SELECT c.idPrenotazione, c.idEvento, c.dataAggiunta
    FROM tcarrello c
    WHERE c.idUtente = :idUtente AND c.pagata = 0 AND disponibile = 1
";
$stmt = $conn->prepare($sql);
$stmt->execute(['idUtente' => $idUtente]);
$prenotazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Array per raccogliere i dati finali
$risultato = [];

foreach ($prenotazioni as $carrello) {
    $idPrenotazione = $carrello['idPrenotazione'];
    $idEvento = $carrello['idEvento'];
    $dataAggiunta = $carrello['dataAggiunta'];

    // Recupera il prezzo e idSettore dalla tabella tprenotazione
    $sql = "
        SELECT pr.prezzo, pr.idSettore, pr.idPosto
        FROM tprenotazione pr
        WHERE pr.idPrenotazione = :idPrenotazione
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['idPrenotazione' => $idPrenotazione]);
    $prenotazione = $stmt->fetch(PDO::FETCH_ASSOC);

    // Recupera il nomeSettore
    $sql = "SELECT nomeSettore FROM tsettore WHERE idSettore = :idSettore";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['idSettore' => $prenotazione['idSettore']]);
    $settore = $stmt->fetch(PDO::FETCH_ASSOC);

    // Recupera nomeEvento, pathFotoLocandina, dataOraEvento
    $sql = "SELECT nomeEvento, pathFotoLocandina, dataOraEvento FROM tevento WHERE idEvento = :idEvento";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['idEvento' => $idEvento]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    // Recupera citta e locazione
    $sql = "SELECT citta, locazione FROM tluogo WHERE idLuogo = (SELECT idLuogo FROM tevento WHERE idEvento = :idEvento)";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['idEvento' => $idEvento]);
    $luogo = $stmt->fetch(PDO::FETCH_ASSOC);

    // Recupera numeroPosto, solo se il posto è numerato (idPosto non è null)
    $numeroPosto = null;
    if ($prenotazione['idPosto'] !== null) {
        $sql = "SELECT numeroPosto FROM tposto WHERE idPosto = :idPosto";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['idPosto' => $prenotazione['idPosto']]);
        $posto = $stmt->fetch(PDO::FETCH_ASSOC);
        $numeroPosto = $posto['numeroPosto'];
    }

    // Aggiungi i dati al risultato, inclusa idPrenotazione e dataAggiunta
    $risultato[] = [
        'idPrenotazione' => $idPrenotazione, // Aggiunto idPrenotazione
        'nomeEvento' => $evento['nomeEvento'],
        'citta' => $luogo['citta'],
        'nomeSettore' => $settore['nomeSettore'],
        'prezzo' => $prenotazione['prezzo'],
        'posto' => $numeroPosto,  // Sarà null se il posto non è numerato
        'pathFotoLocandina' => $evento['pathFotoLocandina'],
        'dataOraEvento' => $evento['dataOraEvento'],
        'locazione' => $luogo['locazione'],
        'dataAggiunta' => $dataAggiunta
    ];
}

// Ritorna i risultati in formato JSON
echo json_encode($risultato);
?>
