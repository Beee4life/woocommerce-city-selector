<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    function woocommerce_field_args( $args, $key, $value ) {
        // error_log($key);

        if ( in_array( $key, [ 'billing_country', 'shipping_country' ] ) ) {
            // echo '<pre>'; var_dump($args['options']); echo '</pre>'; exit;
        }

        if ( in_array( $key, [ 'billing_city', 'shipping_city' ] ) ) {
            // echo '<pre>'; var_dump($args['options']); echo '</pre>'; exit;
        }

        if ( in_array( $key, [ 'billing_state', 'shipping_state' ] ) ) {
            // echo '<pre>'; var_dump($args['options']); echo '</pre>'; exit;
        }

        return $args;
    }
    add_filter( 'woocommerce_form_field_args', 'woocommerce_field_args', 10, 3 );

    function woocs_filter_state_field( $field, $key, $args, $value ) {
        // error_log($key);
        // @TODO: get changed country + add states through filter ?
        // echo '<pre>'; var_dump($field); echo '</pre>'; exit;
        // echo '<pre>'; var_dump($args); echo '</pre>'; exit;
        // echo '<pre>'; var_dump($value); echo '</pre>'; exit;

        return $field;
    }
    add_filter( 'woocommerce_form_field_state', 'woocs_filter_state_field', 10, 4 );

    function woocs_checkout_fields( $fields ) {
		// echo '<pre>'; var_dump($fields); echo '</pre>'; exit;
		$fields[ 'billing' ][ 'billing_state' ][ 'priority' ]     = 50;
		$fields[ 'billing' ][ 'billing_city' ][ 'priority' ]      = 60;
		$fields[ 'billing' ][ 'billing_address_1' ][ 'priority' ] = 70;
		$fields[ 'billing' ][ 'billing_address_2' ][ 'priority' ] = 80;
		uasort( $fields, 'wc_checkout_fields_uasort_comparison' );
		// echo '<pre>'; var_dump($fields); echo '</pre>'; exit;

        return $fields;
    }
    add_filter( 'woocommerce_checkout_fields', 'woocs_checkout_fields', 1 );
