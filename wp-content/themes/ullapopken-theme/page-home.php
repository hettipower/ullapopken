<?php /* Template Name: Home */ ?>
<?php get_header(); 

$getShopCookie = theme_get_cookie('shop');

if( $getShopCookie == 'women' ) {
    $homePageID = get_field( 'women_home', 'option' );
	get_template_part( 'template-part/home/women', 'home' );
} else if( $getShopCookie == 'men' ) {
    $homePageID = get_field( 'men_home', 'option' );
	get_template_part( 'template-part/home/men', 'home' );
} else {
    $homePageID = get_field( 'women_home', 'option' );
	get_template_part( 'template-part/home/women', 'home' );
}

?>

<?php get_footer();