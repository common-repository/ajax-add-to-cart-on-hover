<?php
/**

 * Plugin Name: Ajax add to cart on hover

 * Plugin URI:

 * Description: Ajax add to cart on hover Plugin is used for adding variable products to cart using overlay on image when hovered over product image.

 * Version: 1.0.0

 * Author: Scrumwheel

 * Author URI: https://scrumwheel.com/

 * License: GPLv2 or later

 */
if (!defined('ABSPATH')) {

exit;

}



/**
 * Activation hook of plugin
 */
function aatcoh_activation()
{

}

/**
 * Deactivation hook of plugin
 */
function aatcoh_deactivation(){



}



/**
 * Its used add to cart buttons on products thumbnail groups.
 */
add_action( 'woocommerce_before_shop_loop_item_title', 'attcoh_showbuttons', 5 );
function attcoh_showbuttons() {

    global $product;

    $product_id = $product->get_id();

    echo '<div class="testing asd prod_'.$product_id.'">';

}



/**
 * Its used add to cart buttons display on particular variations wise create buttons.
 */
remove_action( 'woocommerce_shop_loop_item_title', 'action_woocommerce_shop_loop_item_title', 10, 2 );
add_action( 'woocommerce_shop_loop_item_title', 'attcoh_variationbuttons', 50 );
function attcoh_variationbuttons() {
    global $product;

    $args = array(
        'post_type'     => 'product_variation',
        'post_status'   => array( 'private', 'publish' ),
        'numberposts'   => -1,
        'orderby'       => 'menu_order',
        'order'         => 'asc',
        'post_parent'   => get_the_ID() // get parent post-ID
    );
    $variations = get_posts( $args );

    $variable_prod  = [];

    foreach ( $variations as $variation ) {
        // get variation ID
        $variation_ID = $variation->ID;

        // get variations meta
        $variable_id = $variation_ID;


        $product_variable = new WC_Product_Variation( $variable_id );

        $variable_data = $product_variable->get_data();

        array_push($variable_prod,array("variation_id"=>$variable_id,"variation_name"=>$variable_data['attributes']['pa_size'],"variation_stock"=> $product_variable->get_stock_quantity()));
    }


    $html = '';
    $style = '';
    $style_div = '';
    if(is_product()){
        $style_div = 'style="padding-bottom: 0.5rem;padding-top: 0.5rem;top: 50%;width: 91%;"';
    }
    foreach ($variable_prod as $cart){
        $dis = '';
        if($cart['variation_stock'] < 1){
            if(wp_is_mobile()){

            }else{
               $cart['variation_name'] = str_replace("- NL","",$cart['variation_name']);
                $dis = 'disabled="disabled"';
                $html .= '<button class="test_single_add_to_cart_button disabled quickbuy__unavailable" '.$dis.'" "'.$style.' data-variation-id="'.absint( $cart['variation_id'] ).'" data-product-id="'.absint( $product->get_id() ).'" data-quantity="1">
            '.strtoupper($cart['variation_name']).'<svg style="width: 100%;height: 100%;position: absolute;top: -10;bottom: 0;left: 0;right: 0;">
                        <line x1="0" y1="100%" x2="100%" y2="0" style="stroke:#999999; stroke-width:1"></line>
                    </svg>
            </button>';
            }
        }else{
            $cart['variation_name'] = str_replace("-NL","",$cart['variation_name']);
            $html .= '<button class="test_single_add_to_cart_button '.$dis.'" '.$style.' data-variation-id="'.absint( $cart['variation_id'] ).'" data-product-id="'.absint( $product->get_id() ).'" data-quantity="1">'.strtoupper($cart['variation_name']).'
                <div class="add-loader add_loading_'.absint( $cart['variation_id'] ).'" style="display:none;">
                    <img src="'.plugin_dir_url(__FILE__).'oval.svg" class="loder-gif">
                </div>
                <div class="add-success add_tick_'.absint( $cart['variation_id'] ).'" style="display:none;">
                    <img src="'.plugin_dir_url(__FILE__).'check-mark.png" style="width:70%;max-height: -webkit-fill-available;height:auto;">
                </div></button>';
        }
    }
    $img_url = plugin_dir_url(__FILE__)."/cross.png";
    if(empty($html)){
    
    }else{
        $html_view = '<div class="shop-badge" '.$style_div.'><h5>SIZE</h5><div class="add-custom-cart quickBuyRow">'.$html.'</div></div>
            <div class="full-tick-anim drawn" style="display:none;">
                <div class="trigger drawn"></div>
                <svg version="1.1" id="tick" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 37 37" style="enable-background:new 0 0 37 37;" xml:space="preserve"><path class="circ path" style="fill:none;stroke:#000000;stroke-width:3;stroke-linejoin:round;stroke-miterlimit:10;" d="M30.5,6.5L30.5,6.5c6.6,6.6,6.6,17.4,0,24l0,0c-6.6,6.6-17.4,6.6-24,0l0,0c-6.6-6.6-6.6-17.4,0-24l0,0C13.1-0.2,23.9-0.2,30.5,6.5z"></path><polyline class="tick path" style="fill:none;stroke:#000000;stroke-width:3;stroke-linejoin:round;stroke-miterlimit:10;" points="11.6,20 15.9,24.2 26.4,13.8 "></polyline></svg>
                <span style="display: inline;">Added to Cart</span>
            </div>
            <div class="outofstock_products full-tick-anim-outstock drawn" style="display:none;">
                <img src="'.$img_url.'" style="width:20%;height:20%;margin:50px;">
                <span style="display: block;color:black;">Out of stock</span>
            </div>
          </div>';
    }


    echo $html_view;

}





/**
 * This provides woocommerce add to cart link
 */
add_filter( 'woocommerce_loop_add_to_cart_link', function( $product ) {

    global $product;

    if ( is_shop() && 'variable' === $product->product_type ) {

        return '';

    } else {

        sprintf( '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',

            esc_url( $product->add_to_cart_url() ),

            esc_html( $product->add_to_cart_text() )

        );

    }

});



/**
 * Add custom css of the plugin and override some inbuilt class.
 */
function aatcoh_attchments() {

    wp_enqueue_style('icons',plugin_dir_url(__FILE__) . '/font-awesome.min.css','','1.0.0');

    wp_enqueue_style('custom-style',plugin_dir_url(__FILE__) . '/custom_style.css','','1.1.0');

    wp_enqueue_script('woocommerce-product-hover-cart', plugin_dir_url(__FILE__) . 'custom.js', array('jquery'), '1.0.0', true);

}

add_action( 'wp_enqueue_scripts', 'aatcoh_attchments' );



/**
 * Function used for override woocommerce plugin add-to-cart.min.js
 * Display changes of success message of added to cart
 */
add_action('wp_enqueue_scripts', 'aatcoh_override');
function aatcoh_override() {

    wp_deregister_script('wc-add-to-cart');

    wp_enqueue_script('wc-add-to-cart', plugin_dir_url(__FILE__) . '/add-to-cart.min.js', array('jquery', 'woocommerce', 'wc-country-select', 'wc-address-i18n'), null, true);

    wp_deregister_script('jquery.cookie');
    wp_enqueue_script('wc-cookies', plugin_dir_url(__FILE__) . '/jquery_cookie.min.js', array('jquery', 'woocommerce', '', ''), null, true);
}


add_filter( 'woocommerce_cart_item_name', 'aatcoh_category', 99, 3);
function aatcoh_category( $name, $cart_item, $cart_item_key ) {
    $product_item = $cart_item['data'];

    $variation = wc_get_product($product_item->get_id());
    
    $variation->get_formatted_name();
    if(!empty($variation)){
        $pr_name = $variation->get_data();
        $name = $pr_name['name'];

    }

    return $name;

}
