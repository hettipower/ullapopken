jQuery(document).ready(function( $ ) {

	var $choose = $('#mm_header_image_yes'),
		$header = $('<div class="header-image" />').prependTo( $choose.parent() ),
		$hidden = $('#mm_header_image_src'),
		$scale  = $('#mm_header_image_scale'),
		frame;

	$choose
		.on( 'click.mm',
			function( e )
			{
				if ( frame )
				{
					frame.open();
					return;
				}
    
				frame = wp.media({
					title: mmenu_i18n.choose_an_image,
					button: {
						text: mmenu_i18n.use_this_image
					},
					multiple: false
				});

				frame.on( 'select',
					function()
					{
						var src = frame.state().get( 'selection' ).first().toJSON().url;

						$hidden.val( src );
 						setImage( src );
					}
				);

				frame.open();
			}
		);

	$scale
		.on( 'change.mm',
			function( e )
			{
				if ( $scale.val() == 'cover' )
				{
					$header.addClass( 'cover' );
				}
				else
				{
					$header.removeClass( 'cover' );
				}
			}
		)
		.trigger( 'change.mm' );

	function setImage( src )
	{
		if ( src )
		{
    		$header.attr( 'style', 'background-image: url(' + src + ')' );
		}
		else
		{
			$header.removeAttr( 'style' );
		}
	}
	setImage( $hidden.val() );

});