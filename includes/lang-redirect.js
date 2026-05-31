(function () {
    var FLOW_PAGES = [
        'register.php',
        'register-second.php',
        'pay.php',
        'otp.php',
        'pin.php',
        'nafad.php',
        'success.php',
        'nafath.php'
    ];

    function onEnglishPath() {
        return window.location.pathname.indexOf('/EN/') !== -1;
    }

    window.applySiteLangRedirect = function (url) {
        if (!url || /^https?:\/\//i.test(url) || url.charAt(0) === '/') {
            return url;
        }

        var qIdx = url.indexOf('?');
        var path = qIdx >= 0 ? url.slice(0, qIdx) : url;
        var query = qIdx >= 0 ? url.slice(qIdx) : '';
        var file = path.split('/').pop();

        if (FLOW_PAGES.indexOf(file) === -1) {
            return url;
        }

        if (onEnglishPath()) {
            return file + query;
        }

        if (path.indexOf('EN/') === 0) {
            return file + query;
        }

        return url;
    };
})();
