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
        infinite: true,
        speed: 300,
        slidesToShow: 5,
        slidesToScroll: 4,
        centerMode: true,
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
        infinite: true,
        speed: 300,
        slidesToShow: 5,
        slidesToScroll: 4,
        centerMode: true,
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
        infinite: true,
        speed: 300,
        slidesToShow: 5,
        slidesToScroll: 4,
        centerMode: true,
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

});