<?php
// Avvia la sessione per accedere alla variabile di sessione
session_start();

// Verifica se l'utente è loggato e se l'idUtente è presente nella sessione
if (!isset($_SESSION['idUtente'])) {
    http_response_code(401); // Non autorizzato
    echo json_encode(["error" => "Utente non autenticato"]);
    exit;
}

// Recupera l'idUtente dalla sessione
$idUtente = $_SESSION['idUtente'];

// Includi la classe Database
require_once '../api/config/database.php'; // Sostituisci con il percorso corretto

try {
    // Crea un'istanza della classe Database e ottieni la connessione
    $database = new Database();
    $conn = $database->getConnection();

    // Query per recuperare le prenotazioni dell'utente, includendo il nomeSettore
    $query = "
        SELECT 
            p.idPrenotazione,
            p.statoPrenotazione,
            p.prezzo,
            p.idPosto,
            p.idEvento,
            e.nomeEvento,
            e.dataOraEvento,
            e.idLuogo,
            l.citta,
            l.locazione,
            po.numeroPosto,
            s.nomeSettore
        FROM tprenotazione p
        LEFT JOIN tevento e ON p.idEvento = e.idEvento
        LEFT JOIN tluogo l ON e.idLuogo = l.idLuogo
        LEFT JOIN tposto po ON p.idPosto = po.idPosto
        LEFT JOIN tsettore s ON po.idSettore = s.idSettore  -- Aggiungi il join con tsettore
        WHERE p.idUtente = :idUtente
    ";

    // Prepara e esegui la query
    $stmt = $conn->prepare($query);
    $stmt->execute(['idUtente' => $idUtente]);

    // Recupera i risultati
    $prenotazioni = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formatta i risultati
    $result = [];
    foreach ($prenotazioni as $prenotazione) {
        $result[] = [
            'idPrenotazione' => $prenotazione['idPrenotazione'],
            'statoPrenotazione' => $prenotazione['statoPrenotazione'],
            'prezzo' => $prenotazione['prezzo'],
            'numeroPosto' => $prenotazione['numeroPosto'],
            'nomeEvento' => $prenotazione['nomeEvento'],
            'dataOraEvento' => $prenotazione['dataOraEvento'],
            'citta' => $prenotazione['citta'],
            'locazione' => $prenotazione['locazione'],
            'nomeSettore' => $prenotazione['nomeSettore'] // Aggiungi il nomeSettore
        ];
    }

    // Restituisci i risultati in formato JSON
    header('Content-Type: application/json');
    echo json_encode($result);

} catch (PDOException $e) {
    // Gestione degli errori
    http_response_code(500); // Errore del server
    echo json_encode(["error" => "Errore del database: " . $e->getMessage()]);
}
?>
