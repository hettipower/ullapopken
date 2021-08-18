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