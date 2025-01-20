<?php
$db_remoto = mysqli_connect("localhost", "root", "", "tickets_donadoni");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>AULIC TICKET</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://kit.fontawesome.com/1c5c930d58.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="styles/style-index.css">
</head>

<body>
    <!-- Contenitore principale -->
    <div class="main-container">
        <!-- Header -->
        <section class="header">
            <nav class="primary-nav">
                <a href="index.php"><img src="images/logo-removed1.png" alt="Logo"></a>
                <div class="nav-links" id="navlinks">
                    <i class="fa fa-times-circle" onclick="hideMenu()" style="display: none;" id="fa-times-circle"></i>
                    <ul>
                        <li><a href="cart.php"><i class="fa-solid fa-heart"></i></a></li>
                        <li><a href="profilo.php"><i class="fa-solid fa-user"></i></a></li>
                    </ul>
                </div>
                <i class="fa fa-bars" onclick="showMenu()"></i>
            </nav>
        </section>

        <h1 id="filtri-container" class="label"><?php echo strtoupper($_GET['categoria']); ?></h1>
        <div class="filtri">
            <div class="titolo">
                <h1>Titolo evento</h1>
                <input type="text" id="search-box" placeholder="Scrivi qui il titolo..." class="input-filter">
            </div>
            <div class="categoria">
                <h1>Categoria</h1>
                <select id="categoria-select" class="select-filter">
                    <option value="<?php echo $_GET['categoria']; ?>"><?php echo $_GET['categoria']; ?></option>
                </select>
            </div>
            <div class="luogo">
                <h1>Luogo</h1>

                <select id="citta-select" class="select-filter">
                    <option value="" disablected>Scegli la citta'</option>
                    <?php
                    $queryCitta = "SELECT DISTINCT citta FROM tluogo ORDER BY citta ASC";
                    $result = mysqli_query($db_remoto, $queryCitta);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row['citta'] . '">' . $row['citta'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            
            <div class="luogo">
                <h1>Data</h1>
                <input type="date" id="data-select" class="input-date">
            </div>


        </div>

        <div id="results-section" class="partite-container">
            <div id="result" class="card-orientation">
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

    </div>

    <script src="script/script.js"></script>
    <script src="script/load-concerti.js"></script>
    <script src="script/load-partite.js"></script>
    <script src="script/load-tour.js"></script>
    <script src="script/load-teatro.js"></script>
</body>

<script>

    function loadPosts() {
        function getUrlParameter(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }
        let category = getUrlParameter('categoria');
        console.log(category);
        fetch(`php/category-query.php?categoria=${category}`)
            .then(response => response.json())
            .then(posts => {
                let output = '';
                posts.forEach(post => {
                    const date = new Date(post.dataOraEvento);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = date.toLocaleString('default', { month: 'long' }).toUpperCase();
                    const hour = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');

                    const formattedDate = `${day} ${month} ${hour}:${minutes}`;
                    output += `
                    
                    <a href="post-view.php?idEvento=${post.idEvento}">
                            <div class="card-result">
                                <img id="background-image" src="${post.pathFotoLocandina}" alt="Card Image">
                                <div class="didascalia">
                                    <h2>${post.nomeEvento}</h2>
                                    <h3>${post.citta}</h3>
                                    <h3>${formattedDate}</h3>
                                </div>
                            </div>
                    </a>
                `;
                });
                document.getElementById('result').innerHTML = output;
            })
            .catch(error => console.error('Errore:', error));
    }

    loadPosts();

    $(document).ready(function () {

        $("#search-box, #citta-select, #data-select").on("input change", function () {
            let query = $("#search-box").val().trim();
            let citta = $("#citta-select").val();
            let categoria = $("#categoria-select").val();
            let data = $("#data-select").val();
            console.log(query);
            console.log(citta);
            console.log(categoria);
            console.log(data);

            // Controlla se sono selezionate le opzioni predefinite
            let noFiltersActive =
                query === "" &&
                (citta === "Scegli città" || citta === "") &&
                (data === "");

            if (noFiltersActive) {
                // Se non ci sono filtri attivi, mostra tutti i risultati
                loadPosts();
                $("#results-section").fadeIn(); // Mostra la sezione dei risultati
            } else {
                // Altrimenti, esegui la ricerca AJAX
                $.ajax({
                    url: "php/search.php",
                    method: "POST",
                    data: {
                        query: query,
                        citta: citta,
                        categoria: categoria,
                        data: data
                    },
                    success: function (data) {
                        if (data.trim().length > 0) {
                            $("#result").html(data); // Inserisci i risultati
                            $("#results-section").fadeIn(); // Mostra la sezione dei risultati
                        } else {
                            $("#result").html(""); // Nessun risultato
                            $("#results-section").fadeOut(); // Nascondi la sezione
                        }
                    },
                    error: function () {
                        console.error("Errore durante la richiesta AJAX.");
                    }
                });
            }
        });

    });

</script>

</html>