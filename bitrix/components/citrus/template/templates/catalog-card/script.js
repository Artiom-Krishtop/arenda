
$(function(){
    $('.catalog-card').hover(
	    function () {
		    $( this )
		        .find('.catalog-card__hidden-content')
		        .stop( true, true )
		        .slideDown(200);
	    },
	    function () {
		    $( this )
			    .find('.catalog-card__hidden-content')
		        .stop( true, true )
		        .slideUp(200);
	    }
    );
});