<?php
session_start();
require_once '../config/database.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    $idSettore = $data['idSettore'] ?? null;  // idSettore ricevuto
    $quantita = $data['quantita'] ?? 1;  // Quantità dei posti richiesti, di default 1

    if (!$idSettore) {
        echo json_encode(["success" => false, "message" => "ID settore mancante"]);
        exit;
    }

    $db = new Database();
    $conn = $db->getConnection();

    try {
        // Cerca il settore per idSettore
        $query = "SELECT idSettore, numerato FROM tsettore WHERE idSettore = :idSettore";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':idSettore', $idSettore, PDO::PARAM_INT);
        $stmt->execute();

        $settore = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($settore) {
            if ((bool)$settore['numerato']) {
                // Se il settore è numerato, trova i posti disponibili
                $queryPosto = "SELECT idPosto, numeroPosto FROM tposto WHERE idSettore = :idSettore AND disponibile = 1 LIMIT :quantita";
                $stmtposto = $conn->prepare($queryPosto);
                $stmtposto->bindParam(':idSettore', $settore['idSettore'], PDO::PARAM_INT);
                $stmtposto->bindParam(':quantita', $quantita, PDO::PARAM_INT);
                $stmtposto->execute();

                $posti = $stmtposto->fetchAll(PDO::FETCH_ASSOC);

                if ($posti && count($posti) >= $quantita) {
                    echo json_encode([
                        "success" => true,
                        "numerato" => true,
                        "postiDisponibili" => $posti
                    ]);
                } else {
                    echo json_encode([ "success" => false, "message" => "Posti numerati insufficienti o non disponibili" ]);
                }
            } else {
                // Se il settore non è numerato
                echo json_encode([ "success" => true, "numerato" => false, "message" => "Il settore non è numerato" ]);
            }
        } else {
            echo json_encode([ "success" => false, "message" => "Settore non trovato" ]);
        }
    } catch (PDOException $e) {
        echo json_encode([ "success" => false, "message" => $e->getMessage() ]);
    }
} else {
    echo json_encode([ "success" => false, "message" => "Metodo non supportato" ]);
}
?>
