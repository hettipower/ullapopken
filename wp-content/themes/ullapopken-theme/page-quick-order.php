<?php 
/* Template Name: Quick Order */ 
get_header();

?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<section class="sectionWrap" id="quickOrderWrap">
	<div class="container">
        <div class="contentWrap">
            <h1><?php the_title(); ?></h1>
            <?php the_content(); ?>
        </div>
        <div class="quickOrderFrom">

            <form id="quickOrderFrom" class="repeater" method="post">

                <input type="hidden" name="bag_ids" id="bag_ids" value="" />
                <input type="hidden" name="bag_qty" id="bag_qty" value="" />

                <div class="repeaterItem">
                    <div class="input-group">
                        <input type="text" class="form-control" name="item" placeholder="Item #" aria-label="Item #" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" onKeyUp="sku_type(this)" />
                        <span class="input-group-text" onclick="get_product_by_sku(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="productItem"></div>
                </div>

                <div class="repeaterItem">
                    <div class="input-group">
                        <input type="text" class="form-control" name="item" placeholder="Item #" aria-label="Item #" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" onKeyUp="sku_type(this)" />
                        <span class="input-group-text" onclick="get_product_by_sku(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="productItem"></div>
                </div>

                <div class="repeaterItem">
                    <div class="input-group">
                        <input type="text" class="form-control" name="item" placeholder="Item #" aria-label="Item #" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" onKeyUp="sku_type(this)" />
                        <span class="input-group-text" onclick="get_product_by_sku(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="productItem"></div>
                </div>

                <div class="repeaterItem">
                    <div class="input-group">
                        <input type="text" class="form-control" name="item" placeholder="Item #" aria-label="Item #" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" onKeyUp="sku_type(this)" />
                        <span class="input-group-text" onclick="get_product_by_sku(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="productItem"></div>
                </div>

                <div data-repeater-list="quickOrder">
                    <div data-repeater-item class="repeaterItem">
                        <div class="input-group">
                            <input type="text" class="form-control" name="item" placeholder="Item #" aria-label="Item #" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" onKeyUp="sku_type(this)" />
                            <span class="input-group-text" onclick="get_product_by_sku(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                            </span>
                        </div>
                        <div class="productItem"></div>
                    </div>
                </div>

                <button data-repeater-create type="button" class="btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    add more
                </button>

                <div class="btnsWrap">
                    <button class="btn" disabled type="submit">Add to Bag</button>
                </div>
            </form>

        </div>
	</div>
</section>

<div id="addtoCartPop" style="display:none;">
    <h3>Great, you have moved <span class="count">1</span> items into your cart!</h3>
    <div class="innerAddtoBag"></div>
</div>

<?php endwhile; endif; ?>

<?php 
get_footer();