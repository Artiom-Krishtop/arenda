if (typeof($) !== 'undefined') {
    $(function () {

        if ( cui.ifdefined(citrusValidator)) {
            new citrusValidator(
                $("#citrus_subscribe_form")
            );
        }

	    $("#citrus_subscribe_form").on('submit', function(event) {
            var $res = $('#citrus_subscribe_res'),
                $form = $('#citrus_subscribe_form'),
                $submit = $('#citrus_subscribe_submit');

            var arPost = {};
            $.each($form.find('input'), function () {
                if ($(this).attr('type') != 'checkbox') {
                    arPost[$(this).attr('name')] = $(this).val();
                }
                else if ($(this).attr('type') == 'checkbox' && $(this).is(':checked')) {
                    arPost[$(this).attr('name')] = $(this).val();
                }
            });
            $res.hide();
            $submit.attr('disabled', 'disabled');
            $.post('/bitrix/components/citrus/subscribe.form/action.php', arPost,
                function (data) {
                    $submit.removeAttr('disabled');
                    if (data && typeof(data) === 'object') {
                        if (data.status == 'error') {
                            $res.addClass("errortext").removeClass("notetext");
                        } else {
                            $res.addClass("notetext").removeClass("errortext");
                        }
                        $res.html(data.message).show();
                    }
                }, 'json');
            return false;
        });
    });
}