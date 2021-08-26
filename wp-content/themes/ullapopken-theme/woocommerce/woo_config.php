<?php
remove_action('woocommerce_before_shop_loop' , 'woocommerce_result_count' , 20);
remove_action('woocommerce_before_shop_loop' , 'woocommerce_catalog_ordering' , 30);
remove_action('woocommerce_after_shop_loop_item' , 'woocommerce_template_loop_add_to_cart' , 10);
remove_action('woocommerce_before_shop_loop_item_title' , 'woocommerce_template_loop_product_thumbnail' , 10);
remove_action('woocommerce_before_shop_loop_item' , 'woocommerce_template_loop_product_link_open' , 10);
remove_action('woocommerce_single_product_summary' , 'woocommerce_template_single_meta' , 40);
remove_action('woocommerce_after_single_product_summary' , 'woocommerce_output_product_data_tabs' , 10);

add_action('woocommerce_shop_loop_item_title' , 'woocommerce_template_loop_product_link_open' , 5);
add_action('woocommerce_before_single_product_summary' , 'custom_track_product_view' , 30);


add_action('after_setup_theme', 'ullapopken_woo_add_woocommerce_support');
function ullapopken_woo_add_woocommerce_support()
{
    add_theme_support('html5', array('search-form'));
    add_theme_support('woocommerce', array(
        'thumbnail_image_width' => 150,
        'single_image_width'    => 300,

        'product_grid'          => array(
            'default_rows'    => 3,
            'min_rows'        => 2,
            'max_rows'        => 8,
            'default_columns' => 4,
            'min_columns'     => 2,
            'max_columns'     => 5,
        ),
    ));

    //add_theme_support( 'wc-product-gallery-zoom' );
    //add_theme_support('wc-product-gallery-lightbox');
    //add_theme_support('wc-product-gallery-slider');
}

add_action('woocommerce_before_shop_loop' , 'ullapopken_filter_btns_func' , 35);
function ullapopken_filter_btns_func() {
?>
<div class="filterBtnsWrap">
    <div class="btn btn-stretch btn-dark" data-filter="">
        Filter
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
        </svg>
    </div>
    <div class="btn btn-stretch btn-light" data-filter="orderby">
        Sort
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
        </svg>
    </div>
    <div class="btn btn-stretch btn-light" data-filter="size">
        Size
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
        </svg>
    </div>
    <div class="btn btn-stretch btn-light" data-filter="color">
        Color
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
        </svg>
    </div>
    <div class="btn btn-stretch btn-light" data-filter="price">
        Price
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
        </svg>
    </div>
    <div class="btn btn-stretch btn-light" data-filter="material">
        Material
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
        </svg>
    </div>
</div>
<div class="active-filters"></div>
<?php
}

add_action('woocommerce_before_shop_loop_item_title' , 'ullapopken_product_color_attr' , 35);
function ullapopken_product_color_attr(){
    $colors = wc_get_product_terms( get_the_ID(), 'pa_color' );
    $imagesArr = get_color_variations_image();

    //Show Images
    if( $colors && $imagesArr ) {
        echo '<div class="imageList">';
        $i=1;
        foreach( $colors as $color ) {
            $active = ($i==1) ? 'active' : '' ;
            $imgUrl = in_array_r($color->slug , $imagesArr);
            if( $i==1 ){
                echo woocommerce_template_loop_product_link_open();
                echo '<div class="imgItem" style="background-image: url('.$imgUrl.')"></div>';
                echo woocommerce_template_loop_product_link_close();
            }
            $i++;
        }
        echo '</div>';
    }

    //Show Attributes
    if( $colors ) {
        echo '<div class="colorList">';
        $i=1;
        foreach( $colors as $color ) {
            $colorcodes = get_term_meta($color->term_id , 'product_attribute_color');
            //$colorCode = get_field( 'color', 'pa_color_'.$color->term_id );
            $active = ($i==1) ? 'active' : '' ;
            $imgUrl = in_array_r($color->slug , $imagesArr);
            if( $colorcodes ) {
                echo '<div class="colorItem"><span class="'.$active.'" style="background-color:'.$colorcodes[0].'" data-colorid="'.$color->term_id.'" data-img="'.$imgUrl.'"></span></div>';
            }
            $i++;
        }
        echo '</div>';
    }
}

function get_color_variations_image() {
    global $product;
    $variations = $product->get_available_variations();
    $imagesArr = array();

    if( $variations ) {
        foreach ( $variations as $variation ) {
            array_push($imagesArr , array(
                'img' => $variation['image']['url'],
                'color' => $variation['attributes']['attribute_pa_color']
            ));
        }
    }

    return array_unique($imagesArr, SORT_REGULAR);
}

function in_array_r($needle, $haystack) {
    foreach ($haystack as $subArr) {
        if ( $needle == $subArr['color'] ) {
            return $subArr['img'];
        }
    }
    return false;
}

/**
 * Change number of products that are displayed per page (shop page)
 */
add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );
function new_loop_shop_per_page( $cols ) {
  $cols = 60;
  return $cols;
}

add_filter( 'woocommerce_product_tabs', 'ullapopken_remove_product_tabs', 98 );
function ullapopken_remove_product_tabs( $tabs ) { 
    unset( $tabs['reviews'] );
    unset( $tabs['additional_information'] );
    return $tabs;
}

add_filter( 'woocommerce_output_related_products_args', 'ullapopken_related_products_args', 20 );
function ullapopken_related_products_args( $args ) {
	$args['posts_per_page'] = 20;
	return $args;
}

add_filter( 'woocommerce_upsell_display_args', 'wc_change_number_related_products', 20 );
function wc_change_number_related_products( $args ) {
	$args['posts_per_page'] = 20;
	return $args;
}

function custom_track_product_view() {
    if ( ! is_singular( 'product' ) ) {
        return;
    }

    global $post;

    if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) )
        $viewed_products = array();
    else
        $viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] );

    if ( ! in_array( $post->ID, $viewed_products ) ) {
        $viewed_products[] = $post->ID;
    }

    if ( sizeof( $viewed_products ) > 20 ) {
        array_shift( $viewed_products );
    }

    // Store for session only
    wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
}

function wc_recent_viewed_products(){

    $viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
    $viewed_products = array_filter( array_map( 'absint', $viewed_products ) );

    if( $viewed_products && !empty($viewed_products) ):
?>
<h2>Recently viewed</h2>
<div class="productSlider">

	<?php foreach ( $viewed_products as $viewedID ) : ?>

		<?php
		$post_object = get_post( $viewedID );

		setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

		wc_get_template_part( 'content', 'productSlider' );
		?>

	<?php endforeach; ?>

</div>
<?php
    endif;

}

add_action('woocommerce_single_product_summary', 'ullapopken_add_cart_single_response' , 70);
function ullapopken_add_cart_single_response(){
    global $product;
?>
<div id="addToCartResponseSucess" class="productresponse" style="display:none;max-width:500px;">
    <h3 class="text-center">Great, you have placed the item in your shopping bag!</h3>
    <div class="productDetails">
        <?php
            if( $product->is_type( 'variable' ) ) {
                foreach ($product->get_available_variations() as $variation) {
                    $variationProduct = wc_get_product($variation['variation_id']);
                    $variationAtrrs = $variationProduct->get_variation_attributes();
                    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $variation['variation_id'] ), 'single-post-thumbnail' );
                    ?>
                        <div class="productDetail" data-variation="<?php echo $variation['variation_id']; ?>" style="display:none;">
                            <div class="productImg">
                                <?php if( $image ): ?>
                                    <img src="<?php  echo $image[0]; ?>" />
                                <?php endif; ?>
                            </div>
                            <div class="detail">
                                <h3><?php echo get_the_title($product->get_id()); ?></h3>
                                <table>
                                    <?php
                                        foreach( $variationAtrrs as $attr_name => $attr ): 
                                            $taxonmomy = str_replace( 'attribute_', '', $attr_name );
                                    ?>
                                        <tr>
                                            <td><?php echo wc_attribute_label( $taxonmomy ); ?> : </td>
                                            <td><?php echo $attr; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                                <div class="price text-left">
                                    <?php echo $variationProduct->get_price_html(); ?>
                                </div>
                            </div>
                        </div>
                    <?php
                }
            }
        ?>
    </div>
    <div class="modalFooter d-flex justify-content-between">
        <a href="#" class="close">continue shopping</a>
        <a href="<?php echo wc_get_cart_url(); ?>" class="btn">check out now</a>
    </div>
</div>
<?php
}

add_action('woocommerce_after_add_to_cart_form', 'ullapopken_size_cart' , 70);
function ullapopken_size_cart(){
    ?>
    <div id="sizeChartContent" style="display:none;">
        <table class="table border-0">
            <thead>
                <tr>
                    <th class="border-0" scope="col">Size finder:</th>
                    <th class="border-0" scope="col"><span class="sizeTypeClick" data-id="#womenChart">Women</span></th>
                    <th class="border-0" scope="col"><span class="sizeTypeClick" data-id="#menChart">Men</span></th>
                </tr>
            </thead>
        </table>

        <div class="chartsWrap" id="womenChart">
            <h5 class="text-center">Size Chart - Women</h5>
            <?php if ( have_rows( 'women_size_carts', 'option' ) ) : while ( have_rows( 'women_size_carts', 'option' ) ) : the_row(); ?>
                <div class="cart">
                    <h6><?php the_sub_field( 'title' ); ?></h6>
                    <span class="subtitle"><?php the_sub_field( 'sub_title' ); ?></span>
                    <div class="table-responsive"><?php the_sub_field( 'chart' ); ?></div>
                </div>
            <?php endwhile; endif; ?>
        </div>

        <div class="chartsWrap" id="menChart">
            <h5 class="text-center">Size chart Men</h5>
                <?php if ( have_rows( 'men_size_carts', 'option' ) ) : while ( have_rows( 'men_size_carts', 'option' ) ) : the_row(); ?>
                    <div class="cart">
                        <h6><?php the_sub_field( 'title' ); ?></h6>
                        <span class="subtitle"><?php the_sub_field( 'sub_title' ); ?></span>
                        <div class="table-responsive"><?php the_sub_field( 'chart' ); ?></div>
                    </div>
                <?php endwhile; endif; ?>
        </div>

    </div>
    <?php
}

add_filter( 'woocommerce_add_to_cart_fragments', 'header_add_to_cart_fragment', 30, 1 );
function header_add_to_cart_fragment( $fragments ) {
    global $woocommerce;
    ob_start();
    ?>
    <a class="nav-link cartIcon" href="<?php echo wc_get_cart_url(); ?>">
        <span class="cartCount"><?php echo $woocommerce->cart->cart_contents_count; ?></span>
        <i class="fas fa-shopping-bag"></i><span>Bag</span>
    </a>
    <?php
    $fragments['a.nav-link.cartIcon'] = ob_get_clean();

    return $fragments;
}

add_action( 'wp_ajax_woocommerce_add_to_cart_variable_rc', 'woocommerce_add_to_cart_variable_rc_callback' );	
function woocommerce_add_to_cart_variable_rc_callback() {
    $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
    $quantity = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
    $variation_id = $_POST['variation_id'];
    $variation  = $_POST['variation'];
    $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

    if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation  ) ) {
        do_action( 'woocommerce_ajax_added_to_cart', $product_id );
        if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
            wc_add_to_cart_message( $product_id );
        }

        // Return fragments
        WC_AJAX::get_refreshed_fragments();
    } else {
        WC_AJAX::json_headers();

        // If there was an error adding to the cart, redirect to the product page to show any errors
        $data = array(
            'error' => true,
            'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
        );
        echo json_encode( $data );
    }
    die();
}