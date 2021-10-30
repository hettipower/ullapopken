jQuery(document).ready(function($) {
    $('.submit-export')
        .find('a[href="#mmenu-settings"]')
        .on('click', function(e) {
            e.preventDefault();
            $('.wrap.mmenu-export').removeClass('mmenu-export');
        });
});
