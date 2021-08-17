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
                console.log('test' , $(this));
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
});