// nook determine in template.php
var rootFolder = "/bitrix/components/citrus/realty.favourites/templates/block/";

var popupOnOpen = function() {
    $('.closebutton').click(function () {
        $.fancybox.close();
    });
    $('button[data-url]').click(function () {
        window.location = $(this).data('url');
    });
};

$(function () {
    window.citrusRealtyMark = function ($element, type) {
		if (type == 'add')
		{
			var msgIdx = 'CITRUS_REALTY_GO_2FAV';
	        if ($element.parents('.favorites_page').length)
	        	msgIdx = 'CITRUS_REALTY_FAV_REMOVE_TITLE';
			$element.addClass('added').find('.control-link-label').html(BX.message(msgIdx)).attr('title', BX.message(msgIdx));
		}
		else
		{
			var msgIdx = 'CITRUS_REALTY_GO_2FAV';
	        if ($element.parents('.favorites_page').length)
	        	msgIdx = 'CITRUS_REALTY_2FAV';
			$element.removeClass('added').find('.control-link-label').html(BX.message(msgIdx)).removeAttr('title');
		}
    }
    $('.add2favourites[data-id]').on("click", function (e) {
        e.preventDefault();
        if($(this).hasClass('added') && !$(this).parents('.favorites_page').length) {
            window.location.href = $("a.realty-favourites").attr("href");
            return;
        }

        var $this = $(this),
            id = $this.data('id'),
            type = $this.hasClass('added') ? 'remove' : 'add';

        if (id <= 0)
            return;

        $.getJSON(
            "/ajax/favourites.php",
            {
                type: type,
                id: id
            },
            function (data) {
                if (typeof(data) !== 'object')
                    return;
                if (typeof(data.error) !== 'undefined') {
                    alert(data.error);
                }
                else {
	                if ( typeof updateFavoriteCount !== 'undefined') updateFavoriteCount(data.count);
                    window.citrusRealtyMark($this, data.type);
                    if (typeof(data.popup) !== 'undefined') {
                        $.fancybox.open(data.popup, {
                            autoSize: false,
                            fitToView: false,
                            scrolling: 'no',
                            closeBtn: false,
                            width: 750,
                            minHeight: 666,
                            margin: 0,
                            padding: 0,
                            afterShow: popupOnOpen
                        });
                    }
                }
            }
        );
    });
});

BX.ready(function () {
  BX.bind(document.querySelector(".js-citrus-fav-print"), "click", function () {
    var paramId = [];
    for (var i = 0, l = window.citrusRealtyFav.length; i < l; i++) {
      paramId.push("id[]=" + window.citrusRealtyFav[i]);
    }
    var url = BX.message("CITRUS_AREALTY_PDF_SEND_SITE_DIR")
      + "?" + paramId.join("&");
    location.href = url;
  });
});
