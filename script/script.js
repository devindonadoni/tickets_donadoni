const swiper1 = new Swiper('.image-container', {
  loop: true,
  spaceBetween: 40,
  initialSlide: 0,
  speed: 1000,

  // Pagination
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

  on: {
    init: function () {
      console.log('Swiper inizializzato correttamente!');
    },
  },
});

setInterval(() => {
  swiper1.slideNext(); // Passa alla slide successiva ogni 5 secondi
}, 5000);


const commonSwiperOptions = {
  loop: true,
  spaceBetween: 10,
  initialSlide: 0,
  slidesPerView: 5,
  speed: 1000,

  // Pagination
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
};

// Inizializzazione swiper per entrambi i contenitori
new Swiper('.result-container', commonSwiperOptions);
new Swiper('.concerti-container', commonSwiperOptions);
new Swiper('.teatro-container', commonSwiperOptions);
new Swiper('.tour-container', commonSwiperOptions);
new Swiper('.partite-container', commonSwiperOptions);
