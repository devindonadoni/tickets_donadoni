$(document).ready(function () {
    let swiper = new Swiper('.result-container', {
        navigation: {
            nextEl: '.result-next',
            prevEl: '.result-prev',
        },
        pagination: {
            el: '.result-pagination',
            clickable: true,
        },
        slidesPerView: 5,
        spaceBetween: 10,
    });

    $("#search-box, #categoria-select, #citta-select, #data-select").on("input change", function () {
        let query = $("#search-box").val().trim();
        let categoria = $("#categoria-select").val();
        let citta = $("#citta-select").val();
        let data = $("#data-select").val();

        // Controlla se sono selezionate le opzioni predefinite
        let noFiltersActive =
            query === "" &&
            (categoria === "Scegli categoria" || categoria === "") &&
            (citta === "Scegli città" || citta === "") &&
            (data === "");

        if (noFiltersActive) {
            $("#result").html("");
            $("#results-section").fadeOut();
            return;
        }

        $.ajax({
            url: "php/search.php",
            method: "POST",
            data: {
                query: query,
                categoria: categoria,
                citta: citta,
                data: data
            },
            dataType: "json", // Specifica che la risposta è JSON
            success: function (data) {
                if (data.length > 0) {

                    data.sort((a, b) => a.soldOut - b.soldOut);
                    let html = "";
                    data.forEach(evento => {

                    const soldOutOverlay = evento.soldOut == 1 || evento.soldOut === "1"
                        ? '<div class="result-gradient-overlay"><h1>SOLD OUT</h1></div>'
                        : '';
                        html += `
                            <div class="swiper-slide">
                                <a href="post-view.php?idEvento=${evento.idEvento}">
                                    <div class="card-result">
                                        ${soldOutOverlay}
                                        <img id="background-image" src="${evento.pathFotoLocandina}" alt="Card Image">
                                        <div class="didascalia">
                                            <h2>${evento.nomeEvento}</h2>
                                            <h3>${evento.citta}</h3>
                                            <h3>${evento.dataOraEvento}</h3>
                                        </div>
                                    </div>
                                </a>
                            </div>`;
                    });

                    $("#result").html(html);
                    $("#results-section").fadeIn();
                    swiper.update(); // Aggiorna Swiper per rilevare i nuovi elementi
                } else {
                    $("#result").html("<p style='display: flex; justify-content: center; align-items: center; height: 100%; width: 100%; text-align: center; font-size: 20px;'>No results found</p>");
                    $("#results-section").fadeOut();
                }
            },
            error: function () {
                console.error("Errore durante la richiesta AJAX.");
            }
        });
    });
});








function removeFilter() {
    // Resetta il campo di ricerca testuale
    document.getElementById("search-box").value = "";

    // Resetta i selettori dropdown
    document.getElementById("categoria-select").value = "";
    document.getElementById("citta-select").value = "";

    // Resetta il campo data
    document.getElementById("data-select").value = "";

    // Nasconde la sezione dei risultati
    document.getElementById("results-section").style.display = "none";

    // Ricarica gli eventi o ripristina la visualizzazione predefinita
    loadEvents();
}