
define(['jquery', 'theme_apoa/swiper-bundle'], function($, Swiper) {

  var registereventlisteners = function() {

      const menuItems = document.querySelectorAll('.sidejumbo-link');
      //const closemodal = document.getElementById('closemodal');
      var isClicked = {};
      //var istouchevent = false;
      var mySwiper = new Swiper('.swiper-container', {
         autoplay: {
          delay: 4000,
          disableOnInteraction: false
        },
        effect: "creative",
        creativeEffect: {
        prev: {
          shadow: true,
          opacity: 1, // fade out previous
          translate: ["-20%", 0, -1],
        },
        next: {
          opacity:0, // fade out previous
          translate: ["100%", 0, 0],
        },
      },
        pauseOnMouseEnter: true,
        loop: true,
        speed: 800,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
          renderBullet: function (index, className) {
            return '<span class="' + className + '">' +  "</span>";
          },
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
          on: {
        slideChange: function () {
          // Get the real index (0-based)
          const index = this.realIndex;

          // Remove 'active' from all pagination elements
          document.querySelectorAll("[id^='pagination-']").forEach(el => {
            el.classList.remove("active");
          });

          // Find the one that matches current slide and add 'active'
          const activeEl = document.getElementById(`pagination-${index}`);
          if (activeEl) {
            activeEl.classList.add("active");
          }
        },
      },
      });

  document.querySelectorAll("[id^='pagination-']").forEach(el => {
            el.addEventListener('mouseover', function() {
                const id = el.id.split('-');
                mySwiper.slideToLoop(id[1]);
                mySwiper.autoplay.stop(); // stop autoplay
                setTimeout(() => {
                  mySwiper.autoplay.start(); // restart autoplay
                }, 5000);
            });
          });

  mySwiper.on('slideChange', function () {
    isClicked ={};
    isClicked[mySwiper.realIndex] = true;
  });

  menuItems.forEach(menuItem => {
      menuItem.addEventListener('click', function(e) {
          window.location.href = e.currentTarget.getAttribute("data-link-address");
      });
  });
};

  return {
    init: registereventlisteners
  };
});
