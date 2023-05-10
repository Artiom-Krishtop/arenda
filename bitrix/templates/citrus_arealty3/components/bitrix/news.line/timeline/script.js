$(function () {
    var portfolioNavSwiper = new Swiper('.history-results-nav-sllider .swiper-container', {
        centeredSlides: true,
        slidesPerView: 'auto',
        slideToClickedSlide: true,
        spaceBetween: 45,
        navigation: {
            nextEl: '.history-results-nav-sllider .swiper-button-next',
            prevEl: '.history-results-nav-sllider .swiper-button-prev',
        },
    });

    var sliderTabContent = new Swiper('.history-results-content .swiper-container', {
        slidesPerView: 1,
        spaceBetween: 0,
        simulateTouch: false,
        speed: 500,
        autoHeight: true,
    });

    portfolioNavSwiper.on('slideChange', function () {
        $('.history-results-nav-sllider a.is-active').removeClass('is-active');
        $('.history-results-nav-sllider a').eq(this.activeIndex).addClass('is-active');

        var id = $('.history-results-nav-sllider a.is-active').index();
        sliderTabContent.slideTo(id);
    });

    sliderTabContent.on('slideChange', function () {
        $('.history-results-nav-sllider a.is-active').removeClass('is-active');
        $('.history-results-nav-sllider a').eq(this.activeIndex).addClass('is-active');

        var id = $('.history-results-nav-sllider a.is-active').index();
        portfolioNavSwiper.slideTo(id);
    });
});