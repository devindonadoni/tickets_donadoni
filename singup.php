<?php
require_once('api/config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $cognome = isset($_POST['cognome']) ? trim($_POST['cognome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Controlla se tutti i campi sono compilati
    if (!empty($nome) && !empty($cognome) && !empty($email) && !empty($password)) {
        // Verifica se l'email è già presente nel database
        $check_email_query = "SELECT * FROM tutente WHERE email = ?";
        $stmt_check = mysqli_prepare($db_remoto, $check_email_query);
        if ($stmt_check) {
            mysqli_stmt_bind_param($stmt_check, "s", $email);
            mysqli_stmt_execute($stmt_check);
            $result = mysqli_stmt_get_result($stmt_check);
            if (mysqli_num_rows($result) > 0) {
                // Email già esistente
                $error = "L'email è già registrata. Prova con un'altra.";
            } else {
                // Prepara la query per inserire i dati
                $query = "INSERT INTO tutente (nome, cognome, email, password) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($db_remoto, $query);
                if ($stmt) {
                    // Collega i parametri alla query
                    mysqli_stmt_bind_param($stmt, "ssss", $nome, $cognome, $email, $password);
                    // Esegui la query
                    if (mysqli_stmt_execute($stmt)) {
                        $success = "Account creato con successo! Ora puoi effettuare il login.";
                    } else {
                        $error = "Errore durante la creazione dell'account. Riprova.";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Errore nella preparazione della query.";
                }
            }
            mysqli_stmt_close($stmt_check);
        } else {
            $error = "Errore durante la verifica dell'email.";
        }
    } else {
        $error = "Tutti i campi sono obbligatori.";
    }

    mysqli_close($db_remoto);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Registrazione Account</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/1c5c930d58.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles/style-login.css">
</head>
<body>
    <div class="main-container">
        <section class="header">
            <nav class="primary-nav">
                <a href="index.php"><img src="images/logo-removed1.png" alt="Logo"></a>
            </nav>
        </section>
        <form action="" method="POST">
            <div class="login-container">
                <h1>CREA UN NUOVO ACCOUNT</h1>
                <div class="credetianls">
                    <div class="username">
                        <input type="text" name="nome" class="input-filter" placeholder="Nome" required>
                    </div>
                    <div class="username" style="margin-bottom: 3rem;">
                        <input type="text" name="cognome" class="input-filter" placeholder="Cognome" required>
                    </div>
                    <div class="username">
                        <input type="email" name="email" class="input-filter" placeholder="E-mail" required>
                    </div>
                    <div class="password">
                        <input type="password" name="password" class="input-filter" placeholder="Password" required>
                    </div>
                </div>
                <?php if (isset($error)): ?>
                    <p style="color: red;"> <?= htmlspecialchars($error) ?> </p>
                <?php elseif (isset($success)): ?>
                    <p style="color: green;"> <?= htmlspecialchars($success) ?> </p>
                <?php endif; ?>
                <div class="login-button">
                    <input type="submit" name="register" value="REGISTRATI">
                </div>
                <div class="login-footer">
                    <a href="login.php">Hai già un account? Effettua il login</a>
                </div>
            </div>
        </form>

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
</html>