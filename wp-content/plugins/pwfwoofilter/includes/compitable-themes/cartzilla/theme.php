<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'pwf_html_pagination', 'pwf_cartzilla_theme_customize_pagination', 10, 3 );

if ( ! function_exists( 'pwf_cartzilla_theme_customize_pagination' ) ) {
	function pwf_cartzilla_theme_customize_pagination( $output, $filter_id, $args ) {

		$total   = $args['total'];
		$current = $args['current'];
		$base    = $args['base'];
		$format  = '';

		if ( $total <= 1 ) {
			return;
		}

		$links = paginate_links(
			apply_filters(
				'woocommerce_pagination_args',
				array( // WPCS: XSS ok.
					'base'      => $base,
					'format'    => $format,
					'add_args'  => false,
					'current'   => max( 1, $current ),
					'total'     => $total,
					'prev_next' => false,
					'type'      => 'array',
					'end_size'  => 3,
					'mid_size'  => 3,
				)
			)
		);
		ob_start();
		?>
		<nav class="d-flex justify-content-between pt-2 cartzilla-shop-pagination w-100" aria-label="
		<?php
		/* translators: aria-label for products navigation wrapper */
		echo esc_attr_x( 'Page navigation', 'front-end', 'cartzilla' );
		?>
		">
			<ul class="pagination">
				<?php if ( $current && 1 < $current ) : ?>
					<li class="page-item">
						<a class="page-link" href="<?php echo get_pagenum_link( $current - 1 ); ?>">
							<i class="czi-arrow-left mr-2"></i>
							<?php
							/* translators: label for previous products link */
							echo esc_html_x( 'Prev', 'front-end', 'cartzilla' );
							?>
						</a>
					</li>
				<?php endif; ?>
			</ul>
			<ul class="pagination">
				<li class="page-item d-sm-none">
					<span class="page-link page-link-static"><?php echo esc_html( "{$current} / {$total}" ); ?></span>
				</li>
				<?php foreach ( $links as $link ) : ?>
					<?php if ( false !== strpos( $link, 'current' ) ) : ?>
						<li class="page-item active d-none d-sm-block">
							<?php echo str_replace( 'page-numbers', 'page-link', $link ); ?>
						</li>
					<?php else : ?>
						<li class="page-item d-none d-sm-block">
							<?php echo str_replace( 'page-numbers', 'page-link', $link ); ?>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
			<ul class="pagination">
				<?php if ( $current && $current < $total ) : ?>
					<li class="page-item">
						<a class="page-link" href="<?php echo get_pagenum_link( $current + 1 ); ?>">
						<?php
							/* translators: label for next products link */
							echo esc_html_x( 'Next', 'front-end', 'cartzilla' );
						?>
							<i class="czi-arrow-right ml-2"></i>
						</a>
					</li>
				<?php endif; ?>
			</ul>
		</nav>
		<?php
		return ob_get_clean();
	}
}
