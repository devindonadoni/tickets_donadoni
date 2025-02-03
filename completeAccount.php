<?php
session_start();
require_once('api/config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $cognome = isset($_POST['cognome']) ? trim($_POST['cognome']) : '';
    $data_nascita = isset($_POST['data_nascita']) ? $_POST['data_nascita'] : '';
    $luogo_nascita = isset($_POST['luogo_nascita']) ? trim($_POST['luogo_nascita']) : '';
    $codice_fiscale = isset($_POST['codice_fiscale']) ? trim($_POST['codice_fiscale']) : '';
    $termini_accettati = isset($_POST['termini']) ? true : false;

    // Verifica se tutti i campi sono compilati e se l'utente è maggiorenne
    if (!empty($nome) && !empty($cognome) && !empty($data_nascita) && !empty($luogo_nascita) && !empty($codice_fiscale) && $termini_accettati) {
        // Controllo maggiorenne
        $data_nascita_obj = new DateTime($data_nascita);
        $oggi = new DateTime();
        $differenza = $oggi->diff($data_nascita_obj);
        if ($differenza->y < 18) {
            $error = "Devi essere maggiorenne per registrarti.";
        } else {
            $nuovoNome = $nome;
            $nuovoCognome = $cognome;
            $email = $_SESSION["temp_email"];

            $query = "INSERT INTO tutente (nome, cognome, email, dataNascita, luogoNascita, codiceFiscale) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($db_remoto, $query);
            mysqli_stmt_bind_param($stmt, "ssssss", $nuovoNome, $nuovoCognome, $email, $data_nascita, $luogo_nascita, $codice_fiscale);
            mysqli_stmt_execute($stmt);
            $_SESSION['user'] = $email;
            $_SESSION['idUtente'] = mysqli_insert_id($db_remoto);

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
        }
    } else {
        $error = "Tutti i campi sono obbligatori e devi accettare i termini e condizioni.";
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://kit.fontawesome.com/1c5c930d58.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles/style-completeAccount.css">
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
                <h1>COMPLETA <br> IL TUO ACCOUNT</h1>
                <div class="credetianls">

                    <div class="nome-cognome">
                        <input type="text" class="input-nome" placeholder="Nome" name="nome" required>
                        <input type="text" class="input-cognome" placeholder="Cognome" name="cognome" required>
                    </div>
                    
                    <div class="codice-fiscale">
                        <input type="text" name="codice_fiscale" class="input-codice" placeholder="Codice Fiscale"
                            required>
                    </div>



                    <div class="date-luogo">
                        <input type="date" name="data_nascita" class="input-date" required>
                        <input type="text" name="luogo_nascita" class="input-luogo" placeholder="Luogo di Nascita" required>
                    </div>
                    
                        
                    <div class="remember-me">
                        <input type="checkbox" name="termini" required>
                        <label for="termini">Accetto i <a href="terms.php">termini & condizioni</a></label>
                    </div>
                </div>
                <?php if (isset($error)): ?>
                    <p style="color: red;"> <?= htmlspecialchars($error) ?> </p>
                <?php elseif (isset($success)): ?>
                    <p style="color: green;"> <?= htmlspecialchars($success) ?> </p>
                <?php endif; ?>
                <div class="login-button">
                    <input type="submit" name="register" value="COMPLETA">
                </div>
                </div>
        </form>
    </div>
</body>

</html>