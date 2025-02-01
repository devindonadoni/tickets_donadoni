<?php
require_once('api/config/config.php');
include_once 'api/config/init.php';

$utente = "";
if (isset($_SESSION['user'])) {
    $utente = $_SESSION['user'];
    $idUtente = $_SESSION['idUtente'];

    $sql = "SELECT COUNT(*) AS count FROM tcarrello WHERE pagata = 0 AND disponibile = 1 AND idUtente = '$idUtente'";
    $result = mysqli_query($db_remoto, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $countCart = $row['count'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>AULIC TICKET</title>
    <link rel="icon" type="image/png" href="images/logo-aulic.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://kit.fontawesome.com/1c5c930d58.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

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
                        <li><a href="#filtri-container" id="scrollToFilters"><i
                                    class="fa-solid fa-magnifying-glass"></i></a></li>
                        <li>
                            <a href="cart.php" class="cart-wrapper">
                                <i class="fa-solid fa-heart"></i>
                                <?php
                                if ($utente) {
                                    if ($result) {
                                        echo '<span class="cart-count">';
                                        echo $countCart;
                                        echo '</span> <!-- Numero hardcoded -->';
                                    }
                                }
                                ?>
                            </a>
                        </li>

                        <?php
                        if ($utente != "") {
                            $nomeUtente = "";
                            $emailUtente = "";
                            $sqlNome = "SELECT * FROM tUtente WHERE idUtente = '$idUtente'";
                            $resultNome = mysqli_query($db_remoto, $sqlNome);


                            if (mysqli_num_rows($resultNome) > 0) {
                                while ($row = mysqli_fetch_assoc($resultNome)) {
                                    $nomeUtente = $row['nome'] . " " . $row["cognome"];
                                    $emailUtente = $row['email'];
                                }
                            }

                            echo '<li class="profile-icon">
                                <a class="profile-link">
                                    <i class="fa-solid fa-user"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <div class="name-container">
                                        <h1>' . $nomeUtente . '</h1>
                                        <p>' . $emailUtente . '</p>
                                    </div>
                                    <div class="dropmenu-element" onclick="rindirizza(\'profilo\')">
                                        <i class="fa-solid fa-user"></i>
                                        <p>Profilo</p>
                                    </div>
                                    <div class="dropmenu-element" onclick="rindirizza(\'Biglietti\')">
                                        <i class="fa-solid fa-ticket"></i>
                                        <p>Biglietti</p>
                                    </div>
                                    <div class="dropmenu-element" onclick="rindirizza(\'Impostazioni\')">
                                        <i class="fa-solid fa-gear"></i>    
                                        <p>Impostazioni</p>
                                    </div>
                                    <div class="dropmenu-element" onclick="rindirizza(\'Help\')">
                                        <i class="fa-solid fa-comments"></i>
                                        <p>Help</p>
                                    </div>
                                    <div class="dropmenu-element-signout" onclick="logout()">
                                        <i class="fa-solid fa-sign-out"></i>
                                        <p>SIGN OUT</p>
                                    </div>
                                </div>
                            </li>';
                        } else {
                            echo '<li><a href="login.php"><i class="fa-solid fa-user"></i></a></li>';
                        }
                        ?>
                    </ul>
                </div>
                <i class="fa fa-bars" onclick="showMenu()"></i>
            </nav>
        </section>

        <!-- Carousel -->
        <div class="image-container swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <a href="ciao.php" class="card-link">
                        <img id="background-image" src="images/concerto3.png" alt="Card Image">
                        <div class="text-overlay">
                            <p>Travis Scott</p>
                            <h2>22 luglio 22:00</h2>
                        </div>
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="ciao.php" class="card-link">
                        <img id="background-image" src="images/concerto-sfera.png" alt="Card Image">
                        <div class="text-overlay">
                            <p>Sfera Ebbasta</p>
                            <h2>12 luglio 21:00</h2>
                        </div>
                    </a>
                </div>
                <div class="swiper-slide">
                    <a href="ciao.php" class="card-link">
                        <img id="background-image" src="images/concerto3.png" alt="Card Image">
                        <div class="text-overlay">
                            <p>Sfera Ebbasta</p>
                            <h2>12 luglio 21:00</h2>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Swiper Navigation -->
            <div class="image-next swiper-button-next"></div>
            <div class="image-prev swiper-button-prev"></div>

            <!-- Swiper Pagination -->
            <div class="image-pagination swiper-pagination"></div>
        </div>

        <!-- categoria -->
        <h1 class="label">Categorie:</h1>
        <div class="category">
            <div class="single-category">
                <a href="category-view.php?categoria=partite">
                    <h2>Sport e Partite</h2>
                    <img src="images/stadium.png" alt="Stadium image">
                    <div class="gradient-overlay"></div>
                </a>
            </div>
            <div class="single-category">
                <a href="category-view.php?categoria=teatro">
                    <h2>Teatro</h2>
                    <img src="images/theatre.png" alt="Theatre image">
                    <div class="gradient-overlay"></div>
                </a>
            </div>
            <div class="single-category">
                <a href="category-view.php?categoria=tour">
                    <h2>Tour</h2>
                    <img src="images/tour.png" alt="Tour image">
                    <div class="gradient-overlay"></div>
                </a>
            </div>
            <div class="single-category">
                <a href="category-view.php?categoria=partite">
                    <h2>Concerti</h2>
                    <img src="images/concerto-categ.png" alt="Concerts image">
                    <div class="gradient-overlay"></div>
                </a>
            </div>
        </div>


        <!-- filtri -->
        <h1 id="filtri-container" class="label">Cerca l'evento perfetto per te!</h1>
        <div class="filtri">
            <div class="titolo">
                <h1>Titolo evento</h1>
                <input type="text" id="search-box" placeholder="Scrivi qui il titolo..." class="input-filter">
            </div>
            <div class="categoria">
                <h1>Categoria</h1>
                <select id="categoria-select" class="select-filter">
                    <option value="">Scegli la categoria...</option>
                    <option value="concerto">Concerto</option>
                    <option value="partite">partite</option>
                    <option value="teatro">teatro</option>
                    <option value="tour">tour</option>
                </select>
            </div>
            <div class="luogo">
                <h1>Luogo</h1>

                <select id="citta-select" class="select-filter">
                    <option value="" disablected>Scegli la citta'...</option>
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
            <div class="remove-filter" onclick="removeFilter()">
                <h1>REMOVE</h1>
            </div>

        </div>


        <!-- result  -->
        <div id="results-section" style="display: none;">
            <h1 class="result-label">Risultato:</h1>
            <div class="result-container swiper">
                <div id="result" class="swiper-wrapper"></div>
                <div class="result-next swiper-button-next"></div>
                <div class="result-prev swiper-button-prev"></div>

                <!-- Swiper Pagination -->
                <div class="result-pagination swiper-pagination"></div>
            </div>
        </div>

        <!-- partite -->
        <a href="category-view.php?categoria=partite">
            <h1 class="label">Partite:</h1>
        </a>
        <div class="partite-container swiper">
            <div id="slide-partite" class="swiper-wrapper">
            </div>
            <div class="result-next swiper-button-next"></div>
            <div class="result-prev swiper-button-prev"></div>

            <!-- Swiper Pagination -->
            <div class="result-pagination swiper-pagination"></div>
        </div>

        <!-- concerti -->
        <a href="category-view.php?categoria=concerto">
            <h1 class="label">Concerti:</h1>
        </a>
        <div class="concerto-container swiper">
            <div id="slide-concerto" class="swiper-wrapper">
            </div>
            <div class="result-next swiper-button-next"></div>
            <div class="result-prev swiper-button-prev"></div>

            <!-- Swiper Pagination -->
            <div class="result-pagination swiper-pagination"></div>
        </div>

        <!-- tour -->
        <a href="category-view.php?categoria=tour">
            <h1 class="label">Tour:</h1>
        </a>
        <div class="tour-container swiper">
            <div id="slide-tour" class="swiper-wrapper">
            </div>
            <div class="result-next swiper-button-next"></div>
            <div class="result-prev swiper-button-prev"></div>

            <!-- Swiper Pagination -->
            <div class="result-pagination swiper-pagination"></div>
        </div>


        <!-- teatro -->
        <a href="category-view.php?categoria=teatro">
            <h1 class="label">teatro:</h1>
        </a>
        <div class="teatro-container swiper">
            <div id="slide-teatro" class="swiper-wrapper">
            </div>
            <div class="result-next swiper-button-next"></div>
            <div class="result-prev swiper-button-prev"></div>

            <!-- Swiper Pagination -->
            <div class="result-pagination swiper-pagination"></div>
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

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="script/load-events.js"></script>
    <script src="script/script.js"></script>
    <script src="script/search.js"></script>
</body>


<script src="script/profile-menu.js"></script>
<script>


    document.getElementById("scrollToFilters").addEventListener("click", function (event) {
        event.preventDefault(); // Evita il comportamento predefinito del link
        const filtersSection = document.getElementById("filtri-container");
        filtersSection.scrollIntoView({ behavior: "smooth" });
    });


    var navlinks = document.getElementById("navlinks");
    var fa_times_circle = document.getElementById("fa-times-circle");
    function showMenu() {
        navlinks.style.right = "0px";
        fa_times_circle.style.display = "flex";
    }
    function hideMenu() {
        navlinks.style.right = "-200px";
    }
</script>

<script>

    function rindirizza(pagina) {
        window.location.href = pagina + '.php';
    }



    function logout() {
        Swal.fire({
            title: 'Sei sicuro?',
            text: `Effettuarai il logout.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sì!',
            cancelButtonText: 'Annulla',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            } else {
                Swal.fire(
                    'Errore',
                    'ah ah ah NON PUOI ANDARTENE COSI FACILMENTE',
                    'error'
                );
            }
        });
    }


</script>

</html>