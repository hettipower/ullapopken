<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<div class="cartItemsWrap">
		<h2 class="mainTitle">Bag <span class="cartCount"><?php echo WC()->cart->cart_contents_count; ?> items</span></h2>
		
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<div class="cartItems">
			<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
					$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );

						//Has parent product
						$_parent_product = wc_get_product($_product->get_parent_id());

						$item_id = ( !empty( $cart_item['variation_id'] ) ) ? $cart_item['variation_id'] : '';
						if ( !empty( $item_id ) ) {
							$variations = get_variation_data_from_variation_id( $item_id );
						} else {
							$variations = array();
						}

			?>
				<div class="cartItem <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<div class="imgWrap">
						<?php if( $cart_item['quantity'] > 1 ): ?>
							<span class="cartItemCount"><?php echo $cart_item['quantity']; ?>x</span>
						<?php endif; ?>
						<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							if ( ! $product_permalink ) {
								echo $thumbnail; // PHPCS: XSS ok.
							} else {
								printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
							}
						?>
					</div>
					<div class="contentWrap">

						<div class="proTitle">
							<?php
								if( $_parent_product ) {
									if( $_parent_product->is_type('variable') ) {
										if ( ! $product_permalink ) {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', get_the_title($_product->get_parent_id()), $cart_item, $cart_item_key ) . '&nbsp;' );
										} else {
											echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), get_the_title($_product->get_parent_id()) ), $cart_item, $cart_item_key ) );
										}
									}
								} else {
									if ( ! $product_permalink ) {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
									} else {
										echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
									}
								}

								do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
							?>
						</div>

						<div class="productMeta">
							<?php
								//Variations Data
								if( $variations ) {
									foreach($variations as $variation) {
										echo '<div class="variation">'.$variation.'</div>';
									}
								}
								// Meta data.
								echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

								// Backorder notification.
								if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
								}
							?>
						</div>

						<div class="priceWrap">
							<div class="price">
								<?php if( $cart_item['quantity'] > 1 ): ?>
									Price each :
									<?php
										echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
									?>
								<?php endif; ?>
							</div>
							<div class="subtotal">
								<?php
									echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
								?>
							</div>
						</div>

						<div class="actionsWrap">
							<div class="qty">
								<?php
									if ( $_product->is_sold_individually() ) {
										$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
									} else {
										$product_quantity = woocommerce_quantity_input(
											array(
												'input_name'   => "cart[{$cart_item_key}][qty]",
												'input_value'  => $cart_item['quantity'],
												'max_value'    => $_product->get_max_purchase_quantity(),
												'min_value'    => '0',
												'product_name' => $_product->get_name(),
											),
											$_product,
											false
										);
									}

									echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
								?>
							</div>
							
							<?php
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg> Delete</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										esc_html__( 'Delete', 'woocommerce' ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() )
									),
									$cart_item_key
								);
							?>
							
						</div>

					</div>
				</div>

			<?php endif; endforeach; ?>
		</div>

		<?php do_action( 'woocommerce_cart_contents' ); ?>

		<div class="submitCartWrap">
			<button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

			<?php do_action( 'woocommerce_cart_actions' ); ?>

			<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
		</div>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>

	</div>

	<div class="cartTotalsWrap">
		<div class="innerCartTotals">

			<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
			<div class="cartTotal"><?php echo woocommerce_cart_totals(); ?></div>

			<?php if ( wc_coupons_enabled() ) { ?>
				<div class="coupon">
					<label for="coupon_code">Do you have a promo code?</label>
					<input type="text" name="coupon_code" class="input-text form-control" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'enter promo code', 'woocommerce' ); ?>" />
					<button type="submit" class="btn" name="apply_coupon" value="<?php esc_attr_e( 'Apply', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply', 'woocommerce' ); ?></button>
					<?php do_action( 'woocommerce_cart_coupon' ); ?>
				</div>
			<?php } ?>

			<div class="contactWrap">
				<h3 class="widget-title">Contact us</h3>
				<div class="tele"><?php the_field( 'telephone', 'option' ); ?></div>
				<div class="hour"><?php the_field( 'open_hour', 'option' ); ?></div>
				<div class="email"><?php the_field( 'email', 'option' ); ?></div>
			</div>

		</div>
	</div>

	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<div class="continueShopping">
	<a href="<?php echo home_url(); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
			<path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
		</svg>
		continue shopping
	</a>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
