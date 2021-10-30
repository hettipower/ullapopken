jQuery(document).ready(function( $ ) {

	var $inp = $('.license input');
	var _val =$inp.val();

	$inp.on( 'input.mm-license', function( e ) {
		$(this)
			.next( '.dashicons' )
			[ $inp.val() == _val ? 'show' : 'hide' ]();
	});

});
