<?php
function theme_set_cookie( $name , $value , $time = '86400' ){
  setcookie($name, $value, time() + 86400, "/");
}

function theme_get_cookie( $name ){
  if( isset($_COOKIE[$name]) ){
    return $_COOKIE[$name];
  }

  return false;
}

add_action( 'admin_post_theme_set_shop_cookie_func', 'theme_set_shop_cookie_func' );
add_action( 'admin_post_nopriv_theme_set_shop_cookie_func', 'theme_set_shop_cookie_func' );
function theme_set_shop_cookie_func() {
  
  $url = $_REQUEST['url'];
  $shop = $_REQUEST['shop'];

  theme_set_cookie( 'shop' , $shop );

  wp_redirect($url);
  exit();

}

function store_payment_icons($payment) {

  switch ($payment) {
    case 'Cash':
      return THEME_IMAGES.'/CASH.png';
      break;
    
    case 'DEBIT':
      return THEME_IMAGES.'/DEBIT.svg';
      break;
    
    case 'EC card':
      return THEME_IMAGES.'/EC.png';
      break;
    
    case 'Maestro':
      return THEME_IMAGES.'/MAESTRO.png';
      break;
    
    case 'MasterCard':
      return THEME_IMAGES.'/MASTERCARD.svg';
      break;
    
    case 'NFC':
      return THEME_IMAGES.'/NFC.svg';
      break;
    
    case 'Visa':
      return THEME_IMAGES.'/VISA.png';
      break;
    
    case 'Visa Electron':
      return THEME_IMAGES.'/VISAELECTRON.svg';
      break;
    
    default:
      return '';
      break;
  }

}

function stores_data(){

  $dataArr = array();

  $storesQuery = new WP_Query(
    array(
        'post_type' => 'store',
        'posts_per_page' => -1
    )
  );
  if ( $storesQuery->have_posts() ) : while ( $storesQuery->have_posts() ) : $storesQuery->the_post();
    $address = get_field( 'address' );
    $gallery_images = get_field( 'gallery' );

    $item = array(
      'address' => $address['address'],
      'title' => get_the_title(),
      'image' => $gallery_images[0]['url'],
      'ID' => get_the_ID(),
      'link' => get_the_permalink(),
      'telephone' => get_field( 'telephone' )
    );

    array_push($dataArr , $item);

  endwhile; wp_reset_postdata(); endif;

  return $dataArr;
}