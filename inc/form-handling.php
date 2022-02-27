<?php
    /**
     * Handle CSV upload form
     */
    function woocs_upload_csv_file() {
        if ( isset( $_POST[ 'woocs_upload_csv_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'woocs_upload_csv_nonce' ], 'woocs-upload-csv-nonce' ) ) {
                Woocommerce_City_Selector::woocs_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'woocommerce-city-selector' ) );
            } else {
                Woocommerce_City_Selector::woocs_check_uploads_folder();
                $target_file = woocs_upload_folder( '/' ) . basename( $_FILES[ 'csv_upload' ][ 'name' ] );
                if ( move_uploaded_file( $_FILES[ 'csv_upload' ][ 'tmp_name' ], $target_file ) ) {
                    Woocommerce_City_Selector::woocs_errors()->add( 'success_file_uploaded', sprintf( esc_html__( "File '%s' is successfully uploaded and now shows under 'Select files to import'", 'woocommerce-city-selector' ), $_FILES[ 'csv_upload' ][ 'name' ] ) );
                    do_action( 'woocs_after_success_file_upload' );
                } else {
                    Woocommerce_City_Selector::woocs_errors()->add( 'error_file_uploaded', esc_html__( 'Upload failed. Please try again.', 'woocommerce-city-selector' ) );
                }
            }
        }
    }
    add_action( 'admin_init', 'woocs_upload_csv_file' );


    /**
     * Handle process CSV form
     */
    function woocs_do_something_with_file() {
        if ( isset( $_POST[ 'woocs_select_file_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'woocs_select_file_nonce' ], 'woocs-select-file-nonce' ) ) {
                Woocommerce_City_Selector::woocs_errors()->add( 'error_nonce_no_match', esc_html__( 'Something went wrong, please try again.', 'woocommerce-city-selector' ) );
            } else {
                if ( empty( $_POST[ 'acfcs_file_name' ] ) ) {
                    Woocommerce_City_Selector::woocs_errors()->add( 'error_no_file_selected', esc_html__( "You didn't select a file.", 'woocommerce-city-selector' ) );

                    return;
                }

                $file_name = $_POST[ 'acfcs_file_name' ];
                $delimiter = ! empty( $_POST[ 'acfcs_delimiter' ] ) ? sanitize_text_field( $_POST[ 'acfcs_delimiter' ] ) : apply_filters( 'acfcs_delimiter', ';' );
                $import    = isset( $_POST[ 'acfcs_import' ] ) ? true : false;
                $max_lines = isset( $_POST[ 'acfcs_max_lines' ] ) ? (int) $_POST[ 'acfcs_max_lines' ] : false;
                $remove    = isset( $_POST[ 'acfcs_remove' ] ) ? true : false;
                $verify    = isset( $_POST[ 'acfcs_verify' ] ) ? true : false;

                if ( true === $verify ) {
                    woocs_verify_data( $file_name, $delimiter, $verify );
                } elseif ( true === $import ) {
                    woocs_import_data( $file_name, '', $delimiter, $verify, $max_lines );
                } elseif ( true === $remove ) {
                    woocs_delete_file( $file_name );
                }
            }
        }
    }
    add_action( 'admin_init', 'woocs_do_something_with_file' );


    /**
     * Handle importing of raw CSV data
     */
    function woocs_import_raw_data() {
        if ( isset( $_POST[ 'woo_import_raw_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'woo_import_raw_nonce' ], 'woo-import-raw-nonce' ) ) {
                Woocommerce_City_Selector::woocs_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'woocommerce-city-selector' ) );
            } else {
                $verified_data = woocs_verify_csv_data( sanitize_textarea_field( $_POST[ 'acfcs_raw_csv_import' ] ) );
                if ( isset( $_POST[ 'acfcs_verify' ] ) ) {
                    if ( false != $verified_data ) {
                        Woocommerce_City_Selector::woocs_errors()->add( 'success_csv_valid', esc_html__( 'Congratulations, your CSV data seems valid.', 'woocommerce-city-selector' ) );
                    }
                } elseif ( isset( $_POST[ 'acfcs_import' ] ) ) {
                    if ( false != $verified_data ) {
                        woocs_import_data( $verified_data );
                    }
                }
            }
        }
    }
    add_action( 'admin_init', 'woocs_import_raw_data' );


    /**
     * Handle form to delete one or more countries
     */
    function woocs_delete_countries() {
        if ( isset( $_POST[ 'woocs_remove_countries_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'woocs_remove_countries_nonce' ], 'woocs-remove-countries-nonce' ) ) {
                Woocommerce_City_Selector::woocs_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'woocommerce-city-selector' ) );
            } else {
                if ( empty( $_POST[ 'woocs_delete_country' ] ) ) {
                    Woocommerce_City_Selector::woocs_errors()->add( 'error_no_country_selected', esc_html__( "You didn't select any countries, please try again.", 'woocommerce-city-selector' ) );
                } else {
                    if ( is_array( $_POST[ 'woocs_delete_country' ] ) ) {
                        woocs_delete_country( $_POST[ 'woocs_delete_country' ] );
                    }
                }
            }
        }
    }
    add_action( 'admin_init', 'woocs_delete_countries' );


    /**
     * Form to delete individual rows/cities
     */
    function woocs_delete_rows() {
        if ( isset( $_POST[ 'woocs_delete_row_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'woocs_delete_row_nonce' ], 'woocs-delete-row-nonce' ) ) {
                Woocommerce_City_Selector::woocs_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'woocommerce-city-selector' ) );
            } else {
                global $wpdb;
                if ( is_array( $_POST[ 'row_id' ] ) ) {
                    foreach( $_POST[ 'row_id' ] as $row ) {
                        $sanitized_row = sanitize_text_field( $row );
                        $split    = explode( ' ', $sanitized_row, 2 );
                        if ( isset( $split[ 0 ] ) && isset( $split[ 1 ] ) ) {
                            $ids[]    = $split[ 0 ];
                            $cities[] = $split[ 1 ];
                        }
                    }
                    $cities  = implode( ', ', $cities );
                    $row_ids = implode( ',', $ids );
                    $query   = "DELETE FROM " . $wpdb->prefix . "cities WHERE id IN (" . $row_ids . ")";
                    $amount  = $wpdb->query( $query );

                    if ( $amount > 0 ) {
                        Woocommerce_City_Selector::woocs_errors()->add( 'success_row_delete', sprintf( _n( 'You have deleted the city %s.', 'You have deleted the following cities: %s.', $amount, 'woocommerce-city-selector' ), $cities ) );
                    }
                }
            }
        }
    }
    add_action( 'admin_init', 'woocs_delete_rows' );


    /**
     * Form to handle deleting of all transients
     */
    function woocs_delete_all_transients() {
        if ( isset( $_POST[ 'woocs_delete_transients' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'woocs_delete_transients' ], 'woocs-delete-transients-nonce' ) ) {
                Woocommerce_City_Selector::woocs_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'woocommerce-city-selector' ) );
            } else {
                do_action( 'woocs_delete_transients' );
                Woocommerce_City_Selector::woocs_errors()->add( 'success_transients_delete', esc_html__( 'You have successfully deleted all transients.', 'woocommerce-city-selector' ) );
            }
        }
    }
    add_action( 'admin_init', 'woocs_delete_all_transients' );


    /**
     * Delete contents of entire cities table
     */
    function woocs_truncate_table() {
        if ( isset( $_POST[ 'woocs_truncate_table_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'woocs_truncate_table_nonce' ], 'woocs-truncate-table-nonce' ) ) {
                Woocommerce_City_Selector::woocs_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'woocommerce-city-selector' ) );
            } else {
                global $wpdb;
                $prefix = $wpdb->get_blog_prefix();
                $wpdb->query( 'TRUNCATE TABLE ' . $prefix . 'cities' );
                Woocommerce_City_Selector::woocs_errors()->add( 'success_table_truncated', esc_html__( 'All cities are deleted.', 'woocommerce-city-selector' ) );
                do_action( 'woocs_after_success_nuke' );
            }
        }
    }
    add_action( 'admin_init', 'woocs_truncate_table' );


    /**
     * Handle preserve settings option
     */
    function woocs_delete_settings() {
        if ( isset( $_POST[ 'woocs_remove_cities_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'woocs_remove_cities_nonce' ], 'woocs-remove-cities-nonce' ) ) {
                Woocommerce_City_Selector::woocs_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'woocommerce-city-selector' ) );
            } else {
                if ( isset( $_POST[ 'remove_cities_table' ] ) ) {
                    update_option( 'acfcs_delete_cities_table', 1 );
                } else {
                    delete_option( 'acfcs_delete_cities_table' );
                }
                Woocommerce_City_Selector::woocs_errors()->add( 'success_settings_saved', esc_html__( 'Settings saved', 'woocommerce-city-selector' ) );
            }
        }
    }
    add_action( 'admin_init', 'woocs_delete_settings' );


    /**
     * Manually import default available countries
     */
    function woocs_import_preset_countries() {
        if ( isset( $_POST[ 'woocs_import_actions_nonce' ] ) ) {
            if ( ! wp_verify_nonce( $_POST[ 'woocs_import_actions_nonce' ], 'woocs-import-actions-nonce' ) ) {
                Woocommerce_City_Selector::woocs_errors()->add( 'error_no_nonce_match', esc_html__( 'Something went wrong, please try again.', 'woocommerce-city-selector' ) );
            } else {
                if ( isset( $_POST[ 'acfcs_import_be' ] ) || isset( $_POST[ 'acfcs_import_nl' ] ) ) {
                    if ( isset( $_POST[ 'acfcs_import_be' ] ) && 1 == $_POST[ 'acfcs_import_be' ] ) {
                        woocs_import_data( 'be.csv', ACFCS_PLUGIN_PATH . 'import/' );
                        do_action( 'woocs_delete_transients', 'be' );
                    }
                    if ( isset( $_POST[ 'acfcs_import_nl' ] ) && 1 == $_POST[ 'acfcs_import_nl' ] ) {
                        woocs_import_data( 'nl.csv', ACFCS_PLUGIN_PATH . 'import/' );
                        do_action( 'woocs_delete_transients', 'nl' );
                    }
                    do_action( 'woocs_after_success_import' );
                }
            }
        }
    }
    add_action( 'admin_init', 'woocs_import_preset_countries' );
