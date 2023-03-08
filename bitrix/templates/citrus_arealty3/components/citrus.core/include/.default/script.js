
$(function(){
    $(document).on('click', '.js-section-show-more', function(event) {
        event.preventDefault();
        
        $(this).closest('.section-inner')
                .find('.section__content')
                .css('max-height', '')
                .removeClass('_cut_overflow');
        
	    $(this).remove();
    });
});
