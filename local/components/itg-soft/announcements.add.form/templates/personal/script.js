document.addEventListener("DOMContentLoaded", function () {
    function accountNewTabs() {
        const accountNavItem = $(".account-content__new-item");
        const accountContent = $(".account-content__new-block");

        accountNavItem.on("click", function () {
            let activeContent = $(this).data("nav-target");

            accountNavItem.removeClass("account-content__new-item--active");
            accountContent.removeClass("account-content__new-block--active");

            $(this).addClass("account-content__new-item--active");
            $('.account-content__new-block[data-target="' + activeContent + '"]').addClass("account-content__new-block--active");
        });
    }
    accountNewTabs();

    function toggleTabsBtn() {
        $('.js-toggle-tab-btn').on('click', function (e) {
            e.preventDefault();

            let activeNav = $(".account-content__new-item.account-content__new-item--active");
            let pos = $(this).data('pos');

            switch (pos) {
                case 'next':
                    activeNav.next('.account-content__new-item').click();
                    break;

                case 'prev':
                    activeNav.prev('.account-content__new-item').click();
                    break;

                case 'send':
                    $('form.account-content__new-blocks').submit();
                    break;
            }
        })
    }
    toggleTabsBtn();

    function addInputField() {
        $('.js-btn-add-field').on('click', function (e) {
            e.preventDefault();

            let order = $(this).data('prop-order');
            let propId = $(this).data('prop-id');
            let input = $('<input class="form__input multiple" type="text" name="PROPERTY[' + propId + '][' + order + ']"  value=""/>');

            $(this).before(input);
            $(this).data('prop-order', ++order);
        });
    }
    addInputField();

    const elem = document.querySelectorAll('.form__input-date');
    elem.forEach(el => {
        const datepicker = new Datepicker(el, {
            format: 'dd.mm.yyyy',
            minDate: new Date(),
        });
    });
});