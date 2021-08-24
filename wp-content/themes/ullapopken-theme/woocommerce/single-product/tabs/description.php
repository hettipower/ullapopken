<?php
/**
 * Description tab
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/tabs/description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

global $post;
global $product;

$heading = apply_filters( 'woocommerce_product_description_heading', __( 'Product information', 'woocommerce' ) );

?>
<div class="imgWrap">
	<?php the_post_thumbnail('full'); ?>
</div>
<div class="contentWrap">
	<?php if( $heading ): ?>
		<h2><?php echo $heading; ?></h2>
	<?php endif; ?>
	<div class="categories">
		<?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . '<span>Details</span>' . ' ', '</span>' ); ?>
	</div>
	<h3 class="title">Product Details</h3>
	<?php the_content(); ?>
</div>

<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
	<span class="sku_wrapper"><?php esc_html_e( 'Item #:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>
<?php endif; ?>