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
    window.citrusRealtyFavUpdateCount = function(count) {
        if (count > 0)
            $('.realty-favourites').html(BX.message('CITRUS_REALTY_FAV') + ' (' + count.toString() + ')');
        else
            $('.realty-favourites').html(BX.message('CITRUS_REALTY_FAV') + ' (0)');
    }
    window.citrusRealtyMark = function ($element, type) {
		if (type == 'add')
		{
			var msgIdx = 'CITRUS_REALTY_GO_2FAV';
	        if ($element.parents('.favorites_page').length)
	        	msgIdx = 'CITRUS_REALTY_FAV_REMOVE_TITLE';
			$element.addClass('added').html(BX.message(msgIdx)).attr('title', BX.message(msgIdx));
		}
		else
		{
			var msgIdx = 'CITRUS_REALTY_GO_2FAV';
	        if ($element.parents('.favorites_page').length)
	        	msgIdx = 'CITRUS_REALTY_2FAV';
			$element.removeClass('added').html(BX.message(msgIdx)).removeAttr('title');
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
            rootFolder + "json.php",
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
                    window.citrusRealtyFavUpdateCount(data.count);
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

    if ("object" === typeof(window.citrusRealtyFav)) {
        $('a[data-id]').each(function() {
            var $this = $(this),
                id = $this.data('id');
            if (window.citrusRealtyFav.indexOf(id) != -1)
                window.citrusRealtyMark($this, 'add');
        });
    }
});
