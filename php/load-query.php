<?php
require_once('../api/config/config.php');

if (!$db_remoto) {
    die("Errore di connessione: " . mysqli_connect_error());
}

$sql = "SELECT 
    *
FROM 
    tevento e
JOIN 
    tluogo l
ON 
    e.idLuogo = l.idLuogo";

$result = mysqli_query($db_remoto, $sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    die("NO DATA");
}

header('Content-Type: application/json');
echo json_encode($data);
?>