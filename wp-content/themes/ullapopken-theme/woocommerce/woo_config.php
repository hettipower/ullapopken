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
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
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
    $imagesArr = array();

    if ( $product->is_type( 'variable' ) ) {

        $variations = $product->get_available_variations();

        if( $variations ) {
            foreach ( $variations as $variation ) {
                array_push($imagesArr , array(
                    'img' => $variation['image']['url'],
                    'color' => $variation['attributes']['attribute_pa_color']
                ));
            }
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
            } else {
                    $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
                ?>
                    <div class="productDetail singleProduct" style="display:none;">
                        <div class="productImg">
                            <?php if( $image ): ?>
                                <img src="<?php  echo $image[0]; ?>" />
                            <?php endif; ?>
                        </div>
                        <div class="detail">
                            <h3><?php echo get_the_title($product->get_id()); ?></h3>
                            <div class="price text-left">
                                <?php echo $product->get_price_html(); ?>
                            </div>
                        </div>
                    </div>
                <?php
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
add_action( 'wp_ajax_nopriv_woocommerce_add_to_cart_variable_rc', 'woocommerce_add_to_cart_variable_rc_callback' );	
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
        //WC_AJAX::json_headers();

        // If there was an error adding to the cart, redirect to the product page to show any errors
        $data = array(
            'error' => true,
            'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
        );
        echo json_encode( $data );
    }
    die();
}

add_filter('woocommerce_product_single_add_to_cart_text', 'woo_custom_cart_button_text',1);
function woo_custom_cart_button_text() {
    return __('Add to Bag', 'woocommerce');
}

function get_variation_data_from_variation_id( $item_id ) {
    $_product = new WC_Product_Variation( $item_id );
    $variation_data = $_product->get_variation_attributes();
    $variation_detail = wc_get_formatted_variation( $variation_data, true );
    
    return explode(',' , $variation_detail);
}

add_action( 'woocommerce_after_quantity_input_field', 'dc_quantity_plus_minus_sign' );
function dc_quantity_plus_minus_sign() {
   echo '<button type="button" class="plus" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
   <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
 </svg></button>';
   echo '<button type="button" class="minus" ><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
   <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
 </svg></button>';
}

add_filter( 'woocommerce_checkout_fields' , 'ullapopken_remove_checkout_fields' );
function ullapopken_remove_checkout_fields( $fields ) {

    unset($fields['billing']['billing_company']); 
    unset($fields['billing']['billing_address_2']); 
    unset($fields['billing']['billing_state']); 
    unset($fields['billing']['billing_phone']);

    unset($fields['shipping']['shipping_company']); 
    unset($fields['shipping']['shipping_address_2']);
    unset($fields['shipping']['shipping_state']);
    unset($fields['shipping']['shipping_phone']);
    
    return $fields; 
    
}

add_filter( 'woocommerce_default_address_fields' , 'custom_override_default_address_fields' );
function custom_override_default_address_fields( $address_fields ) {
    $address_fields['address_1']['label'] = 'Street';
    $address_fields['country']['label'] = 'Country';
    $address_fields['city']['label'] = 'City';
    $address_fields['postcode']['label'] = 'Zip Code';

    return $address_fields;
}

function get_product_count($termSlug = '') {

    $args = array( 
        'post_type' => 'product', 
        'post_status' => 'publish', 
        'posts_per_page' => -1,
        'tax_query' => array(),
    );

    if( $termSlug ) {
        $taxQuery = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $termSlug,
        );
        $args['tax_query'][] = $taxQuery;
    }

    $products = new WP_Query( $args );
    
    return $products->found_posts;

}

add_action( 'woocommerce_register_form_start', 'bbloomer_add_name_woo_account_registration' );
function bbloomer_add_name_woo_account_registration() {
    ?>
  
    <p class="form-row">
        <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text form-control" required name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
    </p>
  
    <p class="form-row">
        <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text form-control" required name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
    </p>
  
    <div class="clear"></div>
  
    <?php
}

add_filter( 'woocommerce_registration_errors', 'bbloomer_validate_name_fields', 10, 3 );
function bbloomer_validate_name_fields( $errors, $username, $email ) {
    if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
        $errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
        $errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
    }
    return $errors;
}

add_action( 'woocommerce_created_customer', 'bbloomer_save_name_fields' );
function bbloomer_save_name_fields( $customer_id ) {
    if ( isset( $_POST['billing_first_name'] ) ) {
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        update_user_meta( $customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']) );
    }
    if ( isset( $_POST['billing_last_name'] ) ) {
        update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
        update_user_meta( $customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']) );
    }
  
}

function get_product_by_sku( $sku ) {
    global $wpdb;
  
    $product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );
  
    if ( $product_id ) return wc_get_product( $product_id );
  
    return false;
}

add_action( 'wp_ajax_get_product_details_by_sku_ajax', 'get_product_details_by_sku_ajax' );
add_action( 'wp_ajax_nopriv_get_product_details_by_sku_ajax', 'get_product_details_by_sku_ajax' );	
function get_product_details_by_sku_ajax() {

    $results = array();
    $html = '';

    $productSku = (isset($_POST['productSku'])) ? (int)$_POST['productSku'] : false;
    $productQty = (isset($_POST['productQty'])) ? $_POST['productQty'] : [];


    if( $productSku ) {
        $productDetail = get_product_by_sku( $productSku );
        //792855100

        if( $productDetail ){

            $parentProduct = wc_get_product( $productDetail->get_parent_id() );
            if( $parentProduct ) {
                $sizeAttr =  explode(',' , $parentProduct->get_attribute( 'pa_size' ));
            } else {
                $sizeAttr = false;
            }

            $html .= '<div class="itemDetailsWrap">
                <div class="skuWrap">
                    <span class="sku"># '.$productSku.'</span>
                    <span class="close" onclick="clearProductItems(this , '.$productDetail->get_id().')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                            <path d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"></path>
                        </svg>
                    </span>
                </div>
                <div class="productDetail">
                    <div class="productImage">'.$productDetail->get_image().'</div>
                    <div class="details">
                        <h3><a href="'.get_permalink( $productDetail->get_id() ).'">'.$productDetail->get_name().'</a></h3>
                        <div class="attributes">
                            <div class="attr">
                                <div class="qty">
                                    <label>Qty</label>
                                    <select class="qtySelect form-select" onchange="quick_order_qty_change(this , '.$productDetail->get_id().')">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </div>';
                                if( $sizeAttr ) {
                                    $html .= '<div class="size">
                                        <label>Size</label>
                                        <select class="sizeSelect form-select">';
                                            foreach( $sizeAttr as $size ) {
                                                $html .= '<option value="'.$size.'">'.$size.'</option>';
                                            }
                                        $html .= '</select>
                                    </div>';
                                }
                            $html .= '</div>
                            <div class="price">'.$productDetail->get_price_html().'</div>
                        </div>
                        <div class="shipping">Sofort lieferbar - In 3-5 Werktagen bei dir</div>
                    </div>
                </div>
            </div>';

            $results['productID'] = $productDetail->get_id();
            
            $productnewQty = array();
            if( $productQty ) {
                foreach( $productQty as $qty ){
                    $qtyArr = explode(':' , $qty);
                    if( $qtyArr[0] == $productDetail->get_id() ) {
                        $qtyArr[1]++;
                    }
                    array_push($productnewQty , implode(':',$qtyArr));
                }
            } else {
                $productnewQty = array( $productDetail->get_id().':1'  );
            }

            $results['productQty'] = $productnewQty;
        }
    }

    $results['html'] = $html;

    echo json_encode( $results );
    die();

}

add_action( 'wp_ajax_wc_add_to_bag_rc', 'wc_add_to_bag_rc_callback' );
add_action( 'wp_ajax_nopriv_wc_add_to_bag_rc', 'wc_add_to_bag_rc_callback' );	
function wc_add_to_bag_rc_callback() {

    $product_ids = (isset($_POST['product_ids'])) ? $_POST['product_ids'] : false;
    $quantity = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
    $variation_id = '';
    $variation  = '';

    $productQtyArr = (isset($_POST['productQty'])) ? $_POST['productQty'] : false;

    if( $productQtyArr ) {
        foreach( $productQtyArr as $productQty ){
            $productsArr = explode(":" , $productQty);

            $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $productsArr[0] ) );
            $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $productsArr[0], $productsArr[1] );

            if ( $passed_validation && WC()->cart->add_to_cart( $productsArr[0], $productsArr[1], $variation_id, $variation  ) ) {
                do_action( 'woocommerce_ajax_added_to_cart', $productsArr[0] );
                if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
                    wc_add_to_cart_message( $productsArr[0] );
                }
        
                // Return fragments
                WC_AJAX::get_refreshed_fragments();
            } else {
                //WC_AJAX::json_headers();
        
                // If there was an error adding to the cart, redirect to the product page to show any errors
                $data = array(
                    'error' => true,
                    'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $productsArr[0] ), $productsArr[0] )
                );
                echo json_encode( $data );
            }
        }
    }
    
    die();
}