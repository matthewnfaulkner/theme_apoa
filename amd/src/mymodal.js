
define(['jquery', 'theme_apoa/swiper'], function($, Swiper) {

  var registereventlisteners = function (){
      const jumbo = document.getElementById('jumbo');
  const subjumbo = document.getElementById('jumbomodal');
  const menuItems = document.querySelectorAll('.sidejumboitemcontainer');
  const swiperpages = document.querySelectorAll('.swiper-slide');
  const closemodal = document.getElementById('closemodal');
  var isClicked = {};
  var istouchevent = false;
  var mySwiper = new Swiper('.swiper-container', {
    loop: true,
    pagination: {
      div: '.swiper-pagination',
      clickable: true,
    },
    preventClicks: true
  });

  $('#pagination-0').hover(function() {
    mySwiper.slideToLoop(0); // Go to the first slide (index 0)
  });

  $('#pagination-1').hover(function() {
    mySwiper.slideToLoop(1); // Go to the second slide (index 1)
  });

  $('#pagination-2').hover(function() {
    mySwiper.slideToLoop(2); // Go to the third slide (index 2)
  });

  mySwiper.on('slideChange', function () {
    isClicked ={};
    isClicked[mySwiper.realIndex] = true;
  });


$('.sidejumbo-link').on('touchstart', function() {
  istouchevent = true;
});

$('.sidejumbo-link').on('touchend', function() {
  istouchevent = false;
  var linkId = $(this).data('link-id');
  var linkAddress = $(this).data('link-address');
  istouchevent = true;
  if (isClicked[linkId] == true) {
    // Link is not "readied" yet, prevent the default behavior
    window.location.href = linkAddress;
  }
});

$('.sidejumbo-link').on('click', function() {
  var linkAddress = $(this).data('link-address');

    // Link is not "readied" yet, prevent the default behavior
    if(!istouchevent){
      window.location.href = linkAddress;
    }
});


  /**
     * Register event listeners for the subscription toggle.
     */
  function closemodalfunc() {
    subjumbo.addEventListener('animationend', handleAnimationEnd);
    subjumbo.classList.remove('drag');
    subjumbo.classList.add('hiding');
    subjumbo.classList.remove('show');
    isClicked = {};

    /**
     * Register event listeners for the subscription toggle.
     */
    function handleAnimationEnd() {
      subjumbo.classList.replace('hiding', 'hide');
      subjumbo.removeEventListener('animationend', handleAnimationEnd);
    }
  }
  if(closemodal !== null){
    closemodal.addEventListener('touchstart', function(event) {
      closemodalfunc();
      event.preventDefault();
    });

    closemodal.addEventListener('click', function() {
      closemodalfunc();
    });
  }
  var mouseovermenu = false;
  var mouseoversub = false;
  var delayTimer;

  swiperpages.forEach(swiperpage => {
    var swiperimg = swiperpage.querySelector('img');
    var swiperid = swiperpage.getAttribute('data-control-id');
    var query = '[data-link-id="' + swiperid + '"]';
    var swipercontrol = document.querySelector(query);
    if(swipercontrol !== null){
      swiperimg.src = swipercontrol.getAttribute('data-modalimg');
    }
  });

  menuItems.forEach(menuItem => {
    menuItem.addEventListener('touchend', function() {
      subjumbo.addEventListener('animationend', handleAnimationEnd);
      subjumbo.classList.replace('hide', 'showing');

      /**
     * Register event listeners for the subscription toggle.
     */
      function handleAnimationEnd() {
      subjumbo.classList.replace('showing', 'show');
      subjumbo.removeEventListener('animationend', handleAnimationEnd);
      }

      mouseovermenu = true;
    });
    menuItem.addEventListener('mouseover', function() {
      subjumbo.addEventListener('animationend', handleAnimationEnd);
      subjumbo.classList.replace('hide', 'showing');
      startDelayTimerOver();
        /**
       * Register event listeners for the subscription toggle.
       */
      function handleAnimationEnd() {

        subjumbo.classList.replace('showing', 'show');
        subjumbo.removeEventListener('animationend', handleAnimationEnd);
      }

      mouseovermenu = true;
    });

    menuItem.addEventListener('mouseout', function() {

      mouseovermenu = false;
      startDelayTimer();
    });
  });

  jumbo.addEventListener('mouseout', function() {

    mouseoversub = false;
    startDelayTimer();
  });

  jumbo.addEventListener('mouseover', function() {

    mouseoversub = true;
  });
  /**
     * Register event listeners for the subscription toggle.
     */
  function handleAnimationEnd() {

    subjumbo.classList.replace('hiding', 'hide');
    isClicked = {};
    subjumbo.removeEventListener('animationend', handleAnimationEnd);
  }
  /**
     * Register event listeners for the subscription toggle.
     */
  function startDelayTimerOver() {
    clearTimeout(delayTimer);
    delayTimer = setTimeout(function() {
      if (!mouseoversub && !mouseovermenu) {
        subjumbo.addEventListener('animationend', handleAnimationEnd);
        subjumbo.classList.replace('hiding', 'show');

        handleAnimationEnd();
      }

    }, 200); // Adjust the delay time in milliseconds (e.g., 500ms)
  }
  /**
     * Register event listeners for the subscription toggle.
     */
  function startDelayTimer() {
    clearTimeout(delayTimer);
    delayTimer = setTimeout(function() {
      if (!mouseoversub && !mouseovermenu) {
        subjumbo.addEventListener('animationend', handleAnimationEnd);
        subjumbo.classList.replace('show', 'hiding');

        handleAnimationEnd();
      }

    }, 200); // Adjust the delay time in milliseconds (e.g., 500ms)
  }

  let jumboHeight = jumbo.clientHeight;
  let startTop = 0;
  let startY = 0;
  let momentum = 0;

  subjumbo.addEventListener('touchstart', handleTouchStart);
  subjumbo.addEventListener('touchmove', handleTouchMove);
  subjumbo.addEventListener('touchend', handleTouchEnd);
  /**
     * Register event listeners for the subscription toggle.
     * @param {event} event
     */
  function handleTouchStart(event) {
    subjumbo.classList.replace('show', 'drag');
    startY = event.touches[0].clientY;

    momentum = 0;
  }
  /**
     * Register event listeners for the subscription toggle.
     * @param {event} event
     */
  function handleTouchMove(event) {
    event.preventDefault();
    const currentY = event.touches[0].clientY;
    const deltaY = currentY - startY;
    const percent = Math.floor((deltaY / jumboHeight) * 100);

    if (percent > 0) {
      subjumbo.style.top = Math.min(100, percent) + '%';
    }

    momentum = deltaY;
  }
  /**
     * Register event listeners for the subscription toggle.
     */
  function handleTouchEnd() {
    if (!subjumbo.classList.contains('drag')) {
      return;
    }

    subjumbo.style.transitionDuration = '0.5s';
    subjumbo.style.top = Math.min(startTop, 0) + '%';

    if (parseFloat(subjumbo.style.top) > 60 || momentum > 250) {
      subjumbo.classList.replace('drag', 'hide');
      isClicked = {};
    } else {
      subjumbo.classList.replace('drag', 'show');
    }

    setTimeout(() => {
      startY = 0;
      momentum = 0;
    }, 100);
  }
  };
  return {
    init: registereventlisteners
  };
});
