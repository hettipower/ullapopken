<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_footer', 'pwf_avada_theme_add_js_code', 1000 );

if ( ! function_exists( 'pwf_avada_theme_add_js_code' ) ) {

	function pwf_avada_theme_add_js_code() {
		?>
		<script type="text/javascript">
			(function( $ ) {
				$('body').on('click', '.catalog-ordering .orderby li ul a', function( event ) {
					event.preventDefault();
					let link  = $(this).attr('href');
					let orderBy = link.replace('?product_orderby=', '');
					$( document.body ).trigger('pwfTriggerSorting', [{orderby:orderBy}]);
					let text = orderBy.charAt(0).toUpperCase() + orderBy.slice(1);
					$('.catalog-ordering .orderby li .current-li strong').text( text );
				});
			})(jQuery);
		</script>
		<?php
	}
}
