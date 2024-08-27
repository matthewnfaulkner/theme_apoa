

define(['jquery'], function($) {


    var registerEventListeners = function(viewInAppElementId) {

        const button = $('#' + viewInAppElementId);

        button.on('click', function(e) {
            e.preventDefault();
            // Always use the original element where the event listener is attached
            const a = $(e.target).closest('button')[0];
            const url = a.getAttribute('openAppUrl');
            window.location.href = url;
        });

    };

    return {
        init: registerEventListeners
    };
});
