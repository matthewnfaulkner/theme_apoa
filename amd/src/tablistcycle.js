

define(['jquery'], function($) {
    // Configure the interval time (in milliseconds)
    /**
    * Register event listeners for the subscription toggle.
    *
    */
    function startTabCycling(){
        const intervalTime = 20000; // 10 seconds

        let isHovered = false;
        let activeTab = $('#elibrary-tab .nav-link.active');

        const navLinks = $('#elibrary-tab .nav-link');
        const tabPanes = $('#elibrary-tabContent .tab-pane');
        let delayTimeout;

        navLinks.on('click mouseenter', function() {
        clearTimeout(delayTimeout);
        tabPanes.removeClass('active show');
        const targetpaneid = $(this).attr('href');
        const targetpane = $(targetpaneid);

        delayTimeout = setTimeout(() => {
            targetpane.tab('show');
            $(this).tab('show');
            navLinks.removeClass('active');
            $(this).addClass('active');
            targetpane.addClass('active');
        }, 200); // Adjust the delay duration as needed
        });



        // Start cycling through tabs
        const interval = setInterval(tabCycle, intervalTime);

        // Handle hover events
        if (!('ontouchstart' in window || navigator.msMaxTouchPoints)) {
            $('#elibraryTabContainer').hover(
                function() {
                // When hovering over the container, set isHovered to true
                isHovered = true;
                },
                function() {
                // When no longer hovering over the container, set isHovered to false
                isHovered = false;
                }
            );
        }else{
            $('#elibraryTabContainer').on('click',
                function() {
                    isHovered = false;
                    clearInterval(interval); // Reset the interval
                    interval = setInterval(tabCycle, intervalTime);
                }
            );
        }
        /**
 * Register event listeners for the subscription toggle.
 *
 */
        function tabCycle(){
            if (!isHovered) {
                const nextTab = activeTab.next('a').length ? activeTab.next('a') : $('#elibrary-tab').find(':first');
                activeTab.removeClass('active');
                nextTab.tab('show');
                activeTab = nextTab;
            }
        }
    }
    return {
        init : startTabCycling
    };
});