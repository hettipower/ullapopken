<?php
/**
 * Sidebar
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/sidebar.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$getShopCookie = theme_get_cookie('shop');

if( $getShopCookie == 'women' ) {
    $parentTermID = get_field( 'women_category', 'option' )->term_id;
} else if( $getShopCookie == 'men' ) {
    $parentTermID = get_field( 'men_category', 'option' )->term_id;
} else {
    $parentTermID = get_field( 'women_category', 'option' )->term_id;
}

$currentTermID = (isset(get_queried_object()->term_id)) ? get_queried_object()->term_id : false;
?>
<div id="sidebar" role="complementary">
	<h4>Categories</h4>
	<ul class="list-group">
		<?php
			$terms = get_terms( array(
				'taxonomy' => 'product_cat',
				'hide_empty' => false,
				'parent' => $parentTermID
			) );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ):
				foreach ( $terms as $term ):
		?>
			<li class="list-group-item">
				<a href="<?php echo get_term_link( $term ); ?>" class="list-group-item-action"><?php echo $term->name; ?></a>
				<?php 
					if( $currentTermID == $term->term_id ): 
						$childTerms = get_terms( array(
							'taxonomy' => 'product_cat',
							'hide_empty' => false,
							'parent' => $currentTermID
						) );
						if ( ! empty( $childTerms ) && ! is_wp_error( $childTerms ) ):
				?>
					<ul class="list-group sub-cat-wrap">
						<?php foreach ( $childTerms as $childterm ): ?>
							<li class="list-group-item">
								<a href="<?php echo get_term_link( $childterm ); ?>" class="list-group-item-action"><?php echo $childterm->name; ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; endif; ?>
			</li>
		<?php endforeach; endif; ?>
	</ul>
</div>
<?php
