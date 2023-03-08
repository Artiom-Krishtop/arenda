$(function () {
    if($('.ads-nav-sllider .swiper-container').length > 0){
        var portfolioNavSwiper = new Swiper('.ads-nav-sllider .swiper-container', {
            centeredSlides: true,
            slidesPerView: 'auto',
            slideToClickedSlide: true,
            spaceBetween: 45,
            navigation: {
                nextEl: '.ads-nav-sllider .swiper-button-next',
                prevEl: '.ads-nav-sllider .swiper-button-prev',
            },
        });
    }

    var sliderTabContent = new Swiper('.ads-content .swiper-container', {
        slidesPerView: 1,
        spaceBetween: 0,
        simulateTouch: true,
        speed: 500,
        autoHeight: true,
        autoplay: {
            delay: 5000,
            pauseOnMouseEnter: true
        },
    });

    if($('.ads-nav-sllider .swiper-container').length > 0){
        portfolioNavSwiper.on('slideChange', function () {
            $('.ads-nav-sllider a.is-active').removeClass('is-active');
            $('.ads-nav-sllider a').eq(this.activeIndex).addClass('is-active');

            var id = $('.ads-nav-sllider a.is-active').index();
            sliderTabContent.slideTo(id);
        });

        sliderTabContent.on('slideChange', function () {
            $('.ads-nav-sllider a.is-active').removeClass('is-active');
            $('.ads-nav-sllider a').eq(this.activeIndex).addClass('is-active');

            var id = $('.ads-nav-sllider a.is-active').index();
            portfolioNavSwiper.slideTo(id);
        });
    }
});