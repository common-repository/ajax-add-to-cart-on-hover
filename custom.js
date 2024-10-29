/* This function is used for the add to cart using ajax. */
(function ($) {


    jQuery(document).on('click','.test_single_add_to_cart_button:not(.disabled)', function (e) {

        var product_id_parent = $(this).attr('data-product-id');

        var $thisbutton = $(this),
            $form = $thisbutton.closest('form.cart'),
            quantity = $(this).attr('data-quantity') || 1,
            $form = $(this).attr('data-variation-id'),
            variation_id = $(this).attr('data-variation-id'),
            product_id = $(this).attr('data-variation-id') || $(this).attr('data-product-id');
        if (product_id) {

            e.preventDefault();

            var data = {
                'action' : "woocommerce_add_variation_to_cart",
                'product_id': product_id,
                'quantity': quantity,
                'variation_id':variation_id
            };

            $(document.body).trigger('adding_to_cart', [$thisbutton, data]);


            $.ajax({
                type: 'POST',
                url: woocommerce_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
                data: data,
                beforeSend: function (response) {
                    $thisbutton.removeClass('added').addClass('loading');
                    $('button[data-variation-id = '+variation_id+']').addClass('add_loading_cart');
                    $('.add_loading_'+variation_id).css('display','block').fadeIn(5000);
                },
                complete: function (response) {
                    $('.add_loading_'+variation_id).css('display','none');
                    $('button[data-variation-id = '+variation_id+']').removeClass('add_loading_cart');
                    $('.add_tick_'+variation_id).css('display','block');
                    $('.add_tick_'+variation_id).fadeOut("slow");
                },
                success: function (response) {

                    if (response.error & response.product_url) {
                        window.location = response.product_url;
                        return;
                    }

                    $('.add_tick_'+variation_id).css('display','block');


                    $('.prod_'+product_id_parent).find(".full-tick-anim").css('display','block').fadeIn(4000);
                    $(".full-tick-anim").fadeOut(4000);

                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
                    $('.shopkeeper-mini-cart').toggleClass('open');
                },
            });

            return false;
        }

    });
    })(jQuery);
