<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$guest = ( isset($_GET['guest']) ) ? true : false;
if( !is_user_logged_in() ) {
	$showForm = false;
	if( $guest ) {
		$showForm = true;
	}
} else {
	$showForm = true;
}
?>

<?php if( !$showForm ): ?>
	<div class="loginRegistrationWrap">
		<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>
	</div>
<?php endif; ?>

<form <?php echo (!$showForm) ? 'style="display:none"' : '' ; ?> name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<div class="formFieldsWrap">
		
		<?php if ( $checkout->get_checkout_fields() ) : ?>

			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

			<div class="formData billingData">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>
				<div class="radioWrapper">
					
					<div class="form-check">
						<label class="form-check-label woocommerce-form__label woocommerce-form__label-for-checkbox checkbox" for="ship-to-same-address-checkbox">
							<input 
								id="ship-to-same-address-checkbox"
								class="form-check-input woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" 
								checked="checked"
								type="radio" 
								name="ship_to_different_address" 
								value="0" 
							/>
							<div class="custom-control-label"></div>
							<h4>Ship to Billing Address</h4>
							<div class="form-text">The delivery address corresponds to the billing address</div>
						</label>
					</div>

					<div class="form-check" >
						<label class="form-check-label woocommerce-form__label woocommerce-form__label-for-checkbox checkbox" for="ship-to-different-address-checkbox">
							<input 
								id="ship-to-different-address-checkbox"
								class="form-check-input woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" 
								type="radio" 
								name="ship_to_different_address" 
								value="1" 
							/>
							<div class="custom-control-label"></div>
							<h4>Use Shipping Address</h4>
							<div class="form-text">Specify a different delivery address</div>
						</label>
					</div>

				</div>
			<?php endif; ?>

			<div class="formData shippingData">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

		<?php endif; ?>

	</div>

	<div class="orderDetailsWrap">
		<div class="innerOrderDetails">

			<div class="orderDetail">
				<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
		
				<h3 id="order_review_heading"><?php esc_html_e( 'My Order', 'woocommerce' ); ?></h3>
				
				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
			</div>

			<div class="contactWrap">
				<h3 class="widget-title">Contact us</h3>
				<div class="tele"><?php the_field( 'telephone', 'option' ); ?></div>
				<div class="hour"><?php the_field( 'open_hour', 'option' ); ?></div>
				<div class="email"><?php the_field( 'email', 'option' ); ?></div>
			</div>
		</div>

	</div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
