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

  echo $idUtente;
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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://kit.fontawesome.com/1c5c930d58.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

  <link rel="stylesheet" href="styles/style-index.css">
  <link rel="stylesheet" href="styles/style-tickets.css">
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
            <li>
              <a href="cart.php" class="cart-wrapper">
                <i class="fa-solid fa-heart"></i>
                <?php
                if ($utente) {
                  if ($result) {
                    echo '<span class="cart-count">';
                    echo $countCart;
                    echo '</span>';
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

    <h1 class="label">Le tue prenotazioni</h1>
    <div class="container-label">
      <ul class="tabs">
        <li class="tab-item">
          <a href="#item1" class="active">Tutte</a>
        </li>
        <li class="tab-item">
          <a href="#item2">Pagate</a>
        </li>
        <li class="tab-item">
          <a href="#item3">Cancellate</a>
        </li>
        <li class="tab-item">
          <a href="#item4">Scadute</a>
        </li>
        <div class="search-item">
          <a href=""><i class="fa-solid fa-magnifying-glass"></i></a>
        </div>
      </ul>
    </div>





    <div class="grid-container">
      <div class="wrapper_tab-content">
        <div id="item1" class="tab-content content-visible">
          <!-- Filtri -->
          <div class="container-filter">
            <div class="single-filter">
              <p>Evento</p>
              <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="single-filter">
              <p>Luogo</p>
              <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="single-filter">
              <p>Data</p>
              <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="single-filter">
              <p>Posto</p>
              <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="single-filter">
              <p>Totale</p>
              <i class="fa-solid fa-chevron-down"></i>
            </div>
          </div>

          <!-- Eventi -->
          <div class="container-event">
            <div class="event-info">
              <h1>TRAVIS SCOTT</h1> <!-- Colonna 1: Evento -->
              <p>ROMA | STADIO OLIMPICO</p> <!-- Colonna 2: Luogo -->
              <p>10 AGOSTO 2024</p> <!-- Colonna 3: Data -->
              <p>Tribuna Ce2</p> <!-- Colonna 4: Posto -->
              <div class="total"> <!-- Colonna 5: Totale -->
                <p>€370,00</p>
                <i class="fa-solid fa-download"></i>
              </div>
            </div>

            <div class="event-info">
              <h1>TRAVIS SCOTT</h1> <!-- Colonna 1: Evento -->
              <p>ROMA | STADIO OLIMPICO</p> <!-- Colonna 2: Luogo -->
              <p>10 AGOSTO 2024</p> <!-- Colonna 3: Data -->
              <p>Tribuna Ce3</p> <!-- Colonna 4: Posto -->
              <div class="total"> <!-- Colonna 5: Totale -->
                <p>€370,00</p>
                <i class="fa-solid fa-download"></i>
              </div>
            </div>
          </div>
        </div>




        <!-- event 2-->
        <div id="item2" class="tab-content">
          <!-- Filtri -->
          <div class="container-filter">
            <div class="single-filter">
              <p>Evento</p>
              <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="single-filter">
              <p>Luogo</p>
              <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="single-filter">
              <p>Data</p>
              <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="single-filter">
              <p>Posto</p>
              <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="single-filter">
              <p>Totale</p>
              <i class="fa-solid fa-chevron-down"></i>
            </div>
          </div>

          <!-- Eventi -->
          <div class="container-event">
            <div class="event-info">
              <h1>TRAVIS SCOTT</h1> <!-- Colonna 1: Evento -->
              <p>ROMA | STADIO OLIMPICO</p> <!-- Colonna 2: Luogo -->
              <p>10 AGOSTO 2024</p> <!-- Colonna 3: Data -->
              <p>Tribuna Ce4</p> <!-- Colonna 4: Posto -->
              <div class="total"> <!-- Colonna 5: Totale -->
                <p>€370,00</p>
                <i class="fa-solid fa-download"></i>
              </div>
            </div>

            <div class="event-info">
              <h1>TRAVIS SCOTT</h1> <!-- Colonna 1: Evento -->
              <p>ROMA | STADIO OLIMPICO</p> <!-- Colonna 2: Luogo -->
              <p>10 AGOSTO 2024</p> <!-- Colonna 3: Data -->
              <p>Tribuna Ce5</p> <!-- Colonna 4: Posto -->
              <div class="total"> <!-- Colonna 5: Totale -->
                <p>€370,00</p>
                <i class="fa-solid fa-download"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="script/load-events.js"></script>
<script src="script/script.js"></script>
<script src="script/search.js"></script>
<script src="script/profile-menu.js"></script>

<script>
  function getTab(el) {
    const active = document.querySelector(".active");
    const visible = document.querySelector(".content-visible");
    const tabContent = document.getElementById(el.href.split("#")[1]);

    active.classList.toggle("active");
    visible.classList.toggle("content-visible");

    el.classList.add("active");
    tabContent.classList.add("content-visible");
  }

  document.addEventListener("click", (e) => {
    if (e.target.matches(".tab-item a")) {
      getTab(e.target);
    }
  });
</script>

</html>