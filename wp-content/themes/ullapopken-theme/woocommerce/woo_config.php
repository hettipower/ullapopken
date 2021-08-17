<?php
remove_action('woocommerce_before_shop_loop' , 'woocommerce_result_count' , 20);
remove_action('woocommerce_before_shop_loop' , 'woocommerce_catalog_ordering' , 30);
remove_action('woocommerce_after_shop_loop_item' , 'woocommerce_template_loop_add_to_cart' , 10);
remove_action('woocommerce_before_shop_loop_item_title' , 'woocommerce_template_loop_product_thumbnail' , 10);
remove_action('woocommerce_before_shop_loop_item' , 'woocommerce_template_loop_product_link_open' , 10);

add_action('woocommerce_shop_loop_item_title' , 'woocommerce_template_loop_product_link_open' , 5);

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

    add_theme_support( 'wc-product-gallery-zoom' );
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
    global $product;
    $colors = wc_get_product_terms( $product->id, 'pa_color' );
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
            $colorCode = get_field( 'color', 'pa_color_'.$color->term_id );
            $active = ($i==1) ? 'active' : '' ;
            $imgUrl = in_array_r($color->slug , $imagesArr);
            echo '<div class="colorItem"><span class="'.$active.'" style="background-color:'.$colorCode.'" data-colorid="'.$color->term_id.'" data-img="'.$imgUrl.'"></span></div>';
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