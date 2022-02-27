<?php
    /**
     * Get country name + i18n country names
     *
     * These are defined here (just in case) so they are 'picked up' as translatable strings, because not all values occur in the plugin itself.
     *
     * @param $country_code
     *
     * @since 0.29.0
     *
     * @return mixed
     */
    function acfcs_country_i18n( $country_code ) {

        $country_array = array(
            'ad'     => esc_html__( 'Andorra', 'woocommerce-city-selector' ),
            'aw'     => esc_html__( 'Aruba', 'woocommerce-city-selector' ),
            'at'     => esc_html__( 'Austria', 'woocommerce-city-selector' ),
            'au'     => esc_html__( 'Australia', 'woocommerce-city-selector' ),
            'br'     => esc_html__( 'Brazil', 'woocommerce-city-selector' ),
            'ca'     => esc_html__( 'Canada', 'woocommerce-city-selector' ),
            'cn'     => esc_html__( 'China', 'woocommerce-city-selector' ),
            'cw'     => esc_html__( 'Curaçao', 'woocommerce-city-selector' ),
            'europe' => esc_html__( 'Europe', 'woocommerce-city-selector' ),
            'fr'     => esc_html__( 'France', 'woocommerce-city-selector' ),
            'de'     => esc_html__( 'Germany', 'woocommerce-city-selector' ),
            'gd'     => esc_html__( 'Grenada', 'woocommerce-city-selector' ),
            'gb'     => esc_html__( 'Great Britain', 'woocommerce-city-selector' ),
            'lu'     => esc_html__( 'Luxembourg', 'woocommerce-city-selector' ),
            'mx'     => esc_html__( 'Mexico', 'woocommerce-city-selector' ),
            'nl'     => esc_html__( 'Netherlands', 'woocommerce-city-selector' ),
            'nz'     => esc_html__( 'New Zealand', 'woocommerce-city-selector' ),
            'pt'     => esc_html__( 'Portugal', 'woocommerce-city-selector' ),
            'kr'     => esc_html__( 'South Korea', 'woocommerce-city-selector' ),
            'es'     => esc_html__( 'Spain', 'woocommerce-city-selector' ),
            'ch'     => esc_html__( 'Switzerland', 'woocommerce-city-selector' ),
            'us'     => esc_html__( 'United States', 'woocommerce-city-selector' ),
            'uy'     => esc_html__( 'Uruguay', 'woocommerce-city-selector' ),
            'world'  => esc_html__( 'World', 'woocommerce-city-selector' ),
        );

        if ( $country_code && array_key_exists( $country_code, $country_array ) ) {
            return $country_array[ $country_code ];
        }

        return $country_code;
    }
