/* YITH WooCommerce Multi Step Checkout */

(function($){
    var $body               = $('body'),
        login               = $('#checkout_login'),
        billing             = $('#customer_billing_details'),
        shipping            = $('#customer_shipping_details'),
        order               = $('#order_review'),
        payment             = $('#payment'),
        form_actions        = $('#form_actions'),
        coupon              = $('#checkout_coupon'),
        timeline            = $(),
        steps               = new Array(login, billing, shipping, order, payment),
        is_user_logged_in   = $body.hasClass('logged-in');

    $body.on( 'updated_checkout yith_wcms_myaccount_order_pay', function(e){
        steps[4] = $('#payment');
         if(e.type == 'updated_checkout' ) {
            steps[4] = $('#payment');
        }

        $('#payment').find( 'input[name=payment_method]' ).on( 'click', function() {
            if ($('.payment_methods input.input-radio').length > 1) {
                var target_payment_box = $('div.payment_box.' + $(this).attr('ID'));
                if ($(this).is(':checked') && !target_payment_box.is(':visible')) {
                    $('div.payment_box').filter(':visible').slideUp(250);

                    if ($(this).is(':checked')) {
                        $('div.payment_box.' + $(this).attr('ID')).slideDown(250);
                    }
                }
            } else {
                $('div.payment_box').show();
            }

            if ($(this).data('order_button_text')) {
                $('#place_order').val($(this).data('order_button_text'));
            } else {
                $('#place_order').val($('#place_order').data('value'));
            }
        });
    } );

    if ($body.hasClass('woocommerce-order-pay')) {
        $body.trigger('yith_wcms_myaccount_order_pay');
    }

    $body.on('yith_wcms_select2', function (event) {
        if ($().select2) {
            var wc_country_select_select2 = function () {
                $('select.country_select, select.state_select').each(function () {
                    var select2_args = {
                        placeholder      : $(this).attr('placeholder'),
                        placeholderOption: 'first',
                        width            : '100%'
                    };

                    $(this).select2(select2_args);
                });
            };

            wc_country_select_select2();

            $body.bind('country_to_state_changed', function () {
                wc_country_select_select2();
            });
        }
    });

    $body.trigger('yith_wcms_select2');

    form_actions.find('.button.prev').add('.button.next').on( 'click', function(e){
        var t               = $(this),
            timeline        = $('#checkout_timeline'),
            action          = t.data('action'),
            current_step    = form_actions.data('step'),
            next_step       = current_step + 1,
            prev_step       = current_step - 1,
            prev            = form_actions.find('.button.prev'),
            next            = form_actions.find('.button.next');

        timeline.find('.active').removeClass('active');

        if( action == 'next' ){
            form_actions.data('step', next_step);
            steps[current_step].fadeOut('slow', function(){
                steps[next_step].fadeIn('slow');
            });

            $('#timeline-' + next_step).toggleClass('active');
        }

        else if( action == 'prev' ){
            form_actions.data('step', prev_step);
            steps[current_step].fadeOut('slow', function(){
                steps[prev_step].fadeIn('slow');
            });

            $('#timeline-' + prev_step).toggleClass('active');
        }

        //steps[current_step].fadeOut();

        current_step = form_actions.data('step');

        // if current step is billing information and current user logged in or
        // current step is login and current user not logged in
        if( ( current_step == 1 && is_user_logged_in == true ) || ( current_step == 0 && is_user_logged_in == false ) ){
            prev.fadeOut('slow');
        }

        else {
            prev.fadeIn('slow');
        }

        // Your order
        if( current_step == 3 ){
            coupon.fadeIn('slow');
        }

        else {
            coupon.fadeOut('slow');
        }

        // Last step
        if( current_step == 4 ){
            next.fadeOut('slow');
        }

        else {
            next.fadeIn('slow');
        }

    } );
})(jQuery);