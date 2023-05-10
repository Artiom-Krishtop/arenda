$(function () {
    var licenseSwiper = new Swiper('.license-swiper .swiper-container', {
        slidesPerView: '3',
        spaceBetween: 30,
        navigation: {
            nextEl: '.license-swiper .swiper-button-next',
            prevEl: '.license-swiper .swiper-button-prev',
        },
        pagination: {
            el: '.license-swiper  .swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            767: {
                slidesPerView: 1
            },
            1023: {
                slidesPerView: 2
            }
        },
    });

    if ($('a.gallery-swipe').length) {
        $('a.gallery-swipe').initPhotoSwipe({
            loop: true,
            bgOpacity: .8
        });
    }
});