<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked wc_empty_cart_message - 10
 */
//do_action( 'woocommerce_cart_is_empty' );

if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
	<div class="emptyCartWrap">
		<h2>Bag</h2>
		<div class="emptyCartImg">
			<img src="<?php print THEME_IMAGES; ?>/icon_cart_empty.svg" alt="empty cart" />
		</div>
		<div class="emptyCartMgs text-center">
			<h3>Your shopping bag is empty</h3>
			<p>Are you missing items from your shopping bag? Sign in to find your saved items.</p>
		</div>
	</div>
<?php endif; ?>
