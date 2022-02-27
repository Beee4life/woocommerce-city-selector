<?php
    function woocommerce_checkout_state_dropdown_fix() {
        if ( function_exists( 'is_checkout' ) && ! is_checkout() ) {
            // return;
        }
        $script = '<script>' . PHP_EOL;
        $script .= "jQuery(function() {" . PHP_EOL;
        $script .= "\tjQuery('#billing_country').trigger('change');" . PHP_EOL;
        $script .= "\tjQuery('#billing_state_field').removeClass('woocommerce-invalid');" . PHP_EOL;
        // $script .= "\tjQuery('#shipping_country').trigger('change');" . PHP_EOL;
        // $script .= "\tjQuery('#shipping_state_field').removeClass('woocommerce-invalid');" . PHP_EOL;
        $script .= "});" . PHP_EOL;
        $script .= '</script>' . PHP_EOL;
        echo $script;
    }
    // add_action( 'wp_footer', 'woocommerce_checkout_state_dropdown_fix', 50 );
