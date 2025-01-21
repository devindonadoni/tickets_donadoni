<?php
$db_remoto = mysqli_connect("localhost", "root", "", "tickets_donadoni");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($email) && !empty($password)) {
        $query = "SELECT * FROM tUtente WHERE email = ? AND password = ?";
        $stmt = mysqli_prepare($db_remoto, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $email, $password);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) === 1) {
                session_start();
                $_SESSION['user'] = $email;
                header("Location: index.php");
                exit;
            } else {
                $error = "Username o password errati.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Errore nella preparazione della query.";
        }
    } else {
        $error = "Inserisci tutti i campi.";
    }
}

mysqli_close($db_remoto);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <link rel="stylesheet" href="styles/style-login.css">
</head>

<body>
    <!-- Contenitore principale -->
    <div class="main-container">
        <!-- Header -->
        <section class="header">
            <nav class="primary-nav">
                <a href="index.php"><img src="images/logo-removed1.png" alt="Logo"></a>
            </nav>
        </section>


        <form action="" method="POST">
            <div class="login-container">
                <h1>EFFETTUA <br> IL LOGIN </h1>
                <div class="credetianls">
                    <div class="username">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="email" class="input-filter" placeholder="E-mail">
                    </div>
                    <div class="password">
                        <i class="fa-solid fa-lock"></i>
                        <input type="text" name="password" class="input-filter" placeholder="Password">
                    </div>
                    <div class="remember-me">
                        <input type="checkbox" id="exampleCheckbox" name="exampleCheckbox">
                        <h1>Remember me</h1>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <div class="login-button">
                    <input type="submit" name="login" value="LOGIN">
                </div>

                <!-- Aggiunta delle scritte cliccabili -->
                <div class="login-footer">
                    <a href="forgot-password.php">Forgot Password?</a>
                    <span>|</span>
                    <a href="singup.php">Don't have an account?</a>
                </div>
            </div>
        </form>


        <!-- <div class="pay">
            <div class="method-payment">
                <img src="images/cartadeocente.png" alt="">
                <img src="images/cartecultura.png" alt="">
                <img src="images/paypall-removebg-preview.png" alt="">
                <img src="images/mastercard-removebg-preview.png" alt="">
                <img src="images/visa-removebg-preview.png" alt="">
            </div>
        </div> -->

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
</body>

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


</html>