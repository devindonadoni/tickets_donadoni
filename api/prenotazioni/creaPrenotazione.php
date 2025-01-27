<?php
session_start();
require_once '../config/database.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $idUtente = $_SESSION['idUtente'] ?? null;
    $idSettore = $data['idSettore'] ?? null;
    $quantita = $data['quantita'] ?? null;
    $idEvento = $data['idEvento'] ?? null;

    if (!$idUtente || !$idSettore || !$quantita || !$idEvento || $quantita > 4) {
        echo json_encode(["success" => false, "message" => "Dati mancanti o quantità non valida"]);
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    try {
        // Inizia transazione
        $conn->beginTransaction();

        // Verifica disponibilità settore e prezzo
        $query = "SELECT numerato, postiTotali, prezzo FROM tSettore WHERE idSettore = :idSettore";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':idSettore', $idSettore, PDO::PARAM_INT);
        $stmt->execute();
        $settore = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$settore || $settore['postiTotali'] < $quantita) {
            echo json_encode(["success" => false, "message" => "Posti insufficienti"]);
            $conn->rollBack();
            exit;
        }

        // Riduci i posti disponibili nel settore
        $queryUpdate = "UPDATE tSettore SET postiTotali = postiTotali - :quantita WHERE idSettore = :idSettore";
        $stmtUpdate = $conn->prepare($queryUpdate);
        $stmtUpdate->bindParam(':quantita', $quantita, PDO::PARAM_INT);
        $stmtUpdate->bindParam(':idSettore', $idSettore, PDO::PARAM_INT);
        $stmtUpdate->execute();

        // Esegui la prenotazione per ogni biglietto
        $prenotazioniIds = [];
        for ($i = 0; $i < $quantita; $i++) {
            // Se il settore è numerato, trova un posto disponibile
            $idPosto = null;
            if ((bool)$settore['numerato']) {
                $queryPosto = "SELECT idPosto FROM tPosto WHERE idSettore = :idSettore AND disponibile = 1 LIMIT 1";
                $stmtPosto = $conn->prepare($queryPosto);
                $stmtPosto->bindParam(':idSettore', $idSettore, PDO::PARAM_INT);
                $stmtPosto->execute();
                $posto = $stmtPosto->fetch(PDO::FETCH_ASSOC);

                if ($posto) {
                    // Associa il posto all'utente
                    $idPosto = $posto['idPosto'];
                    $queryUpdatePosto = "UPDATE tPosto SET disponibile = 0, idUtente = :idUtente WHERE idPosto = :idPosto";
                    $stmtUpdatePosto = $conn->prepare($queryUpdatePosto);
                    $stmtUpdatePosto->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
                    $stmtUpdatePosto->bindParam(':idPosto', $idPosto, PDO::PARAM_INT);
                    $stmtUpdatePosto->execute();
                } else {
                    echo json_encode(["success" => false, "message" => "Nessun posto disponibile"]);
                    $conn->rollBack();
                    exit;
                }
            }

            // Aggiungi la prenotazione
            $queryPrenotazione = "INSERT INTO tPrenotazione (idEvento, idSettore, idUtente, prezzo, statoPrenotazione, idPosto, dataPrenotazione)
                                  VALUES (:idEvento, :idSettore, :idUtente, :prezzo, 'in elaborazione', :idPosto, NOW())";
            $stmtPrenotazione = $conn->prepare($queryPrenotazione);
            $stmtPrenotazione->bindParam(':idEvento', $idEvento, PDO::PARAM_INT);
            $stmtPrenotazione->bindParam(':idSettore', $idSettore, PDO::PARAM_INT);
            $stmtPrenotazione->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
            $stmtPrenotazione->bindParam(':prezzo', $settore['prezzo'], PDO::PARAM_STR);
            $stmtPrenotazione->bindParam(':idPosto', $idPosto, PDO::PARAM_INT);
            $stmtPrenotazione->execute();

            // Salva l'ID della prenotazione
            $prenotazioniIds[] = $conn->lastInsertId();
        }

        // Inserisci nel carrello per ciascuna prenotazione
        // Inserisci nel carrello per ciascuna prenotazione
        foreach ($prenotazioniIds as $idPrenotazione) {
            $queryCarrello = "INSERT INTO tCarrello (idPrenotazione, dataAggiunta, idUtente, pagata, idEvento, disponibile)
                            VALUES (:idPrenotazione, NOW(), :idUtente, false, :idEvento, true)";
            $stmtCarrello = $conn->prepare($queryCarrello);
            $stmtCarrello->bindParam(':idPrenotazione', $idPrenotazione, PDO::PARAM_INT);
            $stmtCarrello->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
            $stmtCarrello->bindParam(':idEvento', $idEvento, PDO::PARAM_INT); // Aggiungi idEvento
            $stmtCarrello->execute();
        }


        // Commit transazione
        $conn->commit();
        echo json_encode(["success" => true, "message" => "Prenotazione creata con successo"]);
    } catch (PDOException $e) {
        $conn->rollBack();
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Metodo non supportato"]);
}
?>
