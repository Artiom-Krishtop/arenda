"use strict";

(function( $ ) {

    $.fn.citrusRealtyOfficeMapCheckHash = function(hash) {
        if ('undefined' === typeof(hash))
        {
            var hash = window.location.hash.substr(1);
        }
        if (window.geoObjects) {
            for (var i = 0; i < window.geoObjects.length; ++i) {
                if (window.geoObjects[i]._info.code == hash)
                    window.geoObjects[i].balloon.open();
            }
        }
    }

    $(window).on('hashchange', function () {
        $().citrusRealtyOfficeMapCheckHash();
    });

}( jQuery ));
