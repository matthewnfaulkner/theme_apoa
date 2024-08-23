define(['jquery'], function($) {

    var registerEventListeners = function(viewInAppElementId) {
        console.log(viewInAppElementId);
        const button = $('#' + viewInAppElementId);

        button.on('click', function(e) {
            e.preventDefault();
            var start = new Date().getTime();

            // Always use the original element where the event listener is attached
            const a = $(e.target).closest('button')[0];
            console.log(a);
            const url = a.getAttribute('openAppUrl');
            const alturl = a.getAttribute('openAppUrlAlt');
            console.log(url, alturl);
            window.location.href = url;

            setTimeout(function() {
                var end = new Date().getTime();
                if(end - start < 1500) {
                    window.location.href = alturl;
                }
            });
        });

    };

    return {
        init: registerEventListeners
    };
});
