<?php
$db_remoto = mysqli_connect("localhost", "root", "", "tickets_donadoni");

if (!$db_remoto) {
    die("Errore di connessione: " . mysqli_connect_error());
}

$idEvento = $_GET['idEvento'];

$sql = "SELECT 
    *
FROM 
    tEvento e
JOIN 
    tLuogo l
ON 
    e.idLuogo = l.idLuogo
WHERE 
    e.idEvento = $idEvento";

$result = mysqli_query($db_remoto, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AULIC TICKET</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://kit.fontawesome.com/1c5c930d58.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <link rel="stylesheet" href="styles/style-postview.css">
</head>

<body>
    <div class="main-container">
        <!-- Header -->
        <section class="header">
            <nav class="primary-nav">
                <a href="index.php"><img src="images/logo-removed1.png" alt="Logo"></a>
                <div class="nav-links" id="navlinks">
                    <i class="fa fa-times-circle" onclick="hideMenu()" style="display: none;" id="fa-times-circle"></i>
                    <ul>
                        <li><a href="preferiti.php"><i class="fa-solid fa-heart"></i></a></li>
                        <li><a href="profilo.php"><i class="fa-solid fa-user"></i></a></li>
                    </ul>
                </div>
                <i class="fa fa-bars" onclick="showMenu()"></i>
            </nav>
        </section>

        <div class="event-view-container">
            
                    <?php
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {

                            $date = new DateTime($row['dataOraEvento']);

                            // Estrai il giorno, il mese e l'ora
                            $day = $date->format('d');  // Ottiene il giorno con due cifre
                            $month = strtoupper($date->format('F'));  // Ottiene il mese in formato lungo e lo converte in maiuscolo
                            $hour = $date->format('H');  // Ottiene l'ora in formato a 24 ore
                            $minutes = $date->format('i');  // Ottiene i minuti
                    
                            // Format la data come "dd AGOSTO hh:mm"
                            $formattedDate = $day . ' ' . $month . ' ' . $hour . ':' . $minutes;
                            $categoria = strtoupper(string: $row['categoria']);

                            echo '<div class="event-details">';
                            echo '<img src="'.$row['pathFotoLocandina'].'" alt="'.$row['nomeEvento'].'" class="event-image">';
                            echo '<div id="event-info" class="event-info">';

                            echo '<a href="' . $row['categoria'] . '-section.php">' . $categoria . '</a>';
                            echo '<h1>' . $row['nomeEvento'] . '</h1>';
                            echo '<div class="info-location">';
                            echo '<div class="where">';
                            echo '<i class="fa-solid fa-calendar"></i>';
                            echo '<p>' . $row['citta'] . ' <br> ' . $row['locazione'] . '</p>';
                            echo '</div>';
                            echo '<div class="where">';
                            echo '<i class="fa-solid fa-location-dot"></i>';
                            echo '<p>' . $formattedDate . '</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '<h2>' . $row['biofrafia'] . '</h2>';

                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        die("NO DATA");
                    }
                    ?>

            <div class="tickets">
                <h1 class="label">Prenota il tuo biglietto adesso!</h1>
                <div class="ticket-container">
                    <div class="ticket-section">
                        <div class="ticket-label">
                            <h1>Numero di biglietti</h1>
                        </div>
                        <div class="ticket-selector">
                            <button class="remove-button" disabled>-</button>
                            <div class="ticket-counter">1</div>
                            <button class="add-button">+</button>
                        </div>
                    </div>
                    <div class="ticket-section">
                        <div class="ticket-label">
                            <h1>Categoria di biglietto:</h1>
                        </div>
                    </div>
                        <?php
                        $sql = "SELECT * FROM tEvento e 
                                JOIN tSettore l ON e.idEvento = l.idEvento 
                                WHERE e.idEvento = $idEvento";
                        $result = mysqli_query($db_remoto, $sql);

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<div class="ticket-section">';
                                echo '<div class="ticket-option">';
                                echo '<h1>'.$row['nomeSettore'].'</h1>';
                                echo '</div>';
                                echo '<div class="ticket-option">';
                                echo '<p id="prato-a-price">€'.$row['prezzo'].'</p>';
                                echo '<input type="radio" name="Categoria" value="prato a">';
                                echo '</div>';
                                echo '</div>';
                            }
                        }else{
                            echo '<h1 class="error">Nessun settore disponibile ancora</h1>';
                        }
                        ?>
                </div>

                <a href="check-out.php">
                    <div class="checkout-button-container">
                        <div class="checkout-button">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <p><span></span> biglietti, €<span id="total-price">0</span></p>
                        </div>
                    </div>
                </a>
            </div>

        <!-- concerti -->
        <a href="concerti-catogry.php">
            <h1 class="label">Eventi simili:</h1>
        </a>
        <div class="concerti-container swiper">
            <!-- Additional required wrapper -->
            <div id="slide-concerti" class="swiper-wrapper">
            </div>
            <div class="result-next swiper-button-next"></div>
            <div class="result-prev swiper-button-prev"></div>

        </div>
    </div>

    <div class="pay">
            <div class="method-payment">
                <img src="images/cartadeocente.png" alt="">
                <img src="images/cartecultura.png" alt="">
                <img src="images/paypall-removebg-preview.png" alt="">
                <img src="images/mastercard-removebg-preview.png" alt="">
                <img src="images/visa-removebg-preview.png" alt="">
            </div>
        </div>

        <!-- Wrapper esterno per il footer -->
        <div class="footer-wrapper">
            <footer class="footer">
                <div class="footer-decor-top">
                    <div class="top-left"></div>
                    <div class="top-center"></div>
                    <div class="top-right"></div>
                </div>
                <div class="footer-content">
                    <div class="footer-section about">
                        <h3>La Tua Azienda</h3>
                        <p>Siamo una compagnia impegnata a portare innovazione e creatività nel mondo digitale.</p>
                    </div>

                    <div class="footer-section links">
                        <h3>Link Utili</h3>
                        <ul>
                            <li><a href="#">Home</a></li>
                            <li><a href="#">Servizi</a></li>
                            <li><a href="#">Chi Siamo</a></li>
                            <li><a href="#">Contatti</a></li>
                        </ul>
                    </div>

                    <div class="footer-section contact">
                        <h3>Contatti</h3>
                        <p><strong>Email:</strong> info@azienda.com</p>
                        <p><strong>Telefono:</strong> +39 012 345 6789</p>
                    </div>

                    <div class="footer-section social">
                        <h3>Seguici</h3>
                        <div class="social-icons">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <div class="footer-decor">
                    <div class="wave"></div>
                    <div class="circle"></div>
                </div>
            </footer>
        </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="script/script.js"></script>
<script src="script/load-concerti.js"></script>

<script>
   document.addEventListener("DOMContentLoaded", function () {
    const ticketSelector = document.querySelector('.ticket-selector');
    const removeButton = ticketSelector.querySelector('.remove-button');
    const addButton = ticketSelector.querySelector('.add-button');
    const ticketCounter = ticketSelector.querySelector('.ticket-counter');
    const totalPriceElement = document.getElementById('total-price');
    const checkoutButton = document.querySelector('.checkout-button p');
    const radioButtons = document.querySelectorAll('input[name="Categoria"]');
    let numberOfTickets = 1;

    function updateCounter(newCount) {
        numberOfTickets = newCount;
        ticketCounter.textContent = numberOfTickets;
        removeButton.disabled = numberOfTickets === 1; // Disabilita il pulsante "Rimuovi" a 1 biglietto
        addButton.disabled = numberOfTickets === 4;    // Disabilita il pulsante "Aggiungi" a 4 biglietti
        calculateTotalPrice();
    }

    function calculateTotalPrice() {
        const selectedCategory = document.querySelector('input[name="Categoria"]:checked');
        if (!selectedCategory) {
            totalPriceElement.textContent = "0";
            checkoutButton.innerHTML = `<span>0</span> biglietti, €0`;
            return;
        }

        const categoryPrice = parseFloat(
            selectedCategory.closest('.ticket-section')
                .querySelector('.ticket-option p')
                .textContent.replace('€', '')
        );

        const totalPrice = (categoryPrice * numberOfTickets).toFixed(2);
        totalPriceElement.textContent = totalPrice;
        checkoutButton.innerHTML = `<span>${numberOfTickets}</span> biglietti, €${totalPrice}`;
    }

    addButton.addEventListener('click', () => {
        if (numberOfTickets < 4) { 
            updateCounter(numberOfTickets + 1);
        }
    });

    removeButton.addEventListener('click', () => {
        if (numberOfTickets > 1) {
            updateCounter(numberOfTickets - 1);
        }
    });

    radioButtons.forEach(button => {
        button.addEventListener('change', calculateTotalPrice);
    });

    updateCounter(1);
    calculateTotalPrice();
});

</script>


<script>
    // Variabile per salvare il valore selezionato
    let selectedValue = "";

    // Seleziona tutti i radio button per la categoria
    const radioButtons = document.querySelectorAll('input[name="Categoria"]');

    // Aggiungi un listener a ogni radio button
    radioButtons.forEach(radio => {
        radio.addEventListener('change', (event) => {
            selectedValue = event.target.value; // Salva il valore selezionato
            console.log(`Valore selezionato: ${selectedValue}`); // Mostra il valore nella console
        });
    });



    document.addEventListener("DOMContentLoaded", function () {
        const ticketSelector = document.querySelector('.ticket-selector');
        const removeButton = ticketSelector.querySelector('.remove-button');
        const addButton = ticketSelector.querySelector('.add-button');
        const ticketCounter = ticketSelector.querySelector('.ticket-counter');
        const totalPriceElement = document.getElementById('total-price');
        const checkoutButton = document.querySelector('.checkout-button p'); // Seleziona il testo del pulsante di checkout
        let numberOfTickets = 1;

        // Funzione per calcolare il prezzo totale e restituirlo come un intero
        function calculateTotalPrice() {
            const selectedCategory = document.querySelector('input[name="Categoria"]:checked');
            if (!selectedCategory) {
                totalPriceElement.textContent = "0";
                checkoutButton.innerHTML = `<span></span> 0 biglietti, €0`; // Modifica il testo del pulsante di checkout
                return 0;
            }

            // Selezioniamo il prezzo della categoria selezionata tramite id
            let categoryPrice = 0;
            if (selectedCategory.value === "prato a") {
                categoryPrice = parseFloat(document.getElementById('prato-a-price').textContent.replace('€', '').replace(',', '.'));
            } else if (selectedCategory.value === "anello rosso") {
                categoryPrice = parseFloat(document.getElementById('anello-rosso-price').textContent.replace('€', '').replace(',', '.'));
            }

            // Calcoliamo il prezzo totale e lo arrotondiamo a un intero
            const totalPrice = Math.round(categoryPrice * numberOfTickets);
            totalPriceElement.textContent = totalPrice;
            checkoutButton.innerHTML = `<span>${numberOfTickets}</span> biglietti, €${totalPrice}`; // Aggiorna il pulsante con il numero di biglietti e il totale

            return totalPrice; // Restituisce il totale come intero
        }

        // Aggiungi un biglietto
        addButton.addEventListener('click', function () {
            numberOfTickets++;
            ticketCounter.textContent = numberOfTickets;
            removeButton.disabled = false;
            calculateTotalPrice();
        });

        // Rimuovi un biglietto
        removeButton.addEventListener('click', function () {
            if (numberOfTickets > 1) {
                numberOfTickets--;
                ticketCounter.textContent = numberOfTickets;
                if (numberOfTickets === 1) {
                    removeButton.disabled = true;
                }
                calculateTotalPrice();
            }
        });

        // Calcolare il totale quando una categoria viene selezionata
        const radioButtons = document.querySelectorAll('input[name="Categoria"]');
        radioButtons.forEach(button => {
            button.addEventListener('change', calculateTotalPrice);
        });

        // Inizializza il calcolo del prezzo
        calculateTotalPrice();
    });


</script>

</html>