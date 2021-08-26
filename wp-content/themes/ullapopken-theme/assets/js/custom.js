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
        var serializedData = $(this).serialize();
        var $thisbutton = $(this).find('.single_add_to_cart_button');
        var serializedDataArr = getFormData($(this));
        var variation_id = serializedDataArr.variation_id;

        serializedData = serializedData+'&action=woocommerce_ajax_add_to_cart';

        $(document.body).trigger('adding_to_cart', [$thisbutton, serializedData]);

        $.ajax({
            type: 'post',
            url: CUSTOM_PARAMS.base_url + '/?wc-ajax=add_to_cart',
            data: serializedData,
            beforeSend: function (response) {
                $('form.cart').block({
                    message: null,
                    overlayCSS: {
                        cursor: 'none'
                    }
                });
            },
            complete: function (response) {
                $('form.cart').unblock();
            },
            success: function (response) {

                if (!response.success) {
                    
                    $('.productDetails .productDetail').hide();
                    $('.productDetails .productDetail').each(function (index, element) {
                        var variation = $(this).data('variation');
                        console.log('variation' , variation)
                        if( variation == variation_id ) {
                            $(this).show();
                        }
                    });
                    Fancybox.show([{ src: "#addToCartResponseSucess", type: "inline" }]);

                } else {
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
                }

                $(document.body).trigger('wc_fragment_refresh');
            },
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
        

});

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