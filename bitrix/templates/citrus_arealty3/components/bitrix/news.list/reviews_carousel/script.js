$(function () {
    mySwiper = new Swiper(".recommendation-list .swiper-container", {
        slidesPerView: 2,
        spaceBetween: 30,
        autoHeight: true,
        autoplayDisableOnInteraction: false,
        loop: true,
        navigation: {
            nextEl: '.recommendation-list .swiper-button-next',
            prevEl: '.recommendation-list .swiper-button-prev',
        },
        pagination: {
            el: '.recommendation-list .swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            1023: {
                slidesPerView: 1
            }
        },
    });

    if ($(window).outerWidth(true) > 1024) {
        $('.recommendation-item').equalHeight();
        mySwiper.updateAutoHeight(50);
    }
});

