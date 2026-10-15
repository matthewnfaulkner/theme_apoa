

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
            showTab($(this));
            navLinks.removeClass('active');
            $(this).addClass('active');
            targetpane.addClass('active');
        }, 200); // Adjust the delay duration as needed
        });



        // Start cycling through tabs
        let interval = setInterval(tabCycle, intervalTime);

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
         * Show a tab and its pane (Bootstrap 5 has no jQuery $(...).tab()).
         *
         * @param {jQuery} link The tab's nav link.
         */
        function showTab(link) {
            const pane = $(link.attr('href'));
            navLinks.removeClass('active').attr('aria-selected', 'false');
            link.addClass('active').attr('aria-selected', 'true');
            tabPanes.removeClass('active show');
            pane.addClass('active show');
        }

        /**
         * Move to the next tab unless the user is hovering over the tabs.
         */
        function tabCycle(){
            if (!isHovered) {
                const nextTab = activeTab.next('a').length ? activeTab.next('a') : $('#elibrary-tab').find(':first');
                activeTab.removeClass('active');
                showTab(nextTab);
                activeTab = nextTab;
            }
        }
    }
    return {
        init : startTabCycling
    };
});