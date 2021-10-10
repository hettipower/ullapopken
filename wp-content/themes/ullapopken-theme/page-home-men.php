<?php /* Template Name: Home Men */ ?>
<?php get_header(); 

$getShopCookie = theme_get_cookie('shop');

if( $getShopCookie == 'women' ) {
    $homePageID = get_field( 'women_home', 'option' );
} else if( $getShopCookie == 'men' ) {
    $homePageID = get_field( 'men_home', 'option' );
} else {
    $homePageID = get_field( 'women_home', 'option' );
}

?>

<?php get_footer();