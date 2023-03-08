
;(function(){
    window.BigDataMap = function (params) {
        "use strict";

        var self = this;

        self.items = [];
        self.page = 1;
        self.pageCount = +params.pageCount;
        self.$map = $('#'+params.mapId);
        self.ajaxLoadCounter = 0;
        self.loaded = false;

        self.hideMap = function () {
            self.$map.height(0);
        };
        self.loadMap = function(){
            if (!self.items.length) {self.hideMap(); return;}
            self.$map.on("inView", function () {
                if (!self.loaded) {
                    self.loaded = true;
                    $().citrusObjectsMap({
                        id: params.mapId,
                        items: self.items
                    });
                }
            });
            new inView(self.$map, {
                'once': true
            });

            self.$map.on('changeVisible', function (event, isVisible) {
                if (isVisible && !self.loaded) {
                    self.loaded = true;
                    $().citrusObjectsMap({
                        id: params.mapId,
                        items: self.items
                    });
                }
            });
        };

        self.getItems = function (page) {
            self.ajaxLoadCounter++;
            $.ajax({
                url: params.ajaxPath,
                type: 'Get',
                dataType: 'json',
                data: {
                    PAGE: page,
                    arParams: params.arParams,
                    //clear_cache: 'Y',
                    ajax: 1,
                },
            })
                .done(function(data) {
                    if (!data) console.warn('error load map data');
                    if ($.isArray(data) && data.length) self.items = self.items.concat(data);
                    if (!--self.ajaxLoadCounter) self.loadMap();
                })
                .fail(function() {
                    console.log("ajax error");
                    if (!--self.ajaxLoadCounter) self.loadMap();
                });
        };

        if (self.pageCount) {
            //wait page
            setTimeout(function () {
                for (; self.page <= self.pageCount; self.page++) {
                    self.getItems(self.page);
                }
            }, 100)
        } else {
            self.hideMap();
        }
    };
}());