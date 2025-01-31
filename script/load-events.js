let swiperInstances = {};

function loadPosts(categoria, containerClass, slideId) {
    fetch(`php/category-query.php?categoria=${categoria}`)
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

                const soldOutOverlay = post.soldOut == 1 || post.soldOut === "1"
                    ? '<div class="result-gradient-overlay"><h1>SOLD OUT</h1></div>'
                    : '';

                // console.log("Post ricevuto:", post); // Controlla l'intero oggetto
                // console.log("Valore di soldOut:", post.soldOut);


                output += `
                    <div class="swiper-slide">
                        <a href="post-view.php?idEvento=${post.idEvento}">
                            <div class="card-result">
                                ${soldOutOverlay}
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

            document.getElementById(slideId).innerHTML = output;

            if (swiperInstances[categoria]) {
                swiperInstances[categoria].update();
            } else {
                swiperInstances[categoria] = new Swiper(`.${containerClass}`, {
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                        dynamicBullets: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: 1,
                        },
                        768: {
                            slidesPerView: 2,
                        },
                        1024: {
                            slidesPerView: 5,
                        },
                    },
                });
            }
        })
        .catch(error => console.error(`Errore nel caricamento della categoria ${categoria}:`, error));
}

document.addEventListener('DOMContentLoaded', () => {
    const categories = [
        { categoria: 'partite', containerClass: 'partite-container', slideId: 'slide-partite' },
        { categoria: 'concerto', containerClass: 'concerto-container', slideId: 'slide-concerto' },
        { categoria: 'tour', containerClass: 'tour-container', slideId: 'slide-tour' },
        { categoria: 'teatro', containerClass: 'teatro-container', slideId: 'slide-teatro' },
    ];

    categories.forEach(({ categoria, containerClass, slideId }) => {
        loadPosts(categoria, containerClass, slideId);
    });
});
