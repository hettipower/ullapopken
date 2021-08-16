<?php 
$promotion = get_field( 'promotion', 'option' ); 
?>
<footer>
    <?php if( $promotion ): ?>
        <div class="promotionWrap">
            <div class="container">
                <?php echo $promotion; ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if ( have_rows( 'services', 'option' ) ) : ?> 
	<div class="serviceWrap">
        <div class="container d-flex justify-content-between">
            <?php while ( have_rows( 'services', 'option' ) ) : the_row(); ?>
                <div class="service"><i class="fas fa-certificate"></i> <?php the_sub_field( 'service' ); ?></div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="middleWrap">
        <div class="container d-flex justify-content-between">
            <div class="footerBoxWrap">
                <?php if ( is_active_sidebar( 'footer-widget-1' ) ) : ?>
					<?php dynamic_sidebar( 'footer-widget-1' ); ?>
				<?php endif; ?>
            </div>
            <div class="footerBoxWrap">
                <?php if ( is_active_sidebar( 'footer-widget-2' ) ) : ?>
					<?php dynamic_sidebar( 'footer-widget-2' ); ?>
				<?php endif; ?>
            </div>
            <div class="footerBoxWrap">
                <h3 class="widget-title">Contact us</h3>
                <div class="tele"><?php the_field( 'telephone', 'option' ); ?></div>
                <div class="hour"><?php the_field( 'open_hour', 'option' ); ?></div>
                <div class="email"><?php the_field( 'email', 'option' ); ?></div>
            </div>
        </div>
    </div>
    <div class="bottomWrap">
        <div class="container d-flex justify-content-between">
            <div class="quickMenu">
                <?php
                    $defaults = array(
                        'menu'            => 'Quick Links',
                        'container'       => false,
                        'menu_class'      => 'menu',
                        'echo'            => true,
                        'fallback_cb'     => 'wp_page_menu',
                        'items_wrap'      => '<ul id="%1$s" class="%2$s navbar-nav">%3$s</ul>',
                        'depth'           => 0
                    );
                    wp_nav_menu( $defaults );
                ?>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>
</html>