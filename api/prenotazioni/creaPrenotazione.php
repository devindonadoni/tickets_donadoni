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
        $query = "SELECT numerato, postiTotali, prezzo FROM tsettore WHERE idSettore = :idSettore";
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
        $queryUpdate = "UPDATE tsettore SET postiTotali = postiTotali - :quantita WHERE idSettore = :idSettore";
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
                $queryPosto = "SELECT idPosto FROM tposto WHERE idSettore = :idSettore AND disponibile = 1 LIMIT 1";
                $stmtposto = $conn->prepare($queryPosto);
                $stmtposto->bindParam(':idSettore', $idSettore, PDO::PARAM_INT);
                $stmtposto->execute();
                $posto = $stmtposto->fetch(PDO::FETCH_ASSOC);

                if ($posto) {
                    // Associa il posto all'utente
                    $idPosto = $posto['idPosto'];
                    $queryUpdatePosto = "UPDATE tposto SET disponibile = 0, idUtente = :idUtente WHERE idPosto = :idPosto";
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
            $queryPrenotazione = "INSERT INTO tprenotazione (idEvento, idSettore, idUtente, prezzo, statoPrenotazione, idPosto, dataPrenotazione)
                                  VALUES (:idEvento, :idSettore, :idUtente, :prezzo, 'in elaborazione', :idPosto, NOW())";
            $stmtprenotazione = $conn->prepare($queryPrenotazione);
            $stmtprenotazione->bindParam(':idEvento', $idEvento, PDO::PARAM_INT);
            $stmtprenotazione->bindParam(':idSettore', $idSettore, PDO::PARAM_INT);
            $stmtprenotazione->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
            $stmtprenotazione->bindParam(':prezzo', $settore['prezzo'], PDO::PARAM_STR);
            $stmtprenotazione->bindParam(':idPosto', $idPosto, PDO::PARAM_INT);
            $stmtprenotazione->execute();

            // Salva l'ID della prenotazione
            $prenotazioniIds[] = $conn->lastInsertId();
        }

        // Inserisci nel carrello per ciascuna prenotazione
        // Inserisci nel carrello per ciascuna prenotazione
        foreach ($prenotazioniIds as $idPrenotazione) {
            $queryCarrello = "INSERT INTO tcarrello (idPrenotazione, dataAggiunta, idUtente, pagata, idEvento, disponibile)
                            VALUES (:idPrenotazione, NOW(), :idUtente, false, :idEvento, true)";
            $stmtcarrello = $conn->prepare($queryCarrello);
            $stmtcarrello->bindParam(':idPrenotazione', $idPrenotazione, PDO::PARAM_INT);
            $stmtcarrello->bindParam(':idUtente', $idUtente, PDO::PARAM_INT);
            $stmtcarrello->bindParam(':idEvento', $idEvento, PDO::PARAM_INT); // Aggiungi idEvento
            $stmtcarrello->execute();
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
