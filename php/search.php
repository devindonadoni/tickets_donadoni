<?php
header('Content-Type: application/json');

$db_remoto = mysqli_connect("localhost", "root", "", "tickets_donadoni");

if (isset($_POST['query']) || isset($_POST['categoria']) || isset($_POST['citta']) || isset($_POST['data'])) {
    $search = isset($_POST['query']) ? $db_remoto->real_escape_string($_POST['query']) : '';
    $categoria = isset($_POST['categoria']) ? $db_remoto->real_escape_string($_POST['categoria']) : '';
    $luogo = isset($_POST['citta']) ? $db_remoto->real_escape_string($_POST['citta']) : '';
    $data = isset($_POST['data']) ? $db_remoto->real_escape_string($_POST['data']) : '';

    $query = "
        SELECT 
            e.idEvento, 
            e.nomeEvento, 
            e.dataOraEvento, 
            e.pathFotoLocandina, 
            l.citta,
            -- Verifica se l'evento è sold out
            CASE 
                WHEN (
                    SELECT COUNT(*) 
                    FROM tSettore 
                    WHERE idEvento = e.idEvento
                ) = 0 THEN 1 -- Nessun settore associato
                WHEN (
                    SELECT SUM(postiTotali) 
                    FROM tSettore 
                    WHERE idEvento = e.idEvento
                ) = 0 THEN 1 -- Somma dei posti disponibili è zero
                ELSE 0
            END AS soldOut
        FROM tEvento e 
        JOIN tLuogo l ON e.idLuogo = l.idLuogo 
        WHERE 1=1
    ";

    // Aggiungere i filtri solo se presenti
    if (!empty($search)) {
        $query .= " AND e.nomeEvento LIKE '%$search%'";
    }
    if (!empty($categoria)) {
        $query .= " AND e.categoria = '$categoria'";
    }
    if (!empty($luogo)) {
        $query .= " AND l.citta = '$luogo'";
    }
    if (!empty($data)) {
        $query .= " AND DATE(e.dataOraEvento) = '$data'";
    }

    $result = $db_remoto->query($query);
    $eventi = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $eventi[] = $row;
        }
    }

    echo json_encode($eventi);
    exit;
}
?>
