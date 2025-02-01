$(document).ready(function () {
    const apiUrl = 'php/biglietti-query.php';

    function loadPrenotazioni(statoPrenotazione = null, ordine = 'asc', filtro = 'nomeEvento') {
        $.ajax({
            url: apiUrl,
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                // Filtra le prenotazioni in base allo stato
                let filteredData = statoPrenotazione
                    ? data.filter(prenotazione => prenotazione.statoPrenotazione === statoPrenotazione)
                    : data;

                // Ordina i dati in base al filtro selezionato
                filteredData.sort((a, b) => {
                    if (ordine === 'asc') {
                        return a[filtro].localeCompare(b[filtro]);
                    } else {
                        return b[filtro].localeCompare(a[filtro]);
                    }
                });

                let html = filteredData.length === 0
                    ? `<div class="no-booking">
                            <p>Nessuna prenotazione disponibile.</p>
                            <a href="shop.html" class="shop-link">Torna allo shopping</a>
                    </div>`
                                    : filteredData.map(prenotazione => `
                        <div class="event-info">
                            <h1>${prenotazione.nomeEvento}</h1>
                            <p>${prenotazione.citta} | ${prenotazione.locazione}</p>
                            <p>${prenotazione.dataOraEvento}</p>
                            <p>${prenotazione.nomeSettore} - ${prenotazione.numeroPosto ? prenotazione.numeroPosto : 'Posto unico'}</p>
                            <div class="total">
                                <p style="color: white; background-color: ${getColor(prenotazione.statoPrenotazione)};">
                                    €${prenotazione.prezzo}, ${prenotazione.statoPrenotazione}
                                </p>
                                <i class="fa-solid fa-download"></i>
                            </div>
                        </div>
                    `).join('');


                // Inserisce i dati nel tab corretto
                switch (statoPrenotazione) {
                    case null:
                        $('#item1 .container-event').html(html);
                        break;
                    case 'confermata':
                        $('#item2 .container-event').html(html);
                        break;
                    case 'cancellata':
                        $('#item3 .container-event').html(html);
                        break;
                    case 'in elaborazione':
                        $('#item4 .container-event').html(html);
                        break;
                }
            },
            error: function (xhr, status, error) {
                console.error("Errore nel caricamento dati:", error);
            }
        });
    }

    // Carica prenotazioni iniziali per tutti i tab
    loadPrenotazioni(null); // Tutte
    loadPrenotazioni('confermata'); // Pagate
    loadPrenotazioni('cancellata'); // Cancellate
    loadPrenotazioni('in elaborazione'); // Scadute

    function getColor(statoPrenotazione) {
        switch (statoPrenotazione) {
            case 'confermata': return 'green';
            case 'cancellata': return 'red';
            case 'in elaborazione': return 'orange';
            default: return 'gray';
        }
    }

    // 🔥 2️⃣ Aggiunta della funzionalità di filtro dinamico per tutti i tab
    let filtriStato = {
        'evento': { campo: 'nomeEvento', ordine: 'asc' },
        'luogo': { campo: 'citta', ordine: 'asc' },
        'data': { campo: 'dataOraEvento', ordine: 'asc' },
        'posto': { campo: 'numeroPosto', ordine: 'asc' },
        'prezzo': { campo: 'prezzo', ordine: 'asc' } 
    };
    

    $(".single-filter").click(function () {
        let filtroSelezionato = $(this).text().trim().toLowerCase(); // "Evento" → "evento"
        let filtro = filtriStato[filtroSelezionato];

        if (filtro) {
            // Invertiamo l'ordine ad ogni click
            filtro.ordine = filtro.ordine === 'asc' ? 'desc' : 'asc';

            // Troviamo il tab attivo
            let tabAttivo = $(".tab-item a.active").attr("href").replace("#", "");

            // Carichiamo le prenotazioni ordinate per il tab attivo
            switch (tabAttivo) {
                case "item1":
                    loadPrenotazioni(null, filtro.ordine, filtro.campo);
                    break;
                case "item2":
                    loadPrenotazioni("confermata", filtro.ordine, filtro.campo);
                    break;
                case "item3":
                    loadPrenotazioni("cancellata", filtro.ordine, filtro.campo);
                    break;
                case "item4":
                    loadPrenotazioni("in elaborazione", filtro.ordine, filtro.campo);
                    break;
            }
        }
    });

});
