<?php
// Connessione al database
$db_remoto = mysqli_connect("localhost", "root", "", "tickets_donadoni");
header("Content-Type: application/json");


// Controlla se la connessione ha avuto successo
if (!$db_remoto) {
    die("Connessione al database fallita: " . mysqli_connect_error());
}

// Ottieni e sanifica il parametro 'categoria' dalla query string
$categoria = isset($_GET['categoria']) ? mysqli_real_escape_string($db_remoto, $_GET['categoria']) : '';

// Prepara la query SQL per selezionare gli eventi che appartengono alla categoria specificata
if (!empty($categoria)) {
    $query = "
        SELECT 
            e.*, 
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
        WHERE e.categoria = '$categoria'";
}

// Esegui la query
$result = mysqli_query($db_remoto, $query);

// Verifica se ci sono risultati
if ($result && mysqli_num_rows($result) > 0) {
    $eventi = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $eventi[] = $row;
    }
    
    // Restituisci i risultati come JSON
    echo json_encode($eventi);
} else {
    // Se non ci sono eventi, restituisci un array vuoto
    echo json_encode([]);
}

// Chiudi la connessione
mysqli_close($db_remoto);
?>
