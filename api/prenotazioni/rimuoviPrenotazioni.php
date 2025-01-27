<?php
session_start();
require_once '../config/database.php';

header("Content-Type: application/json");
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leggi il corpo della richiesta
    $data = json_decode(file_get_contents("php://input"), true);

    // Debug: Verifica il contenuto ricevuto
    if (isset($data['idPrenotazione'])) {
        $idPrenotazione = $data['idPrenotazione'];
    } else {
        echo json_encode(["success" => false, "message" => "idPrenotazione mancante"]);
        exit;
    }

    try {
        // Verifica l'idPrenotazione
        if (empty($idPrenotazione) || !is_numeric($idPrenotazione)) {
            echo json_encode(["success" => false, "message" => "ID prenotazione non valido"]);
            exit;
        }

        // Cambiare il valore del campo disponibile = false nella tabella tcarrello
        $sqlCarrello = "UPDATE tcarrello SET disponibile = false WHERE idPrenotazione = :idPrenotazione";
        $stmtCarrello = $conn->prepare($sqlCarrello);
        $stmtCarrello->bindParam(':idPrenotazione', $idPrenotazione, PDO::PARAM_INT);
        $stmtCarrello->execute();

        // Recuperare l'idPosto e idSettore dalla tabella tPrenotazione
        $sqlPrenotazione = "SELECT idPosto, idSettore FROM tprenotazione WHERE idPrenotazione = :idPrenotazione";
        $stmtPrenotazione = $conn->prepare($sqlPrenotazione);
        $stmtPrenotazione->bindParam(':idPrenotazione', $idPrenotazione, PDO::PARAM_INT);
        $stmtPrenotazione->execute();
        $prenotazione = $stmtPrenotazione->fetch(PDO::FETCH_ASSOC);

        if ($prenotazione) {
            $idPosto = $prenotazione['idPosto'];
            $idSettore = $prenotazione['idSettore'];

            // Se idPosto non è null, impostare disponibile = true e rimuovere idUtente in tPosto
            if (!is_null($idPosto)) {
                $sqlPosto = "UPDATE tposto SET disponibile = true, idUtente = NULL WHERE idPosto = :idPosto";
                $stmtPosto = $conn->prepare($sqlPosto);
                $stmtPosto->bindParam(':idPosto', $idPosto, PDO::PARAM_INT);
                $stmtPosto->execute();
            }

            // Impostare statoElaborazione = 'cancellata' in tPrenotazione
            $sqlUpdatePrenotazione = "UPDATE tprenotazione SET statoPrenotazione = 'cancellata' WHERE idPrenotazione = :idPrenotazione";
            $stmtUpdatePrenotazione = $conn->prepare($sqlUpdatePrenotazione);
            $stmtUpdatePrenotazione->bindParam(':idPrenotazione', $idPrenotazione, PDO::PARAM_INT);
            $stmtUpdatePrenotazione->execute();

            // Aggiornare postiTotali in tSettore incrementandolo di 1
            $sqlSettore = "UPDATE tsettore SET postiTotali = postiTotali + 1 WHERE idSettore = :idSettore";
            $stmtSettore = $conn->prepare($sqlSettore);
            $stmtSettore->bindParam(':idSettore', $idSettore, PDO::PARAM_INT);
            $stmtSettore->execute();

            echo json_encode(["success" => true, "message" => "Prenotazione cancellata con successo"]);
        } else {
            echo json_encode(["success" => false, "message" => "Prenotazione non trovata"]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Errore durante l'elaborazione", "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Metodo non supportato"]);
}

?>
