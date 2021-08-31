<?php 
$promotion = get_field( 'promotion', 'option' ); 
?>
<footer>
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