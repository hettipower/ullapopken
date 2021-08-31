<style>
.section{
    margin-left: -20px;
    margin-right: -20px;
    font-family: "Raleway",san-serif;
}
.section h1{
    text-align: center;
    text-transform: uppercase;
    color: #808a97;
    font-size: 35px;
    font-weight: 700;
    line-height: normal;
    display: inline-block;
    width: 100%;
    margin: 50px 0 0;
}
.section ul{
    list-style-type: disc;
    padding-left: 15px;
}
.section:nth-child(even){
    background-color: #fff;
}
.section:nth-child(odd){
    background-color: #f1f1f1;
}
.section .section-title img{
    display: table-cell;
    vertical-align: middle;
    width: auto;
    margin-right: 15px;
}
.section h2,
.section h3 {
    display: inline-block;
    vertical-align: middle;
    padding: 0;
    font-size: 24px;
    font-weight: 700;
    color: #808a97;
    text-transform: uppercase;
}

.section .section-title h2{
    display: table-cell;
    vertical-align: middle;
    line-height: 25px;
}

.section-title{
    display: table;
}

.section h3 {
    font-size: 14px;
    line-height: 28px;
    margin-bottom: 0;
    display: block;
}

.section p{
    font-size: 13px;
    margin: 25px 0;
}
.section ul li{
    margin-bottom: 4px;
}
.landing-container{
    max-width: 750px;
    margin-left: auto;
    margin-right: auto;
    padding: 50px 0 30px;
}
.landing-container:after{
    display: block;
    clear: both;
    content: '';
}
.landing-container .col-1,
.landing-container .col-2{
    float: left;
    box-sizing: border-box;
    padding: 0 15px;
}
.landing-container .col-1 img{
    width: 100%;
}
.landing-container .col-1{
    width: 55%;
}
.landing-container .col-2{
    width: 45%;
}
.premium-cta{
    background-color: #808a97;
    color: #fff;
    border-radius: 6px;
    padding: 20px 15px;
}
.premium-cta:after{
    content: '';
    display: block;
    clear: both;
}
.premium-cta p{
    margin: 7px 0;
    font-size: 14px;
    font-weight: 500;
    display: inline-block;
    width: 60%;
}
.premium-cta a.button{
    border-radius: 6px;
    height: 60px;
    float: right;
    background: url(<?php echo YITH_WCMS_ASSETS_URL?>/images/upgrade.png) #ff643f no-repeat 13px 13px;
    border-color: #ff643f;
    box-shadow: none;
    outline: none;
    color: #fff;
    position: relative;
    padding: 9px 50px 9px 70px;
}
.premium-cta a.button:hover,
.premium-cta a.button:active,
.premium-cta a.button:focus{
    color: #fff;
    background: url(<?php echo YITH_WCMS_ASSETS_URL?>/images/upgrade.png) #971d00 no-repeat 13px 13px;
    border-color: #971d00;
    box-shadow: none;
    outline: none;
}
.premium-cta a.button:focus{
    top: 1px;
}
.premium-cta a.button span{
    line-height: 13px;
}
.premium-cta a.button .highlight{
    display: block;
    font-size: 20px;
    font-weight: 700;
    line-height: 20px;
}
.premium-cta .highlight{
    text-transform: uppercase;
    background: none;
    font-weight: 800;
    color: #fff;
}

.section.one{
    background: url(<?php echo YITH_WCMS_ASSETS_URL?>/images/01-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.two{
    background: url(<?php echo YITH_WCMS_ASSETS_URL?>/images/02-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.three{
    background: url(<?php echo YITH_WCMS_ASSETS_URL?>/images/03-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.four{
    background: url(<?php echo YITH_WCMS_ASSETS_URL?>/images/04-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.five{
    background: url(<?php echo YITH_WCMS_ASSETS_URL?>/images/05-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section.six{
    background: url(<?php echo YITH_WCMS_ASSETS_URL?>/images/06-bg.png) no-repeat #fff; background-position: 85% 75%
}


@media (max-width: 768px) {
    .section{margin: 0}
    .premium-cta p{
        width: 100%;
    }
    .premium-cta{
        text-align: center;
    }
    .premium-cta a.button{
        float: none;
    }
}

@media (max-width: 480px){
    .wrap{
        margin-right: 0;
    }
    .section{
        margin: 0;
    }
    .landing-container .col-1,
    .landing-container .col-2{
        width: 100%;
        padding: 0 15px;
    }
    .section-odd .col-1 {
        float: left;
        margin-right: -100%;
    }
    .section-odd .col-2 {
        float: right;
        margin-top: 65%;
    }
}

@media (max-width: 320px){
    .premium-cta a.button{
        padding: 9px 20px 9px 70px;
    }

    .section .section-title img{
        display: none;
    }
}
</style>
<div class="landing">
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Multi-step Checkout%2$s to benefit from all features!','yith_wcms'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith_wcms');?></span>
                    <span><?php _e('to the premium version','yith_wcms');?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="one section section-even clear">
        <h1><?php _e('Premium Features','yith_wcms');?></h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCMS_ASSETS_URL?>/images/01.png" alt="Display styles" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCMS_ASSETS_URL?>/images/01-icon.png" alt="icon 01"/>
                    <h2><?php _e('Display styles','yith_wcms');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('4 different types to shape your site checkout steps. %1$s4 different layouts%2$s  to let you find the best solution for you and the most suitable for the graphic style of your pages.%3$sBut, this is not all! Opt for %1$svertical display%2$s if you want to show all steps as if they were all elements belonging to a sidebar.', 'yith_wcms'), '<b>', '</b>','<br>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="two section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCMS_ASSETS_URL?>/images/02-icon.png" alt="icon 02" />
                    <h2><?php _e('Custom layout','yith_wcms');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Select your favourite %1$sstyle%2$s for your the steps of your checkout page and customise it in every detail thanks to the many options made available by thepremium version of the plugin.%3$sAnd in case %1$slabels%2$s associated to the steps were not what you need, you will be able to change them from plugin option panel. Useful, easy and quick!', 'yith_wcms'), '<b>', '</b>','<br>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCMS_ASSETS_URL?>/images/02.png" alt="Custom layout" />
            </div>
        </div>
    </div>
    <div class="three section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCMS_ASSETS_URL?>/images/03.png" alt="Shipping" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCMS_ASSETS_URL?>/images/03-icon.png" alt="icon 03" />
                    <h2><?php _e( 'Icon or number','yith_wcms');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Unless you have chosen textual style, each step of your checkout will be marked by a distinctive number or icon. In the latter case, you can either use icons made availale by the plugin or upload your own custom icons. Another way to %1$smake your checkout page more intuitive%2$s for your customers.', 'yith_wcms'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="four section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCMS_ASSETS_URL?>/images/04-icon.png" alt="icon 04" />
                    <h2><?php _e('Ajax validation','yith_wcms');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Anyone willing to purchase from your shop will have to add certain specific data concerning their own profile and payment method they prefer. Activate %1$sajax validation%2$s if you want that your customers can access the following step only if they have first entered all information correctly in the previous step.', 'yith_wcms'), '<b>', '</b>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCMS_ASSETS_URL?>/images/04.png" alt="Ajax validation" />
            </div>
        </div>
    </div>
    <div class="five section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_WCMS_ASSETS_URL?>/images/05.png" alt="Navigation buttons" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCMS_ASSETS_URL?>/images/05-icon.png" alt="icon 05" />
                    <h2><?php _e('Navigation buttons','yith_wcms');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Navigation buttons can help users realise they are in a checkout process made of more than one step.%3$sYet, if you think they are superfluous, nothing prevents you from using the specific option to hide them. Anyway, the %1$sstate of process%2$s will be visible and each step will be accessible from the timeline.','yith_wcms'),'<b>','</b>','<br>'); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="six section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_WCMS_ASSETS_URL?>/images/06-icon.png" alt="icon 06" />
                    <h2><?php _e('“My account” and “Order received” pages','yith_wcms');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'A section of your option panel devoted to customisation of color patterns of “My account” and “Order received” pages, or better the page where users are redirected after completing purchase.%3$s%1$sYITH WooCommerce Multi-step Checkout%2$s has thought of everything!','yith_wcms' ),'<b>','</b>','<br>' ) ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_WCMS_ASSETS_URL?>/images/06.png" alt="“My account” and “Order received” pages" />
            </div>
        </div>
    </div>    
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Multi-step Checkout%2$s to benefit from all features!','yith_wcms'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith_wcms');?></span>
                    <span><?php _e('to the premium version','yith_wcms');?></span>
                </a>
            </div>
        </div>
    </div>
</div>
