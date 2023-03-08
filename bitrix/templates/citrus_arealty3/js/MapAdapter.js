/*
 * Map adapter v1.0
 *
 * @author ИП Дмитрий Игоревич Бреский https://github.com/dimabresky
 */

(function (root, factory) {

    'use strict';

    if (typeof define === 'function' && define.amd) {

        define([], function () {
            return (root.MapAdapter = factory());
        });

    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        root.MapAdapter = factory();
    }
})(this, function () {

    'use strict';

    var MapAdapter = function (options) {

        var repository = "https://github.com/dimabresky/MapAdapter";

        var map = null;

        /**
         * @type Array
         */
        var markers = [];

        /**
         * @type Array
         */
        var errors = [];

        /**
         * @type Array
         */
        var allowMapObjects = ["ymaps", "gmaps"];

        /**
         * @type Object
         */
        var defOptions = {

            center: {
                lat: 0,
                lng: 0
            },

            object: "ymaps",

            api_key: "",

            zoom: 5,

            map_id: "",

            lang: "ru"
        };

        /**
         * @type Object
         */
        var worker = null;

        /**
         * @type Object
         */
        var methods = {
            ymaps: {
                loadScript: function () {
                    //__loadScript(`https://api-maps.yandex.ru/2.1/?lang=${defOptions.lang}_${defOptions.lang.toUpperCase()}`);
                },
                __init: function () {
                    ymaps.ready(function () {

                        map = new ymaps.Map(defOptions.map_id, {
                            center: [defOptions.center.lat, defOptions.center.lng],
                            zoom: defOptions.zoom,
                            behaviors: ["scrollZoom", "drag", "multiTouch"],
                            controls: ["zoomControl"]
                        });

                    });
                },
                init: function () {
                    var interval_id;
                    if (typeof ymaps === "undefined") {
                        interval_id = setInterval(function () {
                            if (typeof ymaps !== "undefined") {
                                methods.ymaps.__init();
                                clearInterval(interval_id);
                            }
                        }, 200);
                    } else {
                        methods.ymaps.__init();
                    }
                },
                /**
                 * @param {Array} route
                 * [
                 *      {
                 *          lat: // latitude (required),
                 *          lng: // longitude (required),
                 *          icon: // marker image src (not required),
                 *          title: // title of marker (required),
                 *          content: // content of balloon
                 *      },
                 *      ... another points
                 * ]
                 * @returns {undefined}
                 */
                drawRoute: function (route) {
                    __apply2Map(function () {

                        try {

                            if (!Array.isArray(route) || !route.length) {
                                throw new Error(`Route must be not empty Array of points (see ${repository})`);
                            }

                            ymaps.route(route.map(function (element) {
                                return [element.lat, element.lng];
                            }), {mapStateAutoApply: true})
                                    .then(function (Route) {
                                        var points, point, i;
                                        map.geoObjects.add(Route);
                                        Route.getPaths().options.set('strokeColor', "#337ab7");
                                        points = Route.getWayPoints();
                                        points.options.set('preset', 'islands#darkBlueStretchyIcon');
                                        for (i = 0; i < route.length; i = i + 1) {
                                            point = points.get(i);

                                            if (typeof route[i].icon === "string" && route[i].icon !== "") {
                                                point.options.set("iconLayout", "default#image");
                                                point.options.set("iconImageHref", route[i].icon);
                                                points.options.set('preset', '');
                                            }
                                            if (typeof route[i].title === "string" && route[i].title !== "") {
                                                point.properties.set("iconContent", route[i].title);
                                            }
                                            if (typeof route[i].content === "string" && route[i].content !== "") {
                                                point.properties.set("balloonContent", route[i].content);
                                            } else {
                                                point.options.set("hasBalloon", false);
                                            }
                                        }
                                    })
                                    .catch(function (error) {
                                        console.error(error);
                                    });

                        } catch (error) {
                            console.error(error.message);
                        }

                    });
                },
                /**
                 * @param {Object} marker
                 *      {
                 *          lat: // latitude (required),
                 *          lng: // longitude (required),
                 *          icon: // marker image src (not required),
                 *          title: // title of marker (required),
                 *          content: // content of balloon
                 *      }
                 * @returns {undefined}
                 */
                addMarker: function (marker) {
                    __apply2Map(function () {
                        var placemark_properties = {};
                        var placemark_options = {
                            preset: "islands#darkBlueStretchyIcon"
                        };

                        if (typeof marker.icon === "string" && marker.icon !== "") {
                            placemark_options.iconLayout = "default#image";
                            placemark_options.iconImageHref = marker.icon;
                            placemark_options.preset = "";
                        } else if (typeof marker.title === "string" && marker.title !== "") {
                            placemark_properties.iconContent = marker.title;
                        }
                        if (typeof marker.content === "string" && marker.content !== "") {
                            placemark_properties.balloonContentBody = marker.content;
                        } else {
                            placemark_options.hasBalloon = false;
                        }

                        map.geoObjects.add(new ymaps.Placemark([marker.lat, marker.lng], placemark_properties, placemark_options));
                        map.setCenter([marker.lat, marker.lng]);
                    });
                },
                /**
                 * @param {Number} index
                 * @returns {undefined}
                 */
                removeMarker: function (index) {
                    __apply2Map(function () {
                        // @todo
                    });
                },
                clear: function () {
                    __apply2Map(function () {
                        // @todo
                    });
                }
            },
            gmaps: {
                loadScript: function () {

                    if (typeof google.maps === "undefined") {

                        __loadScript(`https://maps.googleapis.com/maps/api/js?key=${defOptions.api_key}&libraries=geometry,places`);
                    }
                },
                __init: function () {
                    google.maps.event.addListenerOnce(map, 'idle', function () {

                        map = new google.maps.Map(document.getElementById(defOptions.map_id), {
                            center: [defOptions.center.lat, defOptions.center.lng],
                            zoom: defOptions.zoom
                        });

                    });
                },
                init: function () {

                    var interval_id = null;

                    if (typeof google.maps === "undefined") {

                        interval_id = setInterval(function () {
                            if (typeof google !== "undefined") {
                                methods.gmaps.__init();
                                clearInterval(interval_id);
                            }
                        }, 200);

                    } else {
                        methods.gmaps.__init();
                    }

                },
                /**
                 * @param {Array} route
                 * @returns {undefined}
                 */
                drawRoute: function (route) {
                    __apply2Map(function () {
                        // @todo
                    });
                },
                /**
                 * @param {Object} marker
                 * @returns {undefined}
                 */
                addMarker: function (marker) {
                    __apply2Map(function () {
                        // @todo
                    });
                },
                /**
                 * @param {Number} index
                 * @returns {undefined}
                 */
                removeMarker: function (index) {
                    __apply2Map(function () {
                        // @todo
                    });
                },
                clear: function () {
                    __apply2Map(function () {
                        // @todo
                    });
                }
            }
        };

        function __apply2Map(callback) {

            var interval_id = null;
            if (map !== null) {
                callback();
            } else {
                interval_id = setInterval(function () {

                    if (map !== null) {
                        clearInterval(interval_id);
                        callback();
                    }

                }, 200);
            }

        }

        /**
         * @param {String} src
         * @returns {undefined}
         */
        function __loadScript(src) {
            var head = document.getElementsByTagName("head").item(0);
            var script = document.createElement("script");
            script.setAttribute("type", "text/javascript");
            script.setAttribute("src", src);
            head.appendChild(script);
        }

        function __assignObjects() {

            var result = {};
            var len = arguments.length;
            var p;
            for (var i = 0; i < len; i++) {
                for (p in arguments[i]) {
                    if (arguments[i].hasOwnProperty(p)) {
                        result[p] = arguments[i][p];
                    }
                }
            }
            return result;
        }

        /**
         * @param {Array} route
         * @returns {undefined}
         */
        this.drawRoute = function (route) {
            worker.drawRoute(route);
        };

        /**
         * @param {Object} marker
         * @returns {undefined}
         */
        this.addMarker = function (marker) {
            worker.addMarker(marker);
        };

        /**
         * @param {Number} index
         * @returns {undefined}
         */
        this.removeMarker = function (index) {
            worker.removeMarker(index);
        };

        this.clear = function () {
            worker.clear();
        };

        defOptions = __assignObjects(defOptions, options || {});

        // check required options
        if (typeof defOptions.map_id !== "string" || defOptions.map_id === "") {
            errors.push("Map id must be set.");
        }

        if (allowMapObjects.indexOf(defOptions.object) === -1) {
            errors.push("Object must be " + allowMapObjects.join(" or "));
        }

        if (errors.length) {
            errors.forEach(function (error) {
                console.error(error);
            });
        } else {
            worker = methods[defOptions.object];

            worker.loadScript();
            worker.init();
        }
    };

    return MapAdapter;
});


