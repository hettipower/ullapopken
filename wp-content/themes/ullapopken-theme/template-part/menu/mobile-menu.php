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
<div id="mobileMegaMenuWrap">
    <?php if ( have_rows( 'menu_layout' , $menuID ) ): ?>
        <ul class="main-menu-wrap">
            <?php 
                while ( have_rows( 'menu_layout' , $menuID ) ) : the_row(); 
                    $parent_menu_item = get_sub_field( 'parent_menu_item' );
                    $custom_class = get_sub_field( 'custom_class' );
                    $custom_id = get_sub_field( 'custom_id' );
                    $submenu_column = get_sub_field( 'submenu_column' );
            ?>
                <?php if( $parent_menu_item ): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $custom_class; ?>" href="#" id="<?php echo $custom_id; ?>"><?php echo $parent_menu_item['title']; ?></a>

                        <?php if ( have_rows( 'sub_menu_layout' ) ): ?>
                            <ul class="submenu-wrap">
                                <?php while ( have_rows( 'sub_menu_layout' ) ) : the_row(); ?>
                                        <?php 
                                        if ( have_rows( 'menu_items' ) ) : while ( have_rows( 'menu_items' ) ) : the_row();
                                            $menu_item = get_sub_field( 'menu_item' );
                                            if ( $menu_item ) {
                                    ?>
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo $menu_item['url']; ?>" target="<?php echo $menu_item['target']; ?>"><?php echo $menu_item['title']; ?></a>
                                        </li>
                                        <?php } ?>
                                    <?php endwhile; endif; ?>
                                <?php endwhile; ?>
                            </ul>
                        <?php endif; ?>

                    </li>
                <?php endif; ?>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</div>