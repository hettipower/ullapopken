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

<div class="submenuWrap">
    <a href="#" class="close">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
            <path d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"/>
        </svg>
    </a>
<?php
    $i = 1; 
    if ( have_rows( 'menu_layout' , $menuID ) ) {
        while ( have_rows( 'menu_layout' , $menuID ) ) : the_row();
            $submenu_column = get_sub_field( 'submenu_column' );
    ?>
        <div class="submenuItem <?php echo $submenu_column; ?>" id="menu-<?php echo $menuID; ?>-<?php echo $i; ?>">
            <?php 
                if ( have_rows( 'sub_menu_layout' ) ): while ( have_rows( 'sub_menu_layout' ) ) : the_row();

                    if ( get_row_layout() == 'link_menus' ) {
                        get_template_part( 'template-part/menu/link_menus' );
                    }

                    if ( get_row_layout() == 'image_menu_item' ) {
                        get_template_part( 'template-part/menu/image_menu_item' );
                    }

                endwhile; endif; 
            ?>
        </div>
    <?php
        $i++;
        endwhile;
    }
?>
</div>