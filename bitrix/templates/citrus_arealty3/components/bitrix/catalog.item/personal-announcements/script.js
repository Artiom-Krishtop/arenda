$().ready(function(){
    $('.js-remove-announcements').on('click' , function (){
        $.ajax({
            url: $(this).data('url'),
            data: '',
            success: function(data){
                if(data.status == 'ok'){
                    window.location.reload()
                }else if(data.status == 'error') {
                    $('.b-error__notice').remove();
                    $('section.account-content__inner').prepend($('<div class="b-error__notice"></div>').html('Объявление не удалено!'));
                }
            },
            dataType: "json"
        })
    })

    $('.js-edit-announcements').on('click' , function (){
        window.location.href = $(this).data('url');
    })
})