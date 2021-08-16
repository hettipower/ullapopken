<?php
$women_menu = get_field( 'women_menu', 'option' );
$men_menu = get_field( 'men_menu', 'option' );
$getShopCookie = theme_get_cookie('shop');

if( $getShopCookie == 'women' ) {
    $menuID = $women_menu;
} else if( $getShopCookie == 'men' ) {
    $menuID = $men_menu;
} else {
    $menuID = $women_menu;
}
?>

<?php
    $i = 1; 
    if ( have_rows( 'menu_layout' , $menuID ) ) {
        echo '<ul class="navbar-nav">';
        while ( have_rows( 'menu_layout' , $menuID ) ) : the_row();
            $parent_menu_item = get_sub_field( 'parent_menu_item' );
            $custom_class = get_sub_field( 'custom_class' );
            $custom_id = get_sub_field( 'custom_id' );
            if( $parent_menu_item ) {
                echo '<li class="nav-item">';
                    echo '<a data-menu="menu-'.$menuID.'-'.$i.'" class="nav-link '.$custom_class.'" href="#" id="'.$custom_id.'">'.$parent_menu_item['title'].'</a>';
                echo '</li>';
            }
        $i++;
        endwhile;
        echo '</ul>';
    }
?>