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

    BX.bind(document.querySelector(".js-citrus-detail-print"), "click", function () {
        var paramId = this.getAttribute('data-id');

        if(paramId){
            var url = BX.message("CITRUS_AREALTY_PDF_SEND_SITE_DIR") + "?id[]=" + paramId;

            location.href = url;
        }
      });
});


window.onload = function(){
    var videos = $('.js-create-poster');
    var videosThumbs = $('.gallery-thumbs__video');
    var preloader = $('.preloader');
    var print = function () {
    	setTimeout(function(){
    		preloader.removeClass('active');
    		window.print();
    	}, 4000);
    }

    preloader.addClass('active');

    if(videos.length){
        for (let i = 0; i < videos.length; i++) {
            var el = videos[i];
            var $pop = Popcorn('#' + $(el).attr('id'));
            
            $pop.capture({at: 5, set: false});
        }

        if(videosThumbs.length){
            for (let i = 0; i < videosThumbs.length; i++) {
                var el = videosThumbs[i];
                var $pop = Popcorn('#' + $(el).attr('id'));
                
                $pop.capture({at: 5, set: false});
    
                $(el).siblings('canvas').show();
                $(el).remove();
            }
        }

        window.onbeforeprint = function(event) {
            $('figure.is-active > video').hide();
            $('figure.is-active > canvas').show();
        };
        
        window.onafterprint = function(event) {
            $('figure.is-active > video').show();
            $('figure.is-active > canvas').hide();
        };
    }
    
    print();
};

