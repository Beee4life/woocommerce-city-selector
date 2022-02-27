<?php
    /**
     * Function to delete transients
     *
     * @param false $country_code
     */
    function woocs_delete_transients( $country_code = false ) {
        if ( false != $country_code ) {
            delete_transient( 'acfcs_states_' . strtolower( $country_code ) );
            delete_transient( 'acfcs_cities_' . strtolower( $country_code ) );
        } else {
            delete_transient( 'acfcs_countries' );
            $countries = woocs_get_countries( false, false, true );
            if ( ! empty( $countries ) ) {
                foreach( $countries as $country_code => $label ) {
                    do_action( 'woocs_delete_transients', $country_code );
                }
            }
        }
    }
    add_action( 'woocs_delete_transients', 'woocs_delete_transients' );
    add_action( 'woocs_after_success_import', 'woocs_delete_transients' );
    add_action( 'woocs_after_success_import_raw', 'woocs_delete_transients' );
    add_action( 'woocs_after_success_nuke', 'woocs_delete_transients' );
