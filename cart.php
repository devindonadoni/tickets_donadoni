<?php
$db_remoto = mysqli_connect("localhost", "root", "", "tickets_donadoni");
session_start();
$utente = "";
if (isset($_SESSION['user'])) {
    $utente = $_SESSION['user'];
}
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

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="styles/style-index.css">
    <link rel="stylesheet" href="styles/style-cart.css">
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
                        <li><a href="cart.php"><i class="fa-solid fa-heart"></i></a></li>
                        <?php
                        if ($utente != "") {
                            echo '<li><a href="profilo.php"><i class="fa-solid fa-user"></i></a></li>';
                        } else {
                            echo '<li><a href="login.php"><i class="fa-solid fa-user"></i></a></li>';
                        }
                        ?>
                    </ul>
                </div>
                <i class="fa fa-bars" onclick="showMenu()"></i>
            </nav>
        </section>

        <h1 class="categoria-label">Carrello</h1>
        <div class="label-container">
            <p class="evento">Evento</p>
            <p>Timer</p>
            <p>Posto</p>
            <p>Totale</p>
        </div>

        <!-- Contenitore del carrello -->
        <div id="carrello-container">
            <?php
            if ($utente == "") {
                echo '
                <div class="no-items-container">
                    <p>Non sei loggato. Accedi per visualizzare il tuo carrello!</p>
                    <h1 onclick="redirect()">EFFETTUA IL LOGIN</h1>
                </div>';
            }
            ?>
        </div>

        <?php if ($utente != ""): ?>
            <div class="checkout-container" id="checkout-container">
                <div class="totale">
                    <h1>Totale:</h1>
                    <p id="totale">€0.00</p>
                </div>
                <div class="commissioni">
                    <h1>Commissioni:</h1>
                    <p id="commissioni">€0.00</p>
                </div>
                <div class="grantotale">
                    <h1>Gran totale</h1>
                    <h1 id="grantotale">€0.00</h1>
                </div>
                <div class="checkout-button-container">
                    <a href="api/prenotazioni/cart-query.php"><input type="submit" name="checkout-button"
                            id="checkout-button" value="CHECKOUT"></a>
                </div>
            </div>
        <?php endif; ?>

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

    <script src="script/load-cart.js"></script>
</body>

<script>
    function redirect() {
        const idUtente = "<?php echo isset($_SESSION['idUtente']) ? $_SESSION['idUtente'] : ''; ?>"; 
        if (!idUtente) {
            localStorage.setItem('redirect_url', window.location.href);
            window.location.href = 'login.php';
            return;
        }
    }
</script>

</html>