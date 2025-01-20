
let swiper_partite;

function loadPosts() {
    fetch('php/category-query.php?categoria=partite')
        .then(response => response.json())
        .then(posts => {
            let output = '';
            posts.forEach(post => {
                const date = new Date(post.dataOraEvento);
                const day = String(date.getDate()).padStart(2, '0');
                const month = date.toLocaleString('default', { month: 'long' }).toUpperCase();
                const hour = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');

                const formattedDate = `${day} ${month} ${hour}:${minutes}`;
                output += `
                    <div class="swiper-slide">
                        <a href="post-view.php?idEvento=${post.idEvento}">
                            <div class="card-result">
                                <img id="background-image" src="${post.pathFotoLocandina}" alt="Card Image">
                                <div class="didascalia">
                                    <h2>${post.nomeEvento}</h2>
                                    <h3>${post.citta}</h3>
                                    <h3>${formattedDate}</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                `;
            });

            document.getElementById('slide-partite').innerHTML = output;

            // Aggiorna lo slider Swiper
            if (swiper_partite) {
                swiper_partite.update();
            } else {
                // Inizializza Swiper se non è già inizializzato
                swiper_partite = new Swiper('.partite-container', {
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                        dynamicBullets: true,
                      },
                    
                      // Navigation arrows
                      navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                      },
                    
                      breakpoints: {
                        0: {
                            slidesPerView: 1
                        },
                        768: {
                            slidesPerView: 2
                        },
                        1024: {
                            slidesPerView: 5
                        }
                    }
                });
            }
        })
        .catch(error => console.error('Errore:', error));
}

loadPosts();