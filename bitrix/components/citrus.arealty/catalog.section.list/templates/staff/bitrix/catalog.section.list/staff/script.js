$(function () {
    $('.nav-item').click(function () {
        if (!$(this).hasClass('active')) {
            $('.nav-item').removeClass('active');
            $(this).addClass('active');
        }
    });

    var swiper = new Swiper('.nav-list-container > .swiper-container', {
        freeMode: true,
        slideToClickedSlide: true,
        initialSlide: $('.nav-list-container').data('swiperActiveIndex'),
        slidesPerView: 'auto',
        setWrapperSize: true,
        navigation: {
            nextEl: '.nav-list-container > .swiper-button-next',
            prevEl: '.nav-list-container > .swiper-button-prev'
        }
    });
    $(window).resize(function () {
        swiper.update();
    });
});
