<?php
/**
 * Wishlist page template - Standard Layout
 *
 * @author YITH
 * @package YITH\Wishlist\Templates\Wishlist\View
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist                      \YITH_WCWL_Wishlist Current wishlist
 * @var $wishlist_items                array Array of items to show for current page
 * @var $wishlist_token                string Current wishlist token
 * @var $wishlist_id                   int Current wishlist id
 * @var $users_wishlists               array Array of current user wishlists
 * @var $pagination                    string yes/no
 * @var $per_page                      int Items per page
 * @var $current_page                  int Current page
 * @var $page_links                    array Array of page links
 * @var $is_user_owner                 bool Whether current user is wishlist owner
 * @var $show_price                    bool Whether to show price column
 * @var $show_dateadded                bool Whether to show item date of addition
 * @var $show_stock_status             bool Whether to show product stock status
 * @var $show_add_to_cart              bool Whether to show Add to Cart button
 * @var $show_remove_product           bool Whether to show Remove button
 * @var $show_price_variations         bool Whether to show price variation over time
 * @var $show_variation                bool Whether to show variation attributes when possible
 * @var $show_cb                       bool Whether to show checkbox column
 * @var $show_quantity                 bool Whether to show input quantity or not
 * @var $show_ask_estimate_button      bool Whether to show Ask an Estimate form
 * @var $show_last_column              bool Whether to show last column (calculated basing on previous flags)
 * @var $move_to_another_wishlist      bool Whether to show Move to another wishlist select
 * @var $move_to_another_wishlist_type string Whether to show a select or a popup for wishlist change
 * @var $additional_info               bool Whether to show Additional info textarea in Ask an estimate form
 * @var $price_excl_tax                bool Whether to show price excluding taxes
 * @var $enable_drag_n_drop            bool Whether to enable drag n drop feature
 * @var $repeat_remove_button          bool Whether to repeat remove button in last column
 * @var $available_multi_wishlist      bool Whether multi wishlist is enabled and available
 * @var $no_interactions               bool
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php if ( $wishlist && $wishlist->has_items() ) : ?>

	<ul class="wishlistItemsWrap">
		<?php foreach ( $wishlist_items as $item ) : 
			/**
			 * Each of the wishlist items
			 *
			 * @var $item \YITH_WCWL_Wishlist_Item
			 */
			global $product;

			$product      = $item->get_product();
			$availability = $product->get_availability();
			$stock_status = isset( $availability['class'] ) ? $availability['class'] : false;
			if ( $product && $product->exists() ) :
		?>
			<li class="wishlistItem" id="yith-wcwl-row-<?php echo esc_attr( $item->get_product_id() ); ?>" data-row-id="<?php echo esc_attr( $item->get_product_id() ); ?>">

				<div class="imgWrap">
					<?php do_action( 'yith_wcwl_table_before_product_thumbnail', $item, $wishlist ); ?>

					<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item->get_product_id() ) ) ); ?>">
						<?php echo wp_kses_post( $product->get_image('full') ); ?>
					</a>

					<?php do_action( 'yith_wcwl_table_after_product_thumbnail', $item, $wishlist ); ?>
				</div>

				<?php do_action( 'yith_wcwl_table_before_product_name', $item, $wishlist ); ?>
				<h3>
					<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item->get_product_id() ) ) ); ?>">
						<?php echo wp_kses_post( apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ); ?>
					</a>
				</h3>
				<div class="metas">
					<?php
						if ( $product->is_type( 'variation' ) ) {
							/**
							 * Product is a Variation
							 *
							 * @var $product \WC_Product_Variation
							 */
							echo wp_kses_post( wc_get_formatted_variation( $product ) );
						}
					?>
				</div>
				<?php do_action( 'yith_wcwl_table_after_product_name', $item, $wishlist ); ?>

				<div class="price">
					<?php if ( $show_price || $show_price_variations ) : ?>
						<?php do_action( 'yith_wcwl_table_before_product_price', $item, $wishlist ); ?>

						<?php
							if ( $show_price ) {
								echo wp_kses_post( $item->get_formatted_product_price() );
							}

							if ( $show_price_variations ) {
								echo wp_kses_post( $item->get_price_variation() );
							}
						?>

						<?php do_action( 'yith_wcwl_table_after_product_price', $item, $wishlist ); ?>
					<?php endif ?>
				</div>

				<div class="addtoCart">
					<?php do_action( 'yith_wcwl_table_before_product_cart', $item, $wishlist ); ?>

					<?php do_action( 'yith_wcwl_table_product_before_add_to_cart', $item, $wishlist ); ?>

					<!-- Add to cart button -->
					<?php $show_add_to_cart = apply_filters( 'yith_wcwl_table_product_show_add_to_cart', $show_add_to_cart, $item, $wishlist ); ?>
					<?php if ( $show_add_to_cart && isset( $stock_status ) && 'out-of-stock' !== $stock_status ) : ?>
						<?php woocommerce_template_loop_add_to_cart( array( 'quantity' => $show_quantity ? $item->get_quantity() : 1 ) ); ?>
					<?php endif ?>

					<?php do_action( 'yith_wcwl_table_product_after_add_to_cart', $item, $wishlist ); ?>
				</div>

				<?php if ( $show_remove_product ) : ?>
					<div class="remove">
						<a href="<?php echo esc_url( $item->get_remove_url() ); ?>" class="remove remove_from_wishlist" title="<?php echo esc_html( apply_filters( 'yith_wcwl_remove_product_wishlist_message_title', __( 'Remove this product', 'yith-woocommerce-wishlist' ) ) ); ?>">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
								<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
							</svg>
							delete
						</a>
					</div>
				<?php endif; ?>
				
			</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>

<?php else: ?>
    <div class="emptyWishlistWrap">
        <img src="<?php print THEME_IMAGES; ?>/icon_favorites_empty.svg" alt="empty wishlist" />
        <h3>Oops, the favorites list is empty at the moment.</h3>
        <p>She'll be happy if you fill her with your latest favorites!</p>
        <div class="btnWrap">
            <a href="<?php echo home_url(); ?>" class="btn">Discover now</a>
        </div>
    </div>
<?php endif; ?>