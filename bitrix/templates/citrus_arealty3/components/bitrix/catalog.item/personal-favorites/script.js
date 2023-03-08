$(function () {
    window.citrusRealtyMark = function ($element, type) {
		if (type == 'add')
		{
            msgIdx = 'Удалить из избранного';
			$element.addClass('added').removeClass('account-content__btn').html(msgIdx);
		}
		else
		{
            // account-content__btn
            msgIdx = 'Добавить в избранное';
			$element.removeClass('added').addClass('account-content__btn').html(msgIdx);
		}
    }

    $('.add2favourites[data-id]').on("click", function (e) {
        e.preventDefault();

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