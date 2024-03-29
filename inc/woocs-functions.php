<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    /**
     * Create an array with available countries from db.
     * This function makes use of a transient to speed up the process.
     *
     * @param false $show_first
     * @param false $field
     * @param false $force
     *
     * @return array
     */
    function woocs_get_countries( $show_first = true, $field = false, $force = false ) {
        $countries            = array();
        $select_country_label = apply_filters( 'woocs_select_country_label', esc_html__( 'Select a country', 'woocommerce-city-selector' ) );
        $show_labels          = ( isset( $field[ 'show_labels' ] ) ) ? $field[ 'show_labels' ] : true;

        if ( $show_first ) {
            $countries[ '' ] = '-';
            if ( ! $show_labels ) {
                $countries[ '' ] = $select_country_label;
            }
        }

        $transient = get_transient( 'woocs_countries' );
        if ( false != $force || false == $transient || is_array( $transient ) && empty( $transient ) ) {
            global $wpdb;
            $results = $wpdb->get_results( '
                SELECT * FROM ' . $wpdb->prefix . 'cities
                GROUP BY country
                ORDER BY country ASC
            ' );

            $country_results = array();
            foreach ( $results as $data ) {
                if ( isset( $data->country_code ) && isset( $data->country ) ) {
                    $country_results[ $data->country_code ] = esc_html__( $data->country, 'woocommerce-city-selector' );
                }
            }

            set_transient( 'woocs_countries', $country_results, DAY_IN_SECONDS );
            $countries = array_merge( $countries, $country_results );

        } elseif ( is_array( $transient ) ) {
            $countries = array_merge( $countries, $transient );
        }

        return $countries;
    }


    /**
     * Create an array with states based on a country code
     *
     * @param false $country_code
     * @param false $show_first
     * @param false $field
     *
     * @return array
     */
    function woocs_get_states( $country_code = false, $show_first = true, $field = false ) {
        $select_province_state_label = apply_filters( 'woocs_select_province_state_label', esc_attr__( 'Select a province/state', 'woocommerce-city-selector' ) );
        $show_labels                 = ( isset( $field[ 'show_labels' ] ) ) ? $field[ 'show_labels' ] : true;
        $states                      = array();

        if ( $show_first ) {
            if ( $show_labels ) {
                $states[ '' ] = '-';
            } else {
                $states[ '' ] = $select_province_state_label;
            }
        }

        if ( false != $country_code ) {
            $transient = get_transient( 'woocs_states_' . strtolower( $country_code ) );
            if ( false == $transient || is_array( $transient ) && empty( $transient ) ) {
                $order = 'ORDER BY state_name ASC';
                if ( 'FR' == $country_code ) {
                    $order = "ORDER BY LENGTH(state_name), state_name";
                }

                global $wpdb;
                $sql = $wpdb->prepare( "
                    SELECT *
                    FROM " . $wpdb->prefix . "cities
                    WHERE country_code = %s
                    GROUP BY state_code
                    " . $order, strtoupper( $country_code )
                );
                $results = $wpdb->get_results( $sql );

                $state_results = array();
                foreach ( $results as $data ) {
                    $state_results[ $country_code . '-' . $data->state_code ] = esc_attr__( $data->state_name, 'woocommerce-city-selector' );
                }

                set_transient( 'woocs_states_' . strtolower( $country_code ), $state_results, DAY_IN_SECONDS );

                $states = array_merge( $states, $state_results );

            } else {
                $states = array_merge( $states, $transient );
            }
        }

        return $states;
    }


    /**
     * Create an array with cities for a certain country/state
     *
     * @param false $country_code
     * @param false $state_code
     * @param false $field
     *
     * @return array
     */
    function woocs_get_cities( $country_code = false, $state_code = false, $field = false ) {
        $cities            = array();
        $cities_transient  = false;
        $select_city_label = apply_filters( 'woocs_select_city_label', esc_attr__( 'Select a city', 'woocommerce-city-selector' ) );
        $set_transient     = false;
        $show_labels       = ( isset( $field[ 'show_labels' ] ) ) ? $field[ 'show_labels' ] : true;

        if ( $show_labels ) {
            $cities[ '' ] = '-';
        } else {
            $cities[ '' ] = $select_city_label;
        }

        if ( $country_code && ! $state_code ) {
            $cities_transient = get_transient( 'woocs_cities_' . strtolower( $country_code ) );
        } elseif ( $country_code && $state_code ) {
            $cities_transient = get_transient( 'woocs_cities_' . strtolower( $country_code ) . '-' . strtolower( $state_code ) );
        }

        if ( false == $cities_transient || empty( $cities_transient ) ) {
            $set_transient = true;
        } else {
            foreach ( $cities_transient as $data ) {
                $city_array[ esc_attr__( $data, 'woocommerce-city-selector' ) ] = esc_attr__( $data, 'woocommerce-city-selector' );
            }
            if ( isset( $city_array ) ) {
                $cities = array_merge( $cities, $city_array );
            }
        }

        if ( $set_transient ) {
            if ( false !== $country_code ) {
                global $wpdb;
                $query = 'SELECT * FROM ' . $wpdb->prefix . 'cities';
                if ( $country_code && $state_code ) {
                    if ( 3 < strlen( $state_code ) ) {
                        $state_code = substr( $state_code, 3 );
                    }
                    $query .= " WHERE country_code = '{$country_code}' AND state_code = '{$state_code}'";
                    $query .= " ORDER BY state_name, city_name ASC";
                } elseif ( $country_code ) {
                    $query .= " WHERE country_code = '{$country_code}'";
                }
                $city_results = array();
                $results      = $wpdb->get_results( $query );
                foreach ( $results as $data ) {
                    $city_results[] = [
                        'city_name' => $data->city_name,
                    ];
                }

                if ( ! empty( $city_results ) ) {
                    uasort( $city_results, 'wooocs_sort_array_with_quotes' );
                }
                foreach ( $city_results as $data ) {
                    $city_array[ esc_attr__( $data[ 'city_name' ], 'woocommerce-city-selector' ) ] = esc_attr__( $data[ 'city_name' ], 'woocommerce-city-selector' );
                }
                if ( isset( $city_array ) ) {
                    $cities = array_merge( $cities, $city_array );
                }
                if ( ! $state_code ) {
                    set_transient( 'woocs_cities_' . strtolower( $country_code ), $city_array, DAY_IN_SECONDS );
                } elseif ( $state_code ) {
                    set_transient( 'woocs_cities_' . strtolower( $country_code ) . '-' . strtolower( $state_code ), $city_array, DAY_IN_SECONDS );
                }
            }
        }

        return $cities;
    }


    /**
     * Get country name by country code
     *
     * @param $country_code
     *
     * @return mixed
     */
    function woocs_get_country_name( $country_code = false ) {
        if ( false != $country_code ) {
            global $wpdb;
            $country = $wpdb->get_row( $wpdb->prepare( "SELECT country FROM {$wpdb->prefix}cities WHERE country_code = %s", $country_code ) );
            if ( isset( $country->country ) ) {
                return $country->country;
            } else {
                $country_name = woocs_country_i18n( $country_code );
                if ( $country_code != $country_name ) {
                    return $country_name;
                }
            }
        }

        return $country_code;
    }


    /**
     * Checks if there any cities in the database (for page availability)
     *
     * @return bool
     */
    function woocs_has_cities() {
        global $wpdb;
        $results = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'cities LIMIT 1' );

        if ( count( $results ) > 0 ) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Checks if files are uploaded
     *
     * @return array
     */
    function woocs_check_if_files() {
        $target_dir = woocs_upload_folder();
        if ( is_dir( $target_dir ) ) {
            $file_index = scandir( $target_dir );
            $excluded_files = [
                '.',
                '..',
                '.DS_Store',
                'debug.json',
            ];

            if ( is_array( $file_index ) ) {
                $actual_files = array();
                foreach ( $file_index as $file ) {
                    if ( ! in_array( $file, $excluded_files ) ) {
                        $actual_files[] = $file;
                    }
                }
                if ( ! empty( $actual_files ) ) {
                    return $actual_files;
                }
            }
        }

        return array();
    }


    /**
     * Convert data from an uploaded CSV file to an array
     *
     * @param        $file_name
     * @param string $delimiter
     * @param string $upload_folder
     * @param false  $verify
     * @param false  $max_lines
     *
     * @return array|WP_Error
     */
    function woocs_csv_to_array( $file_name, $upload_folder = '', $delimiter = ';', $verify = false, $max_lines = false ) {
        $upload_folder = ( ! empty( $upload_folder ) ) ? $upload_folder : woocs_upload_folder( '/' );
        $csv_array     = array();
        $empty_array   = false;
        $new_array     = array();

        if ( ( file_exists( $upload_folder . $file_name ) && $handle = fopen( $upload_folder . $file_name, "r" ) ) !== false ) {
            $column_benchmark = 5;
            $line_number      = 0;

            while ( ( $csv_line = fgetcsv( $handle, apply_filters( 'woocs_line_length', 1000 ), "{$delimiter}" ) ) !== false ) {
                $line_number++;
                $csv_array[ 'delimiter' ] = $delimiter;

                // if column count doesn't match benchmark
                if ( count( $csv_line ) != $column_benchmark ) {
                    // if column count < benchmark
                    if ( count( $csv_line ) < $column_benchmark ) {
                        $error_message = esc_html__( 'Since your file is not accurate anymore, the file is deleted.', 'woocommerce-city-selector' );
                        Woocommerce_City_Selector::woocs_errors()->add( 'error_no_correct_columns_' . $line_number, sprintf( esc_html__( 'There are too few columns on line %d. %s', 'woocommerce-city-selector' ), $line_number, $error_message ) );

                    } elseif ( count( $csv_line ) > $column_benchmark ) {
                        // if column count > benchmark
                        $error_message = esc_html__( 'Since your file is not accurate anymore, the file is deleted.', 'woocommerce-city-selector' );
                        if ( false === $verify ) {
                            $error_message = 'Lines 0-' . ( $line_number - 1 ) . ' are correctly imported but since your file is not accurate anymore, the file is deleted';
                        }
                        Woocommerce_City_Selector::woocs_errors()->add( 'error_no_correct_columns_' . $line_number, sprintf( esc_html__( 'There are too many columns on line %d. %s', 'woocommerce-city-selector' ), $line_number, $error_message ) );
                    }
                }

                if ( Woocommerce_City_Selector::woocs_errors()->get_error_codes() ) {
                    $empty_array = true;
                    $new_array   = array();
                } else {
                    // create a new array for each row
                    $new_line = array();
                    foreach ( $csv_line as $item ) {
                        $new_line[] = $item;
                    }
                    if ( ! empty( $new_line ) ) {
                        $new_array[] = $new_line;
                    }

                    if ( false != $max_lines ) {
                        if ( $line_number == $max_lines ) {
                            break;
                        }
                    }
                }
            }
            fclose( $handle );

            if ( Woocommerce_City_Selector::woocs_errors()->get_error_codes() ) {
                // delete file
                if ( file_exists( woocs_upload_folder( '/' ) . $file_name ) ) {
                    unlink( woocs_upload_folder( '/' ) . $file_name );
                    $csv_array[ 'error' ] = 'file_deleted';
                }
            }

            /**
             * Don't add data if there are any errors. This to prevent rows which had no error from outputting
             * on the preview page.
             */
            if ( ! empty( $new_array ) && false === $empty_array ) {
                $csv_array[ 'data' ] = array_values( $new_array );
            }
        }

        return $csv_array;
    }


    /**
     * Verify raw csv import
     *
     * @param false  $csv_data
     * @param string $delimiter
     *
     * @return array|false
     */
    function woocs_verify_csv_data( $csv_data = false, $delimiter = ";" ) {
        if ( false != $csv_data ) {
            $column_benchmark = 5;
            $line_number      = 0;
            $lines            = explode( "\r\n", $csv_data );

            foreach ( $lines as $line ) {
                $line_number++;

                if ( ! is_array( $csv_data ) ) {
                    $line_array = explode( $delimiter, $line );
                }

                if ( count( $line_array ) != $column_benchmark ) {
                    // length of a line is not correct
                    if ( count( $line_array ) < $column_benchmark ) {
                        Woocommerce_City_Selector::woocs_errors()->add( 'error_no_correct_columns', sprintf( esc_html__( 'There are too few columns on line %d.', 'woocommerce-city-selector' ), $line_number ) );

                        return false;

                    } elseif ( count( $line_array ) > $column_benchmark ) {
                        Woocommerce_City_Selector::woocs_errors()->add( 'error_no_correct_columns', sprintf( esc_html__( 'There are too many columns on line %d.', 'woocommerce-city-selector' ), $line_number ) );

                        return false;
                    }
                }

                $column_counter = 0;
                foreach( $line_array as $element ) {
                    $column_counter++;
                    if ( $column_counter == 4 ) {
                        if ( 2 != strlen( $element ) ) {
                            Woocommerce_City_Selector::woocs_errors()->add( 'error_wrong_country_length', sprintf( esc_html__( 'The length of the country abbreviation on line %d is incorrect.', 'woocommerce-city-selector' ), $line_number ) );

                            return false;
                        }
                    }
                }
                $validated_data[] = $line_array;
            }

            return $validated_data;
        }

        return false;
    }


    /**
     * Get packages through WP_Http
     *
     * @return array|mixed
     */
    function woocs_get_packages( $endpoint = 'single' ) {
        $url     = ACFCS_WEBSITE_URL . '/wp-json/countries/v1/' . $endpoint;
        $request = new WP_Http;
        $result  = $request->request( $url, array( 'method' => 'GET' ) );
        if ( 200 == $result[ 'response' ][ 'code' ] ) {
            $response = json_decode( $result[ 'body' ] );

            return $response;
        }

        return array();
    }


    /**
     * Get country info for debug
     *
     * @return array
     */
    function woocs_get_countries_info() {
        global $wpdb;
        $results = $wpdb->get_results( '
                SELECT country_code FROM ' . $wpdb->prefix . 'cities
                GROUP BY country_code
                ORDER BY country_code ASC
            ' );

        $woocs_info = array();
        foreach ( $results as $data ) {
            $country_code = $data->country_code;
            $results      = $wpdb->get_results( $wpdb->prepare( '
                SELECT * FROM ' . $wpdb->prefix . 'cities
                WHERE country_code = %s
                ORDER BY country_code ASC
            ', $country_code ) );

            $woocs_info[ $country_code ] = [
                'country_code' => $country_code,
                'count'        => count( $results ),
                'name'         => woocs_get_country_name( $country_code ),
            ];
        }

        return $woocs_info;
    }


    /**
     * Search an array which contains quotes like "'t Veld"
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    function wooocs_sort_array_with_quotes( $a, $b ) {
        return strnatcasecmp( woocs_custom_sort_with_quotes( $a[ 'city_name' ] ), woocs_custom_sort_with_quotes( $b[ 'city_name' ] ) );
    }


    /**
     * Sort with quotes
     *
     * @param $city
     *
     * @return string|string[]|null
     */
    function woocs_custom_sort_with_quotes( $city ) {
        // strip quote marks
        $city = trim( $city, '\'s ' );
        $city = preg_replace( '/^\s*\'s \s+/i', '', $city );

        return $city;
    }


    /**
     * Render select in ACF field
     *
     * @param $type
     * @param $field
     * @param $stored_value
     * @param $prefill_values
     *
     * @return false|string
     */
    function woocs_render_dropdown( $type, $field, $stored_value, $prefill_values ) {
        $woocs_dropdown       = 'woocs__dropdown';
        $city_label           = apply_filters( 'woocs_select_city_label', esc_attr__( 'Select a city', 'woocommerce-city-selector' ) );
        $countries            = woocs_get_countries( true, $field );
        $country_label        = apply_filters( 'woocs_select_country_label', esc_attr__( 'Select a country', 'woocommerce-city-selector' ) );
        $default_country      = ( isset( $field[ 'default_country' ] ) && ! empty( $field[ 'default_country' ] ) ) ? $field[ 'default_country' ] : false;
        $default_value        = false;
        $field_id             = $field[ 'id' ];
        $field_name           = $field[ 'name' ];
        $prefill_cities       = $prefill_values[ 'prefill_cities' ];
        $prefill_states       = $prefill_values[ 'prefill_states' ];
        $province_state_label = apply_filters( 'woocs_select_province_state_label', esc_attr__( 'Select a province/state', 'woocommerce-city-selector' ) );
        $selected_selected    = ' selected="selected"';
        $show_labels          = ( isset( $field[ 'show_labels' ] ) ) ? $field[ 'show_labels' ] : true;
        $use_select2          = ( isset( $field[ 'use_select2' ] ) ) ? $field[ 'use_select2' ] : false;
        $dropdown_class       = ( true == $use_select2 ) ? 'select2 ' . $woocs_dropdown : $woocs_dropdown;
        $data_label_value     = ( true == $show_labels ) ? '1' : '0';
        $which_fields         = ( isset( $field[ 'which_fields' ] ) ) ? $field[ 'which_fields' ] : 'all';

        switch( $type ) {
            case 'country':
                $default_value  = $default_country;
                $field_label    = $country_label;
                $field_suffix   = 'countryCode';
                $modifier       = 'countries';
                $selected_value = esc_attr( $stored_value );
                $values         = $countries;
                break;
            case 'state':
                $field_label    = $province_state_label;
                $field_suffix   = 'stateCode';
                $modifier       = 'states';
                $selected_value = esc_attr( $stored_value );
                $values         = $prefill_states;
                break;
            case 'city':
                $field_label    = $city_label;
                $field_suffix   = 'cityName';
                $modifier       = 'cities';
                $selected_value = esc_attr( $stored_value );
                $values         = $prefill_cities;
                break;
        }
        $dropdown_class = $dropdown_class . ' ' . $woocs_dropdown . '--' . $modifier;

        ob_start();
        ?>
        <div class="woocs__dropdown-box woocs__dropdown-box--<?php echo $modifier; ?>">
            <label for="<?php echo $field_id . $field_suffix; ?>" class="screen-reader-text">
                <?php echo $field_label; ?>
            </label>
            <select name="<?php echo $field_name; ?>[<?php echo $field_suffix; ?>]" id="<?php echo $field_id . $field_suffix; ?>" class="<?php echo $dropdown_class; ?>" data-show-labels="<?php echo $data_label_value; ?>" data-which-fields="<?php echo $which_fields; ?>">
                <?php
                    if ( ! empty( $values ) ) {
                        foreach ( $values as $key => $label ) {
                            $selected = false;
                            if ( ! empty( $selected_value ) ) {
                                $selected = ( $selected_value == $key ) ? $selected_selected : false;
                            } elseif ( ! empty( $default_value ) ) {
                                // only when a default country is set
                                $selected = ( $default_value == $key ) ? $selected_selected : false;
                            }
                            echo '<option value="' . $key . '"' . $selected . '>' . $label . '</option>';
                        }
                    }
                ?>
            </select>
        </div>
        <?php
        $dropdown = ob_get_clean();

        return $dropdown;
    }


    /**
     * Verify CSV data
     *
     * @param        $file_name
     * @param string $delimiter
     * @param bool   $verify
     */
    function woocs_verify_data( $file_name, $delimiter = ';', $verify = true ) {
        $csv_array = woocs_csv_to_array( $file_name, '', $delimiter, $verify );
        if ( isset( $csv_array[ 'data' ] ) ) {
            Woocommerce_City_Selector::woocs_errors()->add( 'success_no_errors_in_csv', sprintf( esc_html__( 'Congratulations, there appear to be no errors in CSV file: "%s".', 'woocommerce-city-selector' ), $file_name ) );
            do_action( 'woocs_after_success_verify' );
        }
    }


    /**
     * Import CSV data
     *
     * @param        $file_name
     * @param string $upload_folder
     * @param string $delimiter
     * @param false  $verify
     * @param false  $max_lines
     */
    function woocs_import_data( $file_name, $upload_folder = '', $delimiter = ';', $verify = false, $max_lines = false ) {
        if ( $file_name ) {
            if ( strpos( $file_name, '.csv', -4 ) !== false ) {
                $csv_array = woocs_csv_to_array( $file_name, $upload_folder, $delimiter, $verify, $max_lines );

                if ( ! is_wp_error( $csv_array ) ) {
                    if ( isset( $csv_array[ 'data' ] ) && ! empty( $csv_array[ 'data' ] ) ) {
                        $line_number = 0;
                        foreach ( $csv_array[ 'data' ] as $line ) {
                            $line_number++;

                            $city_row = array(
                                'city_name'    => $line[ 0 ],
                                'state_code'   => $line[ 1 ],
                                'state_name'   => $line[ 2 ],
                                'country_code' => $line[ 3 ],
                                'country'      => $line[ 4 ],
                            );

                            global $wpdb;
                            $wpdb->insert( $wpdb->prefix . 'cities', $city_row );
                        }
                        if ( in_array( $file_name, [ 'be.csv', 'nl.csv' ] ) ) {
                            $country_code = substr( $file_name, 0, 2 );
                            Woocommerce_City_Selector::woocs_errors()->add( 'success_lines_imported_' . $country_code, sprintf( esc_html__( 'You have successfully imported %d cities from "%s".', 'woocommerce-city-selector' ), $line_number, $file_name ) );
                        } else {
                            Woocommerce_City_Selector::woocs_errors()->add( 'success_lines_imported', sprintf( esc_html__( 'You have successfully imported %d cities from "%s".', 'woocommerce-city-selector' ), $line_number, $file_name ) );
                        }

                        do_action( 'woocs_after_success_import' );
                    }
                }
            } else {
                // raw data
                global $wpdb;
                $line_number   = 0;
                $verified_data = $file_name;

                foreach ( $verified_data as $line ) {
                    $line_number++;

                    $city_row = array(
                        'city_name'    => $line[ 0 ],
                        'state_code'   => $line[ 1 ],
                        'state_name'   => $line[ 2 ],
                        'country_code' => $line[ 3 ],
                        'country'      => $line[ 4 ],
                    );

                    $wpdb->insert( $wpdb->prefix . 'cities', $city_row );
                }
                Woocommerce_City_Selector::woocs_errors()->add( 'success_cities_imported', sprintf( _n( 'Congratulations, you imported 1 city.', 'Congratulations, you imported %d cities.', $line_number, 'woocommerce-city-selector' ), $line_number ) );

                do_action( 'woocs_after_success_import_raw' );
            }
        } else {
            Woocommerce_City_Selector::woocs_errors()->add( 'error_no_file_selected', esc_html__( "You didn't select a file.", 'woocommerce-city-selector' ) );
        }

    }

    /**
     * Remove an uploaded file
     *
     * @param false $file_name
     */
    function woocs_delete_file( $file_name = false ) {
        if ( false != $file_name ) {
            if ( file_exists( woocs_upload_folder( '/' ) . $file_name ) ) {
                $delete_result = unlink( woocs_upload_folder( '/' ) . $file_name );
                if ( true === $delete_result ) {
                    Woocommerce_City_Selector::woocs_errors()->add( 'success_file_deleted', sprintf( esc_html__( 'File "%s" successfully deleted.', 'woocommerce-city-selector' ), $file_name ) );
                    do_action( 'woocs_after_success_file_delete' );
                } else {
                    Woocommerce_City_Selector::woocs_errors()->add( 'error_file_deleted', sprintf( esc_html__( 'File "%s" is not deleted. Please try again.', 'woocommerce-city-selector' ), $file_name ) );
                }
            }
        }
    }


    /**
     * Delete one or more countries
     *
     * @param $countries
     */
    function woocs_delete_country( $countries ) {
        $country_names_and       = false;
        $sanitized_country_codes = array();
        foreach( $countries as $country_code ) {
            $sanitized_country_code    = sanitize_text_field( $country_code );
            $sanitized_country_codes[] = $sanitized_country_code;
            $country_names[]           = woocs_get_country_name( $sanitized_country_code );
        }
        if ( ! empty( $country_names ) ) {
            $country_names_quotes = "'" . implode( "', '", $country_names ) . "'";
            if ( 1 < count( $country_names ) ) {
                $country_names_and = substr_replace( $country_names_quotes, ' and', strrpos( $country_names_quotes, ',' ), 1 );
            } else {
                $country_names_and = $country_names_quotes;
            }
        }

        if ( ! empty( $sanitized_country_codes ) ) {
            global $wpdb;
            $country_string = strtoupper( "'" . implode( "', '", $sanitized_country_codes ) . "'" );
            $query          = "DELETE FROM {$wpdb->prefix}cities WHERE `country_code` IN ({$country_string})";
            $result         = $wpdb->query( $query );
            if ( $result > 0 ) {
                Woocommerce_City_Selector::woocs_errors()->add( 'success_country_remove', sprintf( esc_html__( 'You have successfully removed all entries for %s.', 'woocommerce-city-selector' ), $country_names_and ) );
                foreach( $countries as $country_code ) {
                    do_action( 'woocs_delete_transients', $country_code );
                }
            }
        }
    }


    /**
     * Get upload folder for plugin, can be overriden with filter
     *
     * @param false $suffix
     *
     * @return mixed|void
     */
    function woocs_upload_folder( $suffix = false ) {
        $upload_folder = apply_filters( 'woocs_upload_folder', wp_upload_dir()[ 'basedir' ] . '/woocs' . $suffix );

        return $upload_folder;
    }


    /**
     * Render preview results
     *
     * @param $csv_data
     *
     * @since 1.5.0
     *
     * @return false|string
     */
    function woocs_render_preview_results( $csv_data = [] ) {
        if ( ! empty( $csv_data ) ) {
            $table_columns = [
                esc_html__( 'City', 'woocommerce-city-selector' ),
                esc_html__( 'State code', 'woocommerce-city-selector' ),
                esc_html__( 'State', 'woocommerce-city-selector' ),
                esc_html__( 'Country code', 'woocommerce-city-selector' ),
                esc_html__( 'Country', 'woocommerce-city-selector' ),
            ];
            ob_start();
            foreach( $table_columns as $column ) {
                echo sprintf( '<th>%s</th>', $column );
            }
            echo '<thead><tr>%s</tr></thead>';
            $table_headers = ob_get_clean();

            ob_start();
            foreach ( $csv_data as $line ) {
                echo '<tr>';
                foreach ( $line as $column ) {
                    echo sprintf( '<td>%s</td>', stripslashes( htmlspecialchars( $column ) ) );
                }
                echo '</tr>';
            }

            $table_rows = ob_get_clean();
            $table_body = sprintf( '<tbody>%s</tbody>', $table_rows );
            $table      = sprintf( '<table class="woocs__table woocs__table--preview-result scrollable">%s%s</table>', $table_headers, $table_body );

            return $table;
        }

        return '';
    }


    /**
     * Get optgroups for states
     *
     * @since 1.5.0
     *
     * @return array
     */
    function woocs_get_states_optgroup() {
        $results = woocs_get_countries( false );
        // if there is at least 1 country
        if ( ! empty( $results ) ) {
            foreach ( $results as $country_code => $label ) {
                $countries[] = [
                    'code' => $country_code,
                    'name' => esc_attr__( $label, 'woocommerce-city-selector' ),
                ];
            }

            // get states for these countries
            if ( ! empty( $countries ) ) {
                global $wpdb;
                $states = array();
                foreach ( $countries as $country ) {
                    $states[] = array(
                        'state' => 'open_optgroup',
                        'name'  => esc_attr__( woocs_get_country_name( $country[ 'code' ] ), 'woocommerce-city-selector' ),
                    );

                    $order = 'ORDER BY state_name ASC';
                    if ( 'FR' == $country[ 'code' ] ) {
                        $order = "ORDER BY LENGTH(state_name), state_name";
                    }

                    $query   = "SELECT * FROM " . $wpdb->prefix . "cities WHERE country_code = %s GROUP BY state_code " . $order;
                    $sql     = $wpdb->prepare( $query, $country[ 'code' ] );
                    $results = $wpdb->get_results( $sql );

                    if ( count( $results ) > 0 ) {
                        foreach ( $results as $data ) {
                            $states[] = array(
                                'state' => strtolower( $data->country_code ) . '-' . strtolower( $data->state_code ),
                                'name'  => esc_attr__( $data->state_name, 'woocommerce-city-selector' ),
                            );
                        }
                    }

                    $states[] = array(
                        'state' => 'close_optgroup',
                        'name'  => '',
                    );
                }

                return $states;
            }
        }

        return [];
    }


    /**
     * Get search results (admin)
     *
     * @since 1.5.0
     *
     * @return array|object|stdClass[]|null
     */
    function woocs_get_searched_cities() {
        global $wpdb;
        $search_criteria_state   = ( isset( $_POST[ 'woocs_state' ] ) ) ? sanitize_text_field( $_POST[ 'woocs_state' ] ) : false;
        $search_criteria_country = ( isset( $_POST[ 'woocs_country' ] ) ) ? sanitize_text_field( $_POST[ 'woocs_country' ] ) : false;
        $search_limit            = false;
        $searched_orderby        = ( ! empty( $_POST[ 'woocs_orderby' ] ) ) ? sanitize_text_field( $_POST[ 'woocs_orderby' ] ) : false;
        $searched_term           = ( ! empty( $_POST[ 'woocs_search' ] ) ) ? sanitize_text_field( $_POST[ 'woocs_search' ] ) : false;
        $selected_limit          = ( ! empty( $_POST[ 'woocs_limit' ] ) ) ? (int) $_POST[ 'woocs_limit' ] : 100;
        $where                   = array();

        if ( false != $search_criteria_state ) {
            $where[] = "state_code = '" . substr( $search_criteria_state, 3, 3) . "' AND country_code = '" . substr( $search_criteria_state, 0, 2) . "'";
        } elseif ( false != $search_criteria_country ) {
            $where[] = "country_code = '" . $search_criteria_country . "'";
        }
        if ( false != $searched_term ) {
            $search[] = 'city_name LIKE "%' . $searched_term . '%"';

            if ( $search_criteria_country || $search_criteria_state ) {
                $where[] = '(' . implode( ' OR ', $search ) . ')';
            } else {
                $where[] = implode( ' OR ', $search );
            }

        }
        if ( 0 != $selected_limit ) {
            $search_limit = "LIMIT " . $selected_limit;
        }

        if ( ! empty( $where ) ) {
            $where   = "WHERE " . implode( ' AND ', $where );
        } else {
            $where = false;
        }

        if ( 'state' == $searched_orderby ) {
            $orderby = 'ORDER BY state_name ASC, city_name ASC';
        } else {
            $orderby = 'ORDER BY city_name ASC, state_name ASC';
        }

        $sql = "SELECT * FROM " . $wpdb->prefix . "cities
                " . $where . "
                " . $orderby . "
                " . $search_limit . "
            ";
        $cities = $wpdb->get_results( $sql );

        return $cities;
    }

	function woocs_form_field( $key, $args, $value = null ) {
		$defaults = array(
			'type'              => 'text',
			'label'             => '',
			'description'       => '',
			'placeholder'       => '',
			'maxlength'         => false,
			'required'          => false,
			'autocomplete'      => false,
			'id'                => $key,
			'class'             => array(),
			'label_class'       => array(),
			'input_class'       => array(),
			'return'            => false,
			'options'           => array(),
			'custom_attributes' => array(),
			'validate'          => array(),
			'default'           => '',
			'autofocus'         => '',
			'priority'          => '',
		);

		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'woocommerce_form_field_args', $args, $key, $value );

		if ( is_string( $args['class'] ) ) {
			$args['class'] = array( $args['class'] );
		}

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required        = '&nbsp;<abbr class="required" title="' . esc_attr__( 'required', 'woocommerce' ) . '">*</abbr>';
		} else {
			$required = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
		}

		if ( is_string( $args['label_class'] ) ) {
			$args['label_class'] = array( $args['label_class'] );
		}

		// Custom attribute handling.
		$custom_attributes           = [];
		$args[ 'custom_attributes' ] = array_filter( (array) $args[ 'custom_attributes' ], 'strlen' );

		$field           = '';
		$label_id        = $args['id'];
		$sort            = $args['priority'] ? $args['priority'] : '';
		$field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</p>';

		switch ( $args['type'] ) {
			case 'country':
				$countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

				if ( 1 === count( $countries ) ) {

					$field .= '<strong>' . current( array_values( $countries ) ) . '</strong>';

					$field .= '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . current( array_keys( $countries ) ) . '" ' . implode( ' ', $custom_attributes ) . ' class="country_to_state" readonly="readonly" />';

				} else {
					$data_label = ! empty( $args['label'] ) ? 'data-label="' . esc_attr( $args['label'] ) . '"' : '';

					$field = '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="country_to_state country_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ? $args['placeholder'] : esc_attr__( 'Select a country / region&hellip;', 'woocommerce' ) ) . '" ' . $data_label . '><option value="">' . esc_html__( 'Select a country / region&hellip;', 'woocommerce' ) . '</option>';

					foreach ( $countries as $ckey => $cvalue ) {
						$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
					}

					$field .= '</select>';

					$field .= '<noscript><button type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__( 'Update country / region', 'woocommerce' ) . '">' . esc_html__( 'Update country / region', 'woocommerce' ) . '</button></noscript>';

				}
				break;

		}

		if ( ! empty( $field ) ) {
			$field_html = '';

			if ( $args['label'] && 'checkbox' !== $args['type'] ) {
				$field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) . '">' . wp_kses_post( $args['label'] ) . $required . '</label>';
			}

			$field_html .= '<span class="woocommerce-input-wrapper">' . $field;

			if ( $args['description'] ) {
				$field_html .= '<span class="description" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
			}

			$field_html .= '</span>';

		} else {
			$field_html  = '';
			// echo $key;
			$description = "Override for {$key} comes here";
			$field_html  .= sprintf( '<span class="woocommerce-input-wrapper">%s</span>', $key );

		}
		$container_class = esc_attr( implode( ' ', $args['class'] ) );
		$container_id    = esc_attr( $args['id'] ) . '_field';
		$field           = sprintf( $field_container, $container_class, $container_id, $field_html );

		if ( $args[ 'return' ] ) {
			return $field;
		} else {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $field;
		}
	}
