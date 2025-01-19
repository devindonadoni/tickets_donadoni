<?php
$db_remoto = mysqli_connect("localhost", "root", "", "tickets_donadoni");


if (isset($_POST['query'])) {
    $search = $db_remoto->real_escape_string($_POST['query']);
    $query = "SELECT * FROM tEvento WHERE nomeEvento LIKE '%$search%'";
    $result = $db_remoto->query($query);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="swiper-slide">';
            echo ' <a href="post-view.php?idEvento='.$row['idEvento'].'">';
            echo ' <div class="card-result">';
            echo '<img id="background-image" src="'.$row['pathFotoLocandina'].'" alt="Card Image">';
            echo '<div class="didascalia">';
            echo ' <h2>'.$row['nomeEvento'].'</h2>';
            echo '<h3>ROMA</h3>';
            echo '<h3>'.$row['dataOraEvento'].'</h3>';
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
    font-size: 20px; ' >No results found</p>";
    }
    exit; // Termina l'esecuzione
}


?>