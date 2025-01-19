function searchEvents() {
    var nomeEvento = document.getElementById("nomeEvento-input").value;
    var categoria = document.getElementById("categoria-select").value;
    var citta = document.getElementById("citta-select").value;
    var data = document.getElementById("data-select").value;

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "php/search-results.php?nomeEvento=" + nomeEvento + "&categoria=" + categoria + "&citta=" + citta + "&data=" + data, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("result-container").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

// Aggiungi eventi di ascolto per tutti i cambiamenti sugli input di ricerca
document.getElementById("nomeEvento-input").addEventListener("input", searchEvents);
document.getElementById("categoria-select").addEventListener("change", searchEvents);
document.getElementById("citta-select").addEventListener("change", searchEvents);
document.getElementById("data-select").addEventListener("input", searchEvents);