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
$fabric_content = get_field( 'fabric_content' );

?>
<div class="imgWrap">
	<?php the_post_thumbnail('full'); ?>
</div>
<div class="contentWrap">
	<?php if( $heading ): ?>
		<h2><?php echo $heading; ?></h2>
	<?php endif; ?>

	<ul class="nav nav-tabs" id="productTab" role="tablist">
		<li class="nav-item" role="presentation">
			<button class="nav-link active" id="productDetails-tab" data-bs-toggle="tab" data-bs-target="#productDetails" type="button" role="tab" aria-controls="productDetails" aria-selected="true">Details</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" id="fabricCare-tab" data-bs-toggle="tab" data-bs-target="#fabricCare" type="button" role="tab" aria-controls="fabricCare" aria-selected="false">Fabric & Care</button>
		</li>
	</ul>
	<div class="tab-content" id="productTabContent">
		<div class="tab-pane fade show active" id="productDetails" role="tabpanel" aria-labelledby="productDetails-tab">
			<h3 class="title">Product Details</h3>
			<?php the_content(); ?>
		</div>
		<div class="tab-pane fade" id="fabricCare" role="tabpanel" aria-labelledby="fabricCare-tab">
			<?php if( $fabric_content ): ?>
				<div class="intro">
					<h3 class="title">Fabric</h3>
					<?php echo $fabric_content; ?>
				</div>
			<?php endif; ?>

			<?php if ( have_rows( 'care_instructions' ) ) : ?>
				<div class="intro">
					<h3 class="title">Care Instructions</h3>
					<ul class="care">
						<?php while ( have_rows( 'care_instructions' ) ) : the_row(); ?>
							<li>
								<?php 
									$icon = get_sub_field( 'icon' );
									if ( $icon ) { 
								?>
									<img src="<?php echo $icon['url']; ?>" alt="<?php echo $icon['alt']; ?>" />
								<?php } ?>
								<?php the_sub_field( 'care' ); ?>
							</li>
						<?php endwhile; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
	
</div>

<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
	<span class="sku_wrapper"><?php esc_html_e( 'Item #:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>
<?php endif; ?>