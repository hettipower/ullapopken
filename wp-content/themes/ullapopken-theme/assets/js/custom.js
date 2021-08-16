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

});