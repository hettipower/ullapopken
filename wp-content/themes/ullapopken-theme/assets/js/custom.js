jQuery(document).ready(function ($) {
    
    $('.mainMenuWrap .navbar-nav .nav-link').on('click' , function(){

        var menuID = $(this).data('menu');

        $('.mainMenuWrap .navbar-nav .nav-link').removeClass('active');
        $(this).addClass('active');
        $('.menuContWrap .submenuWrap .submenuItem').css('display' , 'none');
        $('.menuContWrap').css('display' , 'block');
        $('#'+menuID).css('display' , 'flex');

        return false;

    });

    $('.menuContWrap .submenuWrap .close').on('click' , function(){

        $('.menuContWrap .submenuWrap .submenuItem').css('display' , 'none');
        $('.menuContWrap').css('display' , 'none');
        $('.mainMenuWrap .navbar-nav .nav-link').removeClass('active');

        return false;

    });

    $('.filterBtnsWrap .btn').on('click' , function(){

        var filter = $(this).data('filter');
        $('.pwf-filter-container .pwf-field-item').removeClass('pwf-collapsed-open').addClass('pwf-collapsed-close');
        $('.pwf-field-item .pwf-field-item-container').hide();

        $.each($('.pwf-filter-container .pwf-field-item'), function (indexInArray, valueOfElement) { 
            var itemKey = $(this).data('item-key');

            if( filter === itemKey ){
                $(this).addClass('pwf-collapsed-open').removeClass('pwf-collapsed-close');
                $(this).find('.pwf-field-item-container').show();
            }
        });

        $('.backdrop').addClass('active');
        $('.filtersWrapper').addClass('active');
    });

    $('.backdrop').on('click' , function(){
        $('.backdrop').removeClass('active');
        $('.filtersWrapper').removeClass('active');
    });

    $('.colorList .colorItem span').on('click' , function(){
        var imgUrl = $(this).data('img');
        $(this).parent().parent().parent().find('.imageList .imgItem').css('background-image' , 'url('+imgUrl+')');
        $(this).parent().parent().find('span').removeClass('active');
        $(this).addClass('active');
    });

    $('.relatedProductWrap .productSlider').slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 5,
        slidesToScroll: 4,
        centerMode: false,
        prevArrow : '<button type="button" class="slick-prev"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg></button>',
        nextArrow : '<button type="button" class="slick-next"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg></button>',
        responsive: [
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $('.upsaleProductWrap .productSlider').slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 5,
        slidesToScroll: 4,
        centerMode: false,
        prevArrow : '<button type="button" class="slick-prev"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg></button>',
        nextArrow : '<button type="button" class="slick-next"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg></button>',
        responsive: [
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $('.recentlyViewedWrap .productSlider').slick({
        dots: false,
        infinite: false,
        speed: 300,
        slidesToShow: 5,
        slidesToScroll: 4,
        centerMode: false,
        prevArrow : '<button type="button" class="slick-prev"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg></button>',
        nextArrow : '<button type="button" class="slick-next"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg></button>',
        responsive: [
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    });

    $('.variations .value .btn').on('click' , function(){
        var attrname = $(this).data('attrname');
        $('.selectAttributesWrap .backdrop').addClass('active');
        $('.selectAttributesWrap #'+attrname).addClass('active');

        return false;
    });

    $('.filtersWrapper .filterWrap .filterHeader .close').on('click' , function(){
        $('.backdrop').removeClass('active');
        $('.filtersWrapper').removeClass('active');
        return false;
    });

    var sizeVal = $('.variations .value #pa_size').val();
    if( sizeVal ) {
        $('.variations .value.pa_size .btn span').html(sizeVal);
    }

    $('.filtersWrapper .filterWrap .attrOptionsWrap .btn').on('click' , function(){
        var size = $(this).data('size');
        var attrname = $(this).data('attrname');
        $('.variations .value.pa_size .btn span').html(attrname);
        $('.variations .value #pa_size').val(size).change();
        $('.backdrop').removeClass('active');
        $('.filtersWrapper').removeClass('active');
        return false;
    });

    /* Custom Add to cart Functions */
    $('.single-product form.cart').on('submit', function (e) {

        e.preventDefault();
        var $thisbutton = $(this).find('.single_add_to_cart_button');

        var var_id = $(this).find( 'input[name=variation_id]' ).val();
		var product_id = $(this).find( 'input[name=product_id]' ).val();
		var quantity = $(this).find( 'input[name=quantity]' ).val();

        //attributes = [];
		item = {};
		
		$(this).find('select[name^=attribute]').each(function() {

			var attribute = $(this).attr("name");
			var attributevalue = $(this).val();
			
			item[attribute] = attributevalue;
		});

        var data = {
            action: 'woocommerce_add_to_cart_variable_rc',
            product_id: product_id,
            quantity: quantity,
            variation_id: var_id,
            variation: item
        };

        // Trigger event
        $(document.body).trigger('adding_to_cart', [$thisbutton, data]);

        $('form.cart').block({
            message: null,
            overlayCSS: {
                cursor: 'none'
            }
        });

        // Ajax action
        $.post( CUSTOM_PARAMS.ajax_url, data, function( response ) {

            if ( ! response )
                return;


            if ( response.error && response.product_url ) {
                window.location = response.product_url;
                return;
            }

            fragments = response.fragments;
            cart_hash = response.cart_hash;

            $('.productDetails .productDetail').hide();
            $('.productDetails .productDetail').each(function (index, element) {
                var variation = $(this).data('variation');
                if( variation == var_id ) {
                    $(this).show();
                } else if( $(this).hasClass('singleProduct') ) {
                    $(this).show();
                }
            });
            Fancybox.show([{ src: "#addToCartResponseSucess", type: "inline" }]);

            // Trigger event so themes can refresh other areas
            $('body').trigger( 'added_to_cart', [ fragments, cart_hash ] );
            $(document.body).trigger('wc_fragment_refresh');

            $('form.cart').unblock();
        });

        return false;
    });

    $('.modalFooter .close').on('click' , function(){
        Fancybox.close();
        return false;
    });

    Fancybox.bind('.sizeTypeClick');

    $('.sizeTypeClick').on('click' , function(){
        var eleID = $(this).data('id');
        $('.fancybox__slide').animate({
            scrollTop: $(eleID).offset().top - 150
        }, 2000);
        return false;
    });

    $('.scrollToDetails').on('click' , function(){
        scroll_anim('.productDescriptions');
        return false;
    });

    $('.cartItemsWrap .cartItems').on( 'click', 'button.plus, button.minus', function() {
 
        // Get current quantity values
        var qty = $( this ).closest( '.quantity' ).find( '.qty' );
        var val   = parseFloat(qty.val());
        var max = parseFloat(qty.attr( 'max' ));
        var min = parseFloat(qty.attr( 'min' ));
        var step = parseFloat(qty.attr( 'step' ));

        // Change the value if plus or minus
        if ( $( this ).is( '.plus' ) ) {
            if ( max && ( max <= val ) ) {
              qty.val( max );
            } else {
                qty.val( val + step );
            }
        } else {
            if ( min && ( min >= val ) ) {
              qty.val( min );
            } else if ( val > 1 ) {
              qty.val( val - step );
            }
        }
        qty.trigger('change');
         
    });

    $('.radioWrapper .form-check .form-check-label .form-check-input').change(function(){
        var curval = ($(this).val() === '0') ? false : true;
        $('body').trigger('update_checkout');
        $( 'div.shipping_address' ).hide();
        if ( curval ) {
            $( 'div.shipping_address' ).slideDown();
        }
    });

    $('.wcForm .wc-login-toggle h3').on('click' , function(){
        if( !$(this).parent().parent().hasClass('active') ) {
            $('.wcForm').removeClass('active');
            $('.wcForm').find('form').hide();
            $('.wcForm').find('.guestBtn').hide();
            $(this).parent().parent().addClass('active');
            $(this).parent().parent().find('form').slideToggle();
            $(this).parent().parent().find('.guestBtn').slideToggle();
        }
    });

    $('.wcForm .wc-login-toggle .icon').on('click' , function(){
        if( !$(this).parent().parent().hasClass('active') ) {
            $('.wcForm').removeClass('active');
            $('.wcForm').find('form').hide();
            $('.wcForm').find('.guestBtn').hide();
            $(this).parent().parent().addClass('active');
            $(this).parent().parent().find('form').slideToggle();
            $(this).parent().parent().find('.guestBtn').slideToggle();
        }
    });

    $('#loginDropDown').on('click' , function(e){
        e.preventDefault();
        $(this).parent().find('.dropdown-menu').toggleClass('show');
    });

    $('.dropdown-menu .close').on('click' , function(e){
        e.preventDefault();
        $(this).parent().toggleClass('show');
    });

    $('.repeater').repeater({
        initEmpty: true,
        isFirstItemUndeletable: true
    });

    $('#quickOrderFrom').on('submit', function (e) {

        e.preventDefault();
        var $thisbutton = $(this).find('.btnsWrap .btn');
        var productID = jQuery('#quickOrderFrom #bag_ids').val();
        var productQty = jQuery('#quickOrderFrom #bag_qty').val();
        const productIDArr = (productID.length > 0) ? productID.split(",") : [];
        const productQtyArr = (productQty.length > 0) ? productQty.split(",") : [];

        /* var var_id = $(this).find( 'input[name=variation_id]' ).val();
		var product_id = $(this).find( 'input[name=product_id]' ).val();
		var quantity = $(this).find( 'input[name=quantity]' ).val(); */

        var data = {
            action: 'wc_add_to_bag_rc',
            product_ids: productIDArr,
            quantity: '1',
            productQty: productQtyArr
        };

        // Trigger event
        $(document.body).trigger('adding_to_cart', [$thisbutton, data]);

        $('#quickOrderFrom').block({
            message: null,
            overlayCSS: {
                cursor: 'none'
            }
        });

        // Ajax action
        $.post( CUSTOM_PARAMS.ajax_url, data, function( response ) {

            if ( ! response )
                return;


            if ( response.error && response.product_url ) {
                window.location = response.product_url;
                return;
            }

            console.log('response' , response)

            fragments = response.fragments;
            cart_hash = response.cart_hash;

            $('.productItem').each(function (index, element) {
                $(this).html('');
            });

            $('.repeaterItem .input-group').each(function (index, element) {
                $(this)
                .show()
                .find('.form-control').val('');
            });

            Fancybox.show([{ src: "#addtoCartPop", type: "inline" }]);

            // Trigger event so themes can refresh other areas
            $('body').trigger( 'added_to_cart', [ fragments, cart_hash ] );
            $(document.body).trigger('wc_fragment_refresh');

            $('#quickOrderFrom').unblock();
            jQuery('#quickOrderFrom #bag_ids').val('');
            jQuery('#quickOrderFrom #bag_qty').val('');
        });

        return false;
    });

    $('#stores').select2({
        placeholder: "Search locations",
        allowClear: true
    });

    $( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
        $('#singleProductWrap .productDescriptions .woocommerce-tabs .panel .sku_wrapper .sku').html(variation.item_number);
        if( variation.image.url ) {
            $('#singleProductWrap .productDescriptions .woocommerce-tabs .panel .imgWrap img').remove();
            $('#singleProductWrap .productDescriptions .woocommerce-tabs .panel .imgWrap').append( '<img src="'+variation.image.url+'" />' );
        }
    } );

});

function clearProductItems(ele , productID , variationID){
    var repeaterItem = jQuery(ele).parents('.repeaterItem');
    repeaterItem.find('.input-group').show();
    repeaterItem.find('.productItem').html('');

    var productID = jQuery('#quickOrderFrom #bag_ids').val();
    var productIDArr = (productID.length > 0) ? productID.split(",") : [];
    var productQty = jQuery('#quickOrderFrom #bag_qty').val();
    const productQtyArr = (productQty.length > 0) ? productQty.split(",") : [];
    const productNewQtyArr = [];

    if( productQtyArr.length > 0 ){
        productQtyArr
        .map( qty => {
            const qtyArr = qty.split(":");

            if(parseInt(variationID)) {

                if( parseInt(qtyArr[0]) !== parseInt(productID) || parseInt(qtyArr[2]) !== parseInt(variationID) ) {
                    productNewQtyArr.push(qtyArr.join(':'));
                }
            } else {
                if( parseInt(qtyArr[0]) !== parseInt(productID) ) {
                    productNewQtyArr.push(qtyArr.join(':'));
                }
            }

            return false;
        })
    }

    jQuery('#quickOrderFrom #bag_qty').val(productNewQtyArr.join(','));

    var index = productIDArr.indexOf(productID , variationID.toString());

    if (index !== -1) {
        productIDArr.splice(index, 1);
    }
    
    jQuery('#quickOrderFrom #bag_ids').val(productIDArr.join(','));
}

function get_product_by_sku(ele) {

    var thisEle = jQuery(ele);
    var productSku = thisEle.parent().find('.form-control').val();
    var productItem = thisEle.parent().parent().find('.productItem');
    var notFound = '<div class="alert alert-warning d-flex align-items-center" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/></svg><div>Product not found</div></div>';
    var productID = jQuery('#quickOrderFrom #bag_ids').val();
    var productQty = jQuery('#quickOrderFrom #bag_qty').val();
    const productIDArr = (productID.length > 0) ? productID.split(",") : [];
    const productQtyArr = (productQty.length > 0) ? productQty.split(",") : [];

    if( productSku ) {
        var data = {
            action: 'get_product_details_by_sku_ajax',
            productSku : productSku,
            productQty : productQtyArr
        }

        jQuery.ajax({
            type: "POST",
            url: CUSTOM_PARAMS.ajax_url,
            data: data,
            dataType: "JSON",
            beforeSend: function (response) {
                productItem.addClass('loading');
                productItem.block({
                    message: null,
                    overlayCSS: {
                        cursor: 'none'
                    }
                });
            },
            complete: function (response) {
                productItem.removeClass('loading');
            },
            success: function (response) {
                //console.log('response' , response)
                if( response.html ) {
                    productItem.html(response.html);
                    thisEle.parent().parent().find('.input-group').hide().find('.form-control').val('');
                    jQuery('.quickOrderFrom .repeater .btnsWrap .btn').removeAttr('disabled');
                } else {
                    productItem.html(notFound);
                    jQuery('.quickOrderFrom .repeater .btnsWrap .btn').attr('disabled');
                }

                if( response.productID ) {
                    productIDArr.push(response.productID);
                    jQuery('#quickOrderFrom #bag_ids').val(productIDArr.join(','));
                }

                if( response.productQty ) {
                    jQuery('#quickOrderFrom #bag_qty').val(response.productQty.join(','));
                }

                productItem.unblock();
            }
        });
    }

}

function quick_order_qty_change(ele, productID , variationID) {
    var thisEle = jQuery(ele);
    var qtyVal = parseInt(thisEle.val());
    var productQty = jQuery('#quickOrderFrom #bag_qty').val();
    const productQtyArr = (productQty.length > 0) ? productQty.split(",") : [];
    const productNewQtyArr = [];
    var price = thisEle.attr('data-price');
    var currency = thisEle.attr('data-currency');

    if( productQtyArr.length > 0 ){
        productQtyArr
        .map( qty => {
            const qtyArr = qty.split(":");

            if(parseInt(variationID)) {
                if( parseInt(qtyArr[0]) === parseInt(productID) && parseInt(qtyArr[2]) === parseInt(variationID) ) {
                    qtyArr[1] = qtyVal;
                }
            } else {
                if( parseInt(qtyArr[0]) === parseInt(productID) ) {
                    qtyArr[1] = qtyVal;
                }
            }

            productNewQtyArr.push(qtyArr.join(':'));

            return false;
        })
    }

    var newPrice = price * qtyVal;

    thisEle.parent().parent().parent().find('.price').html('<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">'+currency+'</span>'+newPrice+'</bdi></span>');

    jQuery('#quickOrderFrom #bag_qty').val(productNewQtyArr.join(','));

}

function quick_order_size_change(ele, productID){
    var thisEle = jQuery(ele);
    var sizeVal = thisEle.val();
    var color = thisEle.attr('data-color');
    var variationID = thisEle.attr('data-variation');
    var productQty = jQuery('#quickOrderFrom #bag_qty').val();
    const productQtyArr = (productQty.length > 0) ? productQty.split(",") : [];
    const productNewQtyArr = [];
    jQuery('#notifications').html('');
    var productItem = thisEle.parents('.productItem');

    var data = {
        action: 'get_product_variation_details',
        size : sizeVal,
        color : color,
        productID : productID,
        variationID : variationID
    }

    jQuery.ajax({
        type: "POST",
        url: CUSTOM_PARAMS.ajax_url,
        data: data,
        dataType: "JSON",
        beforeSend: function (response) {
            productItem.block({
                message: null,
                overlayCSS: {
                    cursor: 'none'
                }
            });
        },
        success: function (response) {
            //console.log('response' , response);

            if( response.variationID ) {

                if( productQtyArr.length > 0 ){
                    productQtyArr
                    .map( qty => {
                        const qtyArr = qty.split(":");
            
                        if(parseInt(variationID)) {
                            if( parseInt(qtyArr[0]) === parseInt(productID) && parseInt(qtyArr[2]) === parseInt(variationID) ) {
                                qtyArr[0] = response.productID;
                                qtyArr[2] = response.variationID;
                            }
                        }
            
                        productNewQtyArr.push(qtyArr.join(':'));
            
                        return false;
                    })
                }

                thisEle.attr('data-variation' , response.variationID);

                thisEle.parent().parent().find('.color .colorSelect').attr('data-variation' , response.variationID);
            
                jQuery('#quickOrderFrom #bag_qty').val(productNewQtyArr.join(','));
            } else {
                jQuery('#notifications').html('<div class="alert alert-warning d-flex align-items-center" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/></svg><div>Product not found. Try Another.</div></div>');
            }

            thisEle.attr('data-size' , sizeVal);
            thisEle.attr('data-color' , color);

            thisEle.parent().parent().find('.color .colorSelect').attr('data-size' , sizeVal);
            thisEle.parent().parent().find('.color .colorSelect').attr('data-color' , color);

            productItem.unblock();
        }
    });

    
}

function quick_order_color_change(ele, productID){
    var thisEle = jQuery(ele);
    var colorVal = thisEle.val();
    var size = thisEle.attr('data-size');
    var variationID = thisEle.attr('data-variation');
    var productQty = jQuery('#quickOrderFrom #bag_qty').val();
    const productQtyArr = (productQty.length > 0) ? productQty.split(",") : [];
    const productNewQtyArr = [];
    jQuery('#notifications').html('');
    var productItem = thisEle.parents('.productItem');

    var data = {
        action: 'get_product_variation_details',
        size : size,
        color : colorVal,
        productID : productID,
        variationID : variationID
    }

    jQuery.ajax({
        type: "POST",
        url: CUSTOM_PARAMS.ajax_url,
        data: data,
        dataType: "JSON",
        beforeSend: function (response) {
            productItem.block({
                message: null,
                overlayCSS: {
                    cursor: 'none'
                }
            });
        },
        success: function (response) {
            //console.log('response' , response);

            if( response.variationID ) {

                if( productQtyArr.length > 0 ){
                    productQtyArr
                    .map( qty => {
                        const qtyArr = qty.split(":");
            
                        if(parseInt(variationID)) {
                            if( parseInt(qtyArr[0]) === parseInt(productID) && parseInt(qtyArr[2]) === parseInt(variationID) ) {
                                qtyArr[0] = response.productID;
                                qtyArr[2] = response.variationID;
                            }
                        }
            
                        productNewQtyArr.push(qtyArr.join(':'));
            
                        return false;
                    })
                }

                thisEle.attr('data-variation' , response.variationID);

                thisEle.parent().parent().find('.size .sizeSelect').attr('data-variation' , response.variationID);
            
                jQuery('#quickOrderFrom #bag_qty').val(productNewQtyArr.join(','));
            } else {
                jQuery('#notifications').html('<div class="alert alert-warning d-flex align-items-center" role="alert"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/></svg><div>Product not found. Try Another.</div></div>');
            }

            thisEle.attr('data-size' , size);
            thisEle.attr('data-color' , colorVal);
            
            thisEle.parent().parent().find('.size .sizeSelect').attr('data-size' , size);
            thisEle.parent().parent().find('.size .sizeSelect').attr('data-color' , colorVal);

            productItem.unblock();
        }
    });

    
}

function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    jQuery.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

function scroll_anim(ele) {
    jQuery('html, body').animate({
        scrollTop: jQuery(ele).offset().top
    }, 1000);
}

var timeout;

jQuery( function( $ ) {
	$('.woocommerce').on('change', 'input.qty', function(){

		if ( timeout !== undefined ) {
			clearTimeout( timeout );
		}

		timeout = setTimeout(function() {
			$("[name='update_cart']").trigger("click");
		}, 1000 ); // 1 second delay, half a second (500) seems comfortable too

	});
} );

(function( $ ) {
    "use strict";
    $( document.body ).on( "pwf_filter_js_ajax_done", function() {
        let pwfFilterSetting    = pwffilterVariables.filter_setting;
        let productsContainer   = pwfFilterSetting.products_container_selector;

        $('.wc_pro_count').html($(productsContainer+' .product').length);

    });
})(jQuery);

(function () {
    'use strict'
  
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
            }

            form.classList.add('was-validated')
        }, false)
    })
})()

function remove_array_value(arr) {
    var what, a = arguments, L = a.length, ax;
    while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax= arr.indexOf(what)) !== -1) {
            arr.splice(ax, 1);
        }
    }
    return arr;
}

function sku_type(ele){
    if(ele.value.length > 8){
        ele.value = ele.value.substring(0,8)
        ele.blur()
    }
}