<?php
require_once('api/config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $cognome = isset($_POST['cognome']) ? trim($_POST['cognome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $data_nascita = isset($_POST['data_nascita']) ? $_POST['data_nascita'] : '';
    $luogo_nascita = isset($_POST['luogo_nascita']) ? trim($_POST['luogo_nascita']) : '';
    $codice_fiscale = isset($_POST['codice_fiscale']) ? trim($_POST['codice_fiscale']) : '';
    $termini_accettati = isset($_POST['termini']) ? true : false;

    // Controlla se tutti i campi sono compilati
    if (!empty($nome) && !empty($cognome) && !empty($email) && !empty($password) && !empty($data_nascita) && !empty($luogo_nascita) && !empty($codice_fiscale) && $termini_accettati) {
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
                // Controllo maggiorenne
                $data_nascita_obj = new DateTime($data_nascita);
                $oggi = new DateTime();
                $differenza = $oggi->diff($data_nascita_obj);
                if ($differenza->y < 18) {
                    $error = "Devi essere maggiorenne per registrarti.";
                } else {
                    // Prepara la query per inserire i dati
                    $query = "INSERT INTO tutente (nome, cognome, email, password, dataNascita, luogoNascita, codiceFiscale) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($db_remoto, $query);
                    if ($stmt) {
                        // Collega i parametri alla query
                        mysqli_stmt_bind_param($stmt, "sssssss", $nome, $cognome, $email, $password, $data_nascita, $luogo_nascita, $codice_fiscale);
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
            }
            mysqli_stmt_close($stmt_check);
        } else {
            $error = "Errore durante la verifica dell'email.";
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/1c5c930d58.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="styles/style-login.css">

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
                <h1>CREA UN NUOVO ACCOUNT</h1>
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

                    <div class="username">
                        <input type="email" name="email" class="input-filter" placeholder="E-mail" required>
                    </div>
                    <div class="password">
                        <input type="password" name="password" class="input-filter" placeholder="Password" required>
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
                    <input type="submit" name="register" value="REGISTRATI">
                </div>
                <div class="login-footer">
                    <a href="login.php">Hai già un account? Effettua il login</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
