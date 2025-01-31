$(document).ready(function () {
    console.log("Document ready!");
    caricaCarrello();
});

function caricaCarrello() {
    $.ajax({
        url: 'api/prenotazioni/cart-query.php',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            $("#carrello-container").empty();

            if (data.length === 0) {
                // Se l'array è vuoto, mostra il messaggio
                $("#carrello-container").html("<div class='no-items-container'><p>Nessun articolo disponibile nel carrello. Torna allo shop</p></div>");
                $("#checkout-container").css("display", "none");
                return;
            }

            let totale = 0;
            let commissioni = 0;

            data.forEach(function (item) {
                const itemHTML = `
                    <div class="item-container" id="item-${item.idPrenotazione}">
                        <div class="container-img">
                            <img src="${item.pathFotoLocandina}" alt="Locandina">
                            <div class="concert-info">
                                <h1>${item.nomeEvento}</h1>
                                <p>${item.citta}</p>
                                <p>${item.dataOraEvento}</p>
                            </div>
                        </div>
                        <div class="timer-container">
                            <p id="timer-${item.idPrenotazione}">Caricando...</p>
                        </div>
                        <div class="posto-container">
                            <p>${item.nomeSettore}</p>
                        </div>
                        <div class="price-container">
                            <p>€${item.prezzo}</p>
                            <i class="fa-solid fa-trash-can" onclick="rimuoviElemento(${item.idPrenotazione})"></i>
                        </div>
                    </div>
                `;
                $("#carrello-container").append(itemHTML);

                totale += parseFloat(item.prezzo);
                commissioni += parseFloat(item.prezzo) * 0.1;

                const dataAggiunta = new Date(item.dataAggiunta);
                const oraCorrente = new Date();
                let tempoRimanente = dataAggiunta.getTime() + (24 * 60 * 60 * 1000) - oraCorrente.getTime();

                (function startTimer(idPrenotazione, tempoRimanente) {
                    function aggiornaTimer() {
                        if (tempoRimanente <= 0) {
                            clearInterval(timerInterval);
                            $("#timer-" + idPrenotazione).text("EXPIRED");
                            Swal.fire({
                                title: "Prenotazione Scaduta!",
                                text: "La tua prenotazione è scaduta e verrà rimossa.",
                                icon: "warning",
                                confirmButtonText: "OK"
                            }).then(() => {
                                rimuoviElementoExpired(idPrenotazione);
                            });
                        } else {
                            const ore = Math.floor(tempoRimanente / (1000 * 60 * 60));
                            const minuti = Math.floor((tempoRimanente % (1000 * 60 * 60)) / (1000 * 60));
                            const secondi = Math.floor((tempoRimanente % (1000 * 60)) / 1000);
                            $("#timer-" + item.idPrenotazione).text(`${ore}h ${minuti}m ${secondi}s`);
                        }

                        if (tempoRimanente <= 60 * 60 * 1000) {  // 60 minuti * 60 secondi * 1000 millisecondi
                            $("#timer-" + idPrenotazione).css("background-color", "red");
                        } else {
                            $("#timer-" + idPrenotazione).css("background-color", ""); // Ripristina lo sfondo
                        }
                        tempoRimanente -= 1000;
                    }

                    const timerInterval = setInterval(aggiornaTimer, 1000);
                    aggiornaTimer();
                })(item.idPrenotazione, tempoRimanente);
            });

            $("#totale").text(`€${totale.toFixed(2)}`);
            $("#commissioni").text(`€${commissioni.toFixed(2)}`);
            $("#grantotale").text(`€${(totale + commissioni).toFixed(2)}`);
        },
        error: function (xhr, status, error) {
            console.log("Errore nel caricare i dati del carrello", xhr.responseText);
        }
    });
}

function rimuoviElementoExpired(idPrenotazione) {
    $.ajax({
        url: 'api/prenotazioni/rimuoviPrenotazioni.php',
        method: "POST",
        contentType: 'application/json',
        data: JSON.stringify({ idPrenotazione: idPrenotazione }),
        success: function (response) {
            console.log(response);
            if (response.success) {
                // Rimuove l'elemento dal carrello visibile
                $(`#item-${idPrenotazione}`).remove();

                // Ricarica i totali
                let totale = 0;
                let commissioni = 0;

                $(".item-container").each(function () {
                    let prezzo = parseFloat($(this).find('.price-container p').text().replace("€", ""));
                    totale += prezzo;
                    commissioni += prezzo * 0.1;
                });

                $("#totale").text(`€${totale.toFixed(2)}`);
                $("#commissioni").text(`€${commissioni.toFixed(2)}`);
                $("#grantotale").text(`€${(totale + commissioni).toFixed(2)}`);

                if ($(".item-container").length === 0) {
                    $("#carrello-container").html("<div class='no-items-container'><p>Nessun articolo disponibile nel carrello. Torna allo shop</p></div>");
                    $("#checkout-container").css("display", "none");
                }

                console.log(`Prenotazione con ID ${idPrenotazione} eliminata automaticamente dopo la scadenza del timer.`);
            } else {
                console.log(`Errore: impossibile eliminare la prenotazione con ID ${idPrenotazione}.`);
            }
        },
        error: function (xhr, status, error) {
            console.log("Errore nella richiesta AJAX", xhr.responseText);
        }
    });
}


// Funzione per rimuovere un elemento dal carrello con SweetAlert2
function rimuoviElemento(idPrenotazione) {
    const tempoRimanente = $(`#timer-${idPrenotazione}`).text(); // Ottieni il tempo rimanente dal timer

    Swal.fire({
        title: 'Sei sicuro?',
        text: `Hai ancora ${tempoRimanente} per decidere. Questa azione rimuoverà la prenotazione dal carrello.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sì, elimina!',
        cancelButtonText: 'Annulla',
    }).then((result) => {
        if (result.isConfirmed) {
            console.log("idPrenotazione: " + idPrenotazione);
            $.ajax({
                url: 'api/prenotazioni/rimuoviPrenotazioni.php',
                method: "POST",
                contentType: 'application/json',
                data: JSON.stringify({ idPrenotazione: idPrenotazione }),
                success: function (response) {
                    console.log(response);
                    if (response.success) {
                        // Rimuove l'elemento dal carrello visibile
                        $(`#item-${idPrenotazione}`).remove();

                        // Ricarica i totali
                        let totale = 0;
                        let commissioni = 0;

                        $(".item-container").each(function () {
                            let prezzo = parseFloat($(this).find('.price-container p').text().replace("€", ""));
                            totale += prezzo;
                            commissioni += prezzo * 0.1;
                        });

                        $("#totale").text(`€${totale.toFixed(2)}`);
                        $("#commissioni").text(`€${commissioni.toFixed(2)}`);
                        $("#grantotale").text(`€${(totale + commissioni).toFixed(2)}`);

                        if ($(".item-container").length === 0) {
                            $("#carrello-container").html("<div class='no-items-container'><p>Nessun articolo disponibile nel carrello. Torna allo shop</p></div>");
                            $("#checkout-container").css("display", "none");
                        }

                        Swal.fire(
                            'Eliminato!',
                            'La prenotazione è stata rimossa dal carrello.',
                            'success'
                        );
                    } else {
                        Swal.fire(
                            'Errore',
                            'Non è stato possibile eliminare la prenotazione.',
                            'error'
                        );
                    }
                },
                error: function (xhr, status, error) {
                    console.log("Errore nella richiesta AJAX", xhr.responseText);
                    Swal.fire(
                        'Errore',
                        'Si è verificato un errore durante l\'eliminazione.',
                        'error'
                    );
                }
            });
        }
    });
}
