;$(function () {
    function centerElementSlider(slider) {
        $(slider).find('.swiper-wrapper').each(function (index, element) {
            var swiperWrapperWidth = $(element).outerWidth(true),
                slidesWidth = 0;

            $(element).find('.swiper-slide').each(function () {
                slidesWidth += $(this).outerWidth(true);
            });

            if (slidesWidth < swiperWrapperWidth) {
                $(element).find('.swiper-slide:last-child').css('margin-right', 0);
                $(element).css('justify-content', 'center');
            } else {
                $(element).css('justify-content', 'flex-start');
            }
        });
    }

    centerElementSlider('.staff_sections .p__swiper.staff-swiper');

    $(window).resize(function () {
        centerElementSlider('.staff_sections .p__swiper.staff-swiper');
    });
});