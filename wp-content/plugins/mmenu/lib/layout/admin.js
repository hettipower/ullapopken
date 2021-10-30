jQuery(document).ready(function( $ ) {

	$('.section-toggle a').on( 'click', function( e ) {
		e.preventDefault();
		$(this)
			.closest( '.section' )
			.addClass( 'closed' )
			.nextUntil( 'h2', '.section' )
			.removeClass( 'closed' );
	});

});
