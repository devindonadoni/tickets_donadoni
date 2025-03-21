<?php
session_start();
require_once('api/config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $rememberMe = isset($_POST['remember_me']); // Controlla se il checkbox è selezionato

    if (!empty($email) && !empty($password)) {
        // Query per verificare email e password e ottenere anche l'idUtente
        $query = "SELECT idUtente, email FROM tutente WHERE email = ? AND password = ?";
        $stmt = mysqli_prepare($db_remoto, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $email, $password);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                // Ottieni idUtente ed email dall'utente autenticato
                $idUtente = $row['idUtente'];
                $email = $row['email'];

                // Registra i dati nella sessione
                $_SESSION['user'] = $email;
                $_SESSION['idUtente'] = $idUtente;

                // Se l'utente ha selezionato "Remember Me", crea un token
                if ($rememberMe) {
                    $token = bin2hex(random_bytes(32)); // Genera un token sicuro
                    $hashedToken = password_hash($token, PASSWORD_DEFAULT); // Hash del token

                    // Salva il token nel database
                    $updateQuery = "UPDATE tutente SET remember_token = ? WHERE idUtente = ?";
                    $updateStmt = mysqli_prepare($db_remoto, $updateQuery);
                    mysqli_stmt_bind_param($updateStmt, "si", $hashedToken, $idUtente);
                    mysqli_stmt_execute($updateStmt);
                    mysqli_stmt_close($updateStmt);

                    // Salva il token in un cookie (30 giorni)
                    setcookie("remember_token", $token, time() + (86400 * 30), "/", "", true, true);
                }

                // Recupera l'URL di reindirizzamento
                echo '<script>
                    const redirectUrl = localStorage.getItem("redirect_url");
                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                        localStorage.removeItem("redirect_url");
                    } else {
                        window.location.href = "index.php";
                    }
                </script>';
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
                        <input type="checkbox" id="remember_me" name="remember_me">
                        <label for="remember_me">Remember me</label>
                    </div>
                    <script src="https://accounts.google.com/gsi/client" async defer></script>
                    <div id="g_id_onload" data-client_id="170036456019-cufiacjous2ql4dqn1hasnsti8ms9a74.apps.googleusercontent.com"
                        data-login_uri="TICKETS/oauth_callback.php" data-auto_prompt="false">
                    </div>
                    <div class="g_id_signin" data-type="standard"></div>


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