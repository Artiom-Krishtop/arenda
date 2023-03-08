$().ready(function(){
    $('.header-personal').mouseover(function(){
        if(!$(this).hasClass('hover')){
            $(this).addClass('hover');
        }
    });

    $('.header-personal').mouseout(function(){
        if($(this).hasClass('hover')){
            $(this).removeClass('hover');
        }
    });

    $('.js-phone-mask').mask('+375-(99)-999-99-99');
});

