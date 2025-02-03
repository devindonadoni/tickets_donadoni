<?php
session_start();
require_once('api/config/config.php');

if (isset($_POST['credential'])) {
    $token = $_POST['credential'];
    $client_id = "TUO_CLIENT_ID";

    // Verifica il token Google
    $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $token;
    $response = file_get_contents($url);
    $userInfo = json_decode($response, true);

    if (isset($userInfo['email'])) {
        $email = $userInfo['email'];

        // Controlla se l'utente esiste
        $query = "SELECT idUtente FROM tutente WHERE email = ?";
        $stmt = mysqli_prepare($db_remoto, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['user'] = $email;
            $_SESSION['idUtente'] = $row['idUtente'];
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
            // // Se non esiste, lo registriamo
            // $query = "INSERT INTO tutente (email) VALUES (?)";
            // $stmt = mysqli_prepare($db_remoto, $query);
            // mysqli_stmt_bind_param($stmt, "s", $email);
            // mysqli_stmt_execute($stmt);
            // $_SESSION['user'] = $email;
            // $_SESSION['idUtente'] = mysqli_insert_id($db_remoto);
            $_SESSION["temp_email"] = $email;
            echo $_SESSION["temp_email"];
            header(header: "Location: completeAccount.php");
            exit;
        }

    }
}
?>