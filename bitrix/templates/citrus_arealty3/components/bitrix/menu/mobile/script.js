$(function(){
	$('._parent > .mobile-menu__link').on('click', function(event) {
	    event.preventDefault();
	    
	    var $li = $(this).closest('.mobile-menu__li');
        if ($li.hasClass('back_link')) return;

		$li.toggleClass('_open');
		
		$li.siblings("._open")
			.removeClass('_open');
	});

    $('.back_link .mobile-menu__link').on('click', function(event) {
        event.preventDefault();

        var $li = $(this).closest('.mobile-menu__li._open');
        $li.toggleClass('_open');
    });
});