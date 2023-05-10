$().ready(function(){
    $('.form__item--password-icon').on('click', function(){
        let input = $(this).siblings('.form__input');

        if(input.attr('type') == 'password'){
            input.attr('type', 'text');
        }else{
            input.attr('type', 'password');
        }
    });
});