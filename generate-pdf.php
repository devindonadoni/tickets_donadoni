<?php
require __DIR__ . "/vendor/autoload.php";
require_once 'api/config/database.php';

use Dompdf\Dompdf;
use Dompdf\Options;


$db = new Database();
$pdo = $db->getConnection();

// Recupero dell'idPrenotazione da GET
$idPrenotazione = isset($_GET['idPrenotazione']) ? intval($_GET['idPrenotazione']) : 0;

// Query al database per ottenere i dettagli della prenotazione
$stmt = $pdo->prepare("SELECT e.nomeEvento, e.dataOraEvento, l.citta, l.locazione, p.prezzo, s.nomeSettore, t.numeroPosto, p.qr_image_path
                                FROM tprenotazione p
                                JOIN tEvento e ON p.idEvento = e.idEvento
                                JOIN tluogo l ON e.idLuogo = l.idLuogo
                                LEFT JOIN tSettore s ON p.idSettore = s.idSettore
                                LEFT JOIN tPosto t ON p.idPosto = t.idPosto
                                WHERE p.idPrenotazione = ?");
$stmt->execute([$idPrenotazione]);
$prenotazione = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$prenotazione) {
    die("Prenotazione non trovata.");
}

// Calcolo del prezzo totale (prev = 10% di prezzo)
$prev = $prenotazione['prezzo'] * 0.10;
$prezzoTotale = $prenotazione['prezzo'] + $prev;


$dateTime = new DateTime($prenotazione['dataOraEvento']);
$mesi = [
    "January" => "Gennaio", "February" => "Febbraio", "March" => "Marzo",
    "April" => "Aprile", "May" => "Maggio", "June" => "Giugno",
    "July" => "Luglio", "August" => "Agosto", "September" => "Settembre",
    "October" => "Ottobre", "November" => "Novembre", "December" => "Dicembre"
];

$dataFormattata = $dateTime->format("d F Y");
$dataFormattata = str_replace(array_keys($mesi), array_values($mesi), $dataFormattata);

$oraFormattata = $dateTime->format("H:i");

// Set Dompdf options
$options = new Options;
$options->setChroot(__DIR__);
$options->setIsRemoteEnabled(true);
$dompdf = new Dompdf($options);
$dompdf->setPaper("A4", "landscape");

// Caricamento HTML dinamico
$html = file_get_contents("template.html");
$html = str_replace([
    "{{ nomeEvento }}", "{{ citta }}", "{{ locazione }}",
    "{{ dataOraEvento }}", "{{ prezzo }}", "{{ prev }}", "{{ prezzoTotale }}", "{{ nomeSettore }}", "{{ numeroPosto }}",
    "{{ data }}", "{{ ora }}", "{{ qr-path }}"
], [
    $prenotazione['nomeEvento'], $prenotazione['citta'], $prenotazione['locazione'],
    $prenotazione['dataOraEvento'], number_format($prenotazione['prezzo'], 2), number_format($prev, 2), 
    number_format($prezzoTotale, 2), $prenotazione['nomeSettore'], ($prenotazione['numeroPosto']) ? $prenotazione['numeroPosto'] : "", $dataFormattata, $oraFormattata, $prenotazione['qr_image_path']
], $html);

$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream($string = preg_replace('/\s+/', '_', $prenotazione['nomeEvento'])."_Prenotazione_".$idPrenotazione.".pdf", ["Attachment" => false]);
?>
