<header id="mobileHeader">
    <nav class="navbar navbar-expand-md">
        <div class="topnavvbar">
            <div class="container d-flex justify-content-end">
                <div class="menuWrap">
                    <?php
                        $defaults = array(
                            'menu'            => 'Top Menu',
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
        <div class="middleWrap">
            <div class="container d-flex justify-content-between flex-wrap">
                <div class="menuIcon">
                    <button id="menuToggler" class="btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                    </button>
                </div>
                <a class="navbar-brand" href="<?php echo home_url(); ?>">
                    <?php
                        $site_logo = get_field( 'site_logo', 'option' ); 
                        if( $site_logo ): 
                    ?>
                        <img src="<?php echo $site_logo['url']; ?>" alt="<?php echo $site_logo['alt']; ?>" />
                    <?php endif; ?>
                </a>
                <ul class="secondMenuWrap navbar-nav">
                    <?php 
                        global $woocommerce;
                        $myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
                        if (is_user_logged_in()) { 
                            $fullName = get_user_meta( get_current_user_id(), 'first_name', true ).' '.get_user_meta( get_current_user_id(), 'last_name', true );
                    ?>
                        <li class="nav-item dropdown myAccount">
                            <a class="nav-link dropdown-toggle" href="#" id="myaccountDropDown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="far fa-user"></i><span>My Account</span></a>
                            <div class="dropdown-menu" aria-labelledby="myaccountDropDown">
                                <span class="close">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                        <path d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"></path>
                                    </svg>
                                </span>
                                <div class="innerDropDown">
                                    <h3>Welcome <?php echo $fullName; ?>!</h3>
                                    <hr>
                                    <a class="dropdown-item" href="<?php echo home_url('my-account/edit-account'); ?>">Personal data</a>
                                    <a class="dropdown-item" href="<?php echo home_url('my-account/orders'); ?>">Orders</a>
                                    <a class="dropdown-item" href="<?php echo home_url('my-account/edit-address'); ?>">Adresses</a>

                                    <a href="<?php echo wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>" class="btn">Sign Out</a>
                                </div>
                            </div>
                        </li>
                    <?php } else{ ?>
                        <li class="nav-item dropdown myAccount">
                            <!-- <a class="nav-link" href="<?php echo get_permalink( $myaccount_page_id ); ?>"><i class="far fa-user"></i><span>Sign In</span></a> -->
                            <a  class="nav-link dropdown-toggle" href="#" id="loginDropDown" role="button"><i class="far fa-user"></i><span>Sign In</span></a>
                            <div class="dropdown-menu" aria-labelledby="loginDropDown">
                                <span class="close">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                        <path d="M1.293 1.293a1 1 0 0 1 1.414 0L8 6.586l5.293-5.293a1 1 0 1 1 1.414 1.414L9.414 8l5.293 5.293a1 1 0 0 1-1.414 1.414L8 9.414l-5.293 5.293a1 1 0 0 1-1.414-1.414L6.586 8 1.293 2.707a1 1 0 0 1 0-1.414z"></path>
                                    </svg>
                                </span>
                                <div class="innerDropDown">
                                    <h3>My Ulla Popken Account</h3>
                                    <hr>
                                    <form class="woocommerce-form woocommerce-form-login login needs-validation" method="post" novalidate>

                                        <?php do_action( 'woocommerce_login_form_start' ); ?>

                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <label for="username"><?php esc_html_e( 'E-Mail', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                                            <input type="text" class="woocommerce-Input woocommerce-Input--text input-text form-control" name="username" id="username" required autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" placeholder="E-Mail" /><?php // @codingStandardsIgnoreLine ?>
                                        </p>
                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
                                            <input class="woocommerce-Input woocommerce-Input--text input-text form-control" type="password" name="password" id="password" autocomplete="current-password" required placeholder="Password" />
                                        </p>

                                        <?php do_action( 'woocommerce_login_form' ); ?>

                                        <p class="form-row">
                                            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                                                <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
                                            </label>
                                            <?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
                                            <button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Sign In', 'woocommerce' ); ?>"><?php esc_html_e( 'Sign In', 'woocommerce' ); ?></button>
                                        </p>
                                        <p class="woocommerce-LostPassword lost_password">
                                            <a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
                                        </p>

                                        <?php do_action( 'woocommerce_login_form_end' ); ?>

                                    </form>

                                    <div class="registrationWrap">
                                        <h4>New at Ulla Popken?</h4>

                                        <a href="<?php echo home_url('my-account'); ?>" class="btn">Register</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link cartIcon" href="<?php echo wc_get_cart_url(); ?>">
                            <span class="cartCount"><?php echo $woocommerce->cart->cart_contents_count; ?></span>
                            <i class="fas fa-shopping-bag"></i><span>Bag</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="bottomWrap">
            <div class="container d-flex justify-content-between">
                <div class="searchWrap">
                    <form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
                        <div class="input-group">
                            <input 
                                type="search" 
                                class="form-control"
                                placeholder="<?php echo esc_attr_x( 'Search', 'placeholder' ) ?>"
                                value="<?php echo get_search_query() ?>" name="s" 
                            >
                            <span class="input-group-text">
                                <button type="submit"><i class="fas fa-search"></i></button>
                            </span>
                        </div>
                        <input type="hidden" value="product" name="post_type" id="post_type" />
                    </form>
                </div>
            </div>
        </div>
    </nav>
</header>