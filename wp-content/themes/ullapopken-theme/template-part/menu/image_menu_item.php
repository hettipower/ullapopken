<?php 
$title = get_sub_field( 'title' ); 
?>
<div class="submenu-item img-menus">
    <h4><?php echo $title; ?></h4>
    <?php if ( have_rows( 'image_menu_items' ) ) : ?>
        <div class="imgMenuItemWrap">
        <?php while ( have_rows( 'image_menu_items' ) ) : the_row(); ?>
            <?php 
                $image = get_sub_field( 'image' );
                $link = get_sub_field( 'link' );
                if ( $image ) { 
            ?>
                <div class="img">
                    <a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>"></a>
                    <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
                    <h6 class="title"><?php echo $link['title']; ?></h6>
                </div>
            <?php } ?>
        <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>