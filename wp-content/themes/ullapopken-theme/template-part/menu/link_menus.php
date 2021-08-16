<?php 
$title = get_sub_field( 'title' ); 
?>
<div class="submenu-item link-menus">
    <h4><?php echo $title; ?></h4>
    <?php if ( have_rows( 'menu_items' ) ) : ?>
        <ul class="navbar-nav">
        <?php while ( have_rows( 'menu_items' ) ) : the_row(); ?>
            <?php 
                $menu_item = get_sub_field( 'menu_item' );
                if ( $menu_item ) { 
            ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $menu_item['url']; ?>" target="<?php echo $menu_item['target']; ?>"><?php echo $menu_item['title']; ?></a>
                </li>
            <?php } ?>
        <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</div>