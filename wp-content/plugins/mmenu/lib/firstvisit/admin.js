jQuery(document).ready(function($) {
    if (!$('.wrap.mmenu-setup').length) {
        return;
    }

    function showNextSection() {
        $submit.removeClass('import');

        $sections
            .filter('.import')
            .removeClass('show')
            .removeClass('fade')
            .find('textarea')
            .prop('disabled', true);

        $sections.filter('.show').addClass('typed');

        var $s = $sections
            .not('.show')
            .not('.import')
            .first()
            .addClass('show')
            .removeClass('typed');

        setTimeout(function() {
            $s.addClass('fade');
        }, 100);

        if ($s[0] == $sections.not('.import').last()[0]) {
            $submit.addClass('proceed');
        }
    }

    function showImportSection() {
        $submit.addClass('import');
        $sections
            .not('.import')
            .not('.intro')
            .removeClass('show')
            .removeClass('fade')
            .addClass('typed');

        var $s = $sections.filter('.import').addClass('show');
        $s.find('textarea').prop('disabled', false);

        setTimeout(function() {
            $s.addClass('fade');
        }, 100);
    }

    var $submit = $('p.submit');
    var $sections = $('.section');

    showNextSection();

    //	Next button
    $('.button.next').on('click', function(e) {
        e.preventDefault();
        showNextSection();
    });

    //	Cancel button
    $('.button.cancel').on('click', function(e) {
        e.preventDefault();
        showNextSection();
    });

    //	Import button
    $('.button.import').on('click', function(e) {
        e.preventDefault();
        showImportSection();
    });

    $sections.find('input').on('input propertychange', function(e) {
        $(this)
            .closest('.section')
            .addClass('typed');
    });
});
