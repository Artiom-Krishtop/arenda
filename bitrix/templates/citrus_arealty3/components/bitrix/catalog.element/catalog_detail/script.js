$(function () {
    var galleryThumbs = new Swiper('.object-gallery-thumbs .swiper-container', {
        scrollbar: {
            el: '.object-gallery-thumbs .swiper-scrollbar',
            draggable: true,
        },
        slidesPerView: 3,
        paginationClickable: true,
        spaceBetween: 10,
        touchRatio: .5,
        scrollbarHide: false,
        breakpoints: {
            360: {
                slidesPerView: 2
            }
        }
    });

    var gallery = {
        change: function (index) {
            $(".object-gallery-previews figure.is-active").removeClass('is-active');
            $(".object-gallery-previews figure").eq(index).addClass('is-active');
            $(".object-gallery-thumbs a.gallery-thumbs.is-active").removeClass('is-active');
            $(".object-gallery-thumbs a.gallery-thumbs").eq(index).addClass('is-active');
        }
    }

    $(document).on('click', '.object-gallery-thumbs a.gallery-thumbs:not(".is-active")', function (event) {
        event.preventDefault();
        var index = $(this).parent(".swiper-slide").index(),
            SwiperMiddle = +Math.floor(galleryThumbs.params.slidesPerView / 2);
        if (index > 0 && index >= SwiperMiddle && SwiperMiddle >= 1) {
            var index1 = index - SwiperMiddle;
        }
        galleryThumbs.slideTo(index1);
        gallery.change(index);
    });

    $('.personal_manager_link').on('click', function () {
        smoothScroll('#personal_manager', {
            offsetTop: 0,
            duration: 600,
        });
    });

    $("a.gallery-previews").initPhotoSwipe({
        loop: true,
        events: {
            afterChange: function () {
                var index = this.getCurrentIndex();
                var SwiperMiddle = +Math.floor(galleryThumbs.params.slidesPerView / 2);
                if (index > 0 && index >= SwiperMiddle && SwiperMiddle >= 2) index = index - SwiperMiddle;
                galleryThumbs.slideTo(index);
                gallery.change(index);
            }
        },
        bgOpacity: .8
    });

    $('#object-edit-link').show().appendTo('.image-actions');

});

BX.ready(function () {
    BX.bind(document.querySelector(".js-citrus-pdf-send"), "click", function () {
        var id = this.getAttribute("data-id");
        if (id == "") {
            console.error("ID not defined");
            return;
        }
        var email = prompt(BX.message("CITRUS_AREALTY_PDF_SEND_PROMPT"), "");
        if (email == "" || email == null) {
            console.error("E-email not defined");
            return;
        }
        BX.ajax({
            url: BX.message("CITRUS_AREALTY_PDF_SEND_URL")
                + "?id=" + id + "&to=" + email + "&currency=" + currency.current,
            method: "GET",
            dataType: "json",
            cache: false,
            onsuccess: function (data) {
                if (data.result > 0) {
                    alert(BX.message("CITRUS_AREALTY_PDF_SEND_RESULT"));
                } else if (data.error) {
                    alert(data.error);
                }
            },
        });
    });
});