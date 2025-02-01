function rindirizza(pagina) {
    window.location.href = pagina + '.php';
}



function logout() {
    Swal.fire({
        title: 'Sei sicuro?',
        text: `Effettuarai il logout.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sì!',
        cancelButtonText: 'Annulla',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'logout.php';
        } else {
            Swal.fire(
                'Errore',
                'ah ah ah NON PUOI ANDARTENE COSI FACILMENTE',
                'error'
            );
        }
    });
}