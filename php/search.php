<?php
$db_remoto = mysqli_connect("localhost", "root", "", "tickets_donadoni");

if (isset($_POST['query']) || isset($_POST['categoria']) || isset($_POST['luogo']) || isset($_POST['data'])) {
    $search = isset($_POST['query']) ? $db_remoto->real_escape_string($_POST['query']) : '';
    $categoria = isset($_POST['categoria']) ? $db_remoto->real_escape_string($_POST['categoria']) : '';
    $luogo = isset($_POST['citta']) ? $db_remoto->real_escape_string($_POST['citta']) : '';
    $data = isset($_POST['data']) ? $db_remoto->real_escape_string($_POST['data']) : '';
    
    $query = "SELECT * FROM tEvento e 
              JOIN tLuogo l ON e.idLuogo = l.idLuogo 
              WHERE 1=1"; // Condizione di base sempre vera

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

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="swiper-slide">';
            echo ' <a href="post-view.php?idEvento=' . $row['idEvento'] . '">';
            echo ' <div class="card-result">';
            echo '<img id="background-image" src="' . $row['pathFotoLocandina'] . '" alt="Card Image">';
            echo '<div class="didascalia">';
            echo ' <h2>' . $row['nomeEvento'] . '</h2>';
            echo '<h3>' . $row['citta'] . '</h3>';
            echo '<h3>' . $row['dataOraEvento'] . '</h3>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
            echo '</div>';
        }
    } else {
        echo "<p style='display: flex;
            justify-content: center; 
            align-items: center;     
            height: 100%;            
            width: 100%;             
            text-align: center;      
            font-size: 20px;'>No results found</p>";
    }
    exit; // Termina l'esecuzione
}
?>
