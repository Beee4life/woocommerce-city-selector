<?php
    /*
     * Content for the search page
     */
    function woocs_search() {

        if ( ! current_user_can( apply_filters( 'woocs_user_cap', 'manage_options' ) ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
        }

        Woocommerce_City_Selector::woocs_show_admin_notices();

        $all_countries           = woocs_get_countries( false );
        $cities                  = array();
        $city_array              = array();
        $countries               = array();
        $search_criteria_state   = ( isset( $_POST[ 'woocs_state' ] ) ) ? sanitize_text_field( $_POST[ 'woocs_state' ] ) : false;
        $search_criteria_country = ( isset( $_POST[ 'woocs_country' ] ) ) ? sanitize_text_field( $_POST[ 'woocs_country' ] ) : false;
        $searched_orderby        = ( ! empty( $_POST[ 'woocs_orderby' ] ) ) ? sanitize_text_field( $_POST[ 'woocs_orderby' ] ) : false;
        $searched_term           = ( ! empty( $_POST[ 'woocs_search' ] ) ) ? sanitize_text_field( $_POST[ 'woocs_search' ] ) : false;
        $selected_limit          = ( ! empty( $_POST[ 'woocs_limit' ] ) ) ? (int) $_POST[ 'woocs_limit' ] : 100;
        $states                  = woocs_get_states_optgroup();

        // if there is at least 1 country
        if ( ! empty( $all_countries ) ) {
            foreach ( $all_countries as $country_code => $label ) {
                $countries[] = [
                    'code' => $country_code,
                    'name' => esc_attr__( $label, 'woocommerce-city-selector' ),
                ];
            }
        }

        // if user has searched
        if ( isset( $_POST[ 'woocs_search_form' ] ) ) {
            $cities = woocs_get_searched_cities();

            foreach( $cities as $city_object ) {
                $city_array[] = (array) $city_object;
            }

            if ( ! empty( $city_array ) ) {
                uasort( $city_array, 'woocs_sort_array_with_quotes' );
            }
            $result_count = count( $city_array );
        }
        ?>
        <div class="wrap acfcs">
            <h1>Woocommerce City Selector</h1>

            <?php echo Woocommerce_City_Selector::woocs_admin_menu(); ?>

            <div class="acfcs__container">
                <div class="admin_left">
                    <div class="content">
                        <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Search for cities', 'woocommerce-city-selector' ) ); ?>

                        <?php if ( count( $countries ) > 0 ) { ?>
                            <form action="" method="POST">
                                <input name="woocs_search_form" type="hidden" value="1" />

                                <div class="acfcs__search-form">
                                    <?php // if there's only 1 country, no need to add country dropdown ?>
                                    <?php if ( count( $countries ) > 1 ) { ?>
                                        <div class="acfcs__search-criteria acfcs__search-criteria--country">
                                            <label for="woocs_country" class="screen-reader-text"><?php echo apply_filters( 'woocs_select_country_label', esc_html__( 'Select a country', 'woocommerce-city-selector' ) ); ?></label>
                                            <select name="woocs_country" id="woocs_country">
                                                <option value="">
                                                    <?php echo apply_filters( 'woocs_select_country_label', esc_html__( 'Select a country', 'woocommerce-city-selector' ) ); ?>
                                                </option>
                                                <?php foreach( $countries as $country ) { ?>
                                                    <?php $selected = ( $country[ 'code' ] == $search_criteria_country ) ? ' selected="selected"' : false; ?>
                                                    <option value="<?php echo $country[ 'code' ]; ?>"<?php echo $selected; ?>>
                                                        <?php _e( $country[ 'name' ], 'woocommerce-city-selector' ); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="acfcs__search-criteria acfcs__search-criteria--or">
                                            <small><?php esc_html_e( 'OR', 'woocommerce-city-selector' ); ?></small>
                                        </div>
                                    <?php } ?>

                                    <div class="acfcs__search-criteria acfcs__search-criteria--state">
                                        <label for="woocs_state" class="screen-reader-text">
                                            <?php echo apply_filters( 'woocs_select_province_state_label', esc_html__( 'Select a province/state', 'woocommerce-city-selector' ) ); ?>
                                        </label>
                                        <select name="woocs_state" id="woocs_state">
                                            <option value="">
                                                <?php echo apply_filters( 'woocs_select_province_state_label', esc_html__( 'Select a province/state', 'woocommerce-city-selector' ) ); ?>
                                            </option>
                                            <?php
                                                foreach( $states as $state ) {
                                                    if ( 'open_optgroup' == $state[ 'state' ] ) {
                                                        echo '<optgroup label="'. $state[ 'name' ] . '">';
                                                    }
                                                    if ( strpos( $state[ 'state' ], 'optgroup' ) === false ) {
                                                        $selected = ( $state[ 'state' ] == $search_criteria_state ) ? ' selected="selected"' : false;
                                                        echo '<option value="' . $state[ 'state' ] . '"' . $selected . '>' . esc_html__( $state[ 'name' ], 'woocommerce-city-selector' ) . '</option>';
                                                    }
                                                    if ( 'close_optgroup' == $state[ 'state' ] ) {
                                                        echo '</optgroup>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="acfcs__search-criteria acfcs__search-criteria--plus">+</div>

                                    <div class="acfcs__search-criteria acfcs__search-criteria--search">
                                        <?php echo sprintf( '<label for="woocs_search" class="screen-reader-text">%s</label>', esc_attr__( 'Search term', 'woocommerce-city-selector' ) ); ?>
                                        <input name="woocs_search" id="woocs_search" type="text" value="<?php if ( false != $searched_term ) { echo stripslashes( $searched_term ); } ?>" placeholder="<?php esc_html_e( 'City name', 'woocommerce-city-selector' ); ?>">
                                    </div>

                                    <div class="acfcs__search-criteria acfcs__search-criteria--plus">+</div>

                                    <div class="acfcs__search-criteria acfcs__search-criteria--limit">
                                        <?php echo sprintf( '<label for="woocs_limit" class="screen-reader-text">%s</label>', esc_attr__( 'Limit', 'woocommerce-city-selector' ) ); ?>
                                        <input name="woocs_limit" id="woocs_limit" type="number" value="<?php if ( false != $selected_limit ) { echo $selected_limit; } ?>" placeholder="<?php esc_html_e( 'Limit', 'woocommerce-city-selector' ); ?>">
                                    </div>

                                    <div class="acfcs__search-criteria acfcs__search-criteria--plus">+</div>

                                    <div class="acfcs__search-criteria acfcs__search-criteria--orderby">
                                        <?php echo sprintf( '<label for="woocs_orderby" class="screen-reader-text">%s</label>', esc_attr__( 'Order by', 'woocommerce-city-selector' ) ); ?>
                                        <select name="woocs_orderby" id="woocs_orderby">
                                            <option value="">
                                                <?php esc_html_e( 'Order by', 'woocommerce-city-selector' ); ?>
                                            </option>
                                            <?php
                                                $orderby = [
                                                    esc_attr__( 'City', 'woocommerce-city-selector' ),
                                                    esc_attr__( 'State', 'woocommerce-city-selector' ),
                                                ];
                                                foreach( $orderby as $criterium ) {
                                                    $selected = ( $criterium == $searched_orderby ) ? ' selected' : false;
                                                    echo '<option value="' . $criterium . '" ' . $selected . '>' . ucfirst( $criterium ) . '</option>';
                                                }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="acfcs__search-criteria acfcs__search-criteria--submit">
                                        <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Search', 'woocommerce-city-selector' ); ?>" />
                                    </div>
                                </div>
                            </form>
                        <?php } ?>

                        <?php // Results output below ?>
                        <?php if ( isset( $_POST[ 'woocs_search_form' ] ) && empty( $cities ) ) { ?>
                            <p>
                                <br />
                                <?php _e( 'No results, please try again.', 'woocommerce-city-selector'); ?>
                            </p>
                        <?php } elseif ( ! empty( $cities ) ) { ?>
                            <form enctype="multipart/form-data" action="" method="POST">
                                <input name="woocs_delete_row_nonce" type="hidden" value="<?php echo wp_create_nonce( 'woocs-delete-row-nonce' ); ?>" />
                                <div class="acfcs__search-results">
                                    <?php echo sprintf( '<p class="hide568">%s</p>', __( 'Table scrolls horizontally.', 'woocommerce-city-selector' ) ); ?>
                                    <?php echo sprintf( '<p>%d %s</p>', $result_count, _n( 'result',  'results', $result_count, 'woocommerce-city-selector' ) ); ?>
                                    <?php
                                        $table_headers = [
                                            __( 'ID', 'woocommerce-city-selector' ),
                                            __( 'Select', 'woocommerce-city-selector' ),
                                            __( 'City', 'woocommerce-city-selector' ),
                                            __( 'State', 'woocommerce-city-selector' ),
                                            __( 'Country', 'woocommerce-city-selector' ),
                                        ];
                                    ?>

                                    <table class="acfcs__table acfcs__table--search scrollable">
                                        <thead>
                                        <tr>
                                            <?php foreach( $table_headers as $column ) { ?>
                                                <?php echo sprintf( '<th>%s</th>', $column ); ?>
                                            <?php } ?>
                                        </tr>
                                        </thead>
                                        <?php foreach( $city_array as $city ) { ?>
                                            <tr>
                                                <?php echo sprintf( '<td>%s</td>', $city[ 'id' ] ); ?>
                                                <?php echo sprintf( '<td>%s</td>', sprintf( '<label>%s</label>', sprintf( '<input name="row_id[]" type="checkbox" value="%s %s">', $city[ 'id' ], $city[ 'city_name' ] ) ) ); ?>
                                                <?php echo sprintf( '<td>%s</td>', $city[ 'city_name' ] ); ?>
                                                <?php echo sprintf( '<td>%s</td>', $city[ 'state_name' ] ); ?>
                                                <?php echo sprintf( '<td>%s</td>', __( $city[ 'country' ], 'woocommerce-city-selector' ) ); ?>
                                            </tr>
                                        <?php } ?>
                                    </table>

                                    <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Delete selected', 'woocommerce-city-selector' ); ?>" />
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                </div>

                <?php include 'admin-right.php'; ?>

            </div>

        </div>
        <?php
    }

