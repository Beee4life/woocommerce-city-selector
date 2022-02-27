<?php
    /*
     * Content for the settings page
     */
    function woocs_settings() {

        if ( ! current_user_can( apply_filters( 'woocs_user_cap', 'manage_options' ) ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
        }
        $countries = woocs_get_countries( false, false, true );

        Woocommerce_City_Selector::woocs_show_admin_notices();
        ?>

        <div class="wrap acfcs">
            <h1>Woocommerce City Selector</h1>

            <?php echo Woocommerce_City_Selector::woocs_admin_menu(); ?>

            <div class="acfcs__container">
                <div class="admin_left">
                    <div class="content">
                        <form method="post" action="">
                            <input name="woocs_import_actions_nonce" value="<?php echo wp_create_nonce( 'woocs-import-actions-nonce' ); ?>" type="hidden" />
                            <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Import countries', 'acf-city-selector' ) ); ?>
                            <?php echo sprintf( '<p>%s</p>', esc_html__( 'Here you can (re-)import all cities for the individual countries listed below.', 'acf-city-selector' ) ); ?>
                            <ul class="acfcs__checkboxes">
                                <li>
                                    <?php echo sprintf( '<label for="import_be" class="screen-reader-text">%s</label>', esc_attr__( 'Import all cities in Belgium', 'acf-city-selector' ) ); ?>
                                    <input type="checkbox" name="woocs_import_be" id="import_be" value="1" /> <?php esc_html_e( 'Import all cities in Belgium', 'acf-city-selector' ); ?> (1166)
                                </li>
                                <li>
                                    <?php echo sprintf( '<label for="import_nl" class="screen-reader-text">%s</label>', esc_attr__( 'Import all cities in Holland/The Netherlands', 'acf-city-selector' ) ); ?>
                                    <input type="checkbox" name="woocs_import_nl" id="import_nl" value="1" /> <?php esc_html_e( 'Import all cities in Holland/The Netherlands', 'acf-city-selector' ); ?> (2449)
                                </li>
                            </ul>

                            <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Import selected countries', 'acf-city-selector' ); ?>" />
                        </form>

                        <br /><hr />

                        <?php if ( ! empty( $countries ) ) { ?>
                            <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Remove countries', 'acf-city-selector' ) ); ?>
                            <form method="post" action="">
                                <input name="woocs_remove_countries_nonce" value="<?php echo wp_create_nonce( 'woocs-remove-countries-nonce' ); ?>" type="hidden" />
                                <?php echo sprintf( '<p>%s</p>', esc_html__( "Here you can remove a country and all its states and cities from the database.", 'acf-city-selector' ) ); ?>
                                <ul class="acfcs__checkboxes">
                                    <?php foreach( $countries as $key => $value ) { ?>
                                        <li>
                                            <?php echo sprintf( '<label for="%s" class="screen-reader-text">%s</label>', 'delete_' . strtolower( $key ), esc_attr__( $value, 'acf-city-selector' ) ); ?>
                                            <input type="checkbox" name="woocs_delete_country[]" id="delete_<?php echo strtolower( $key ); ?>" value="<?php echo strtolower( $key ); ?>" /> <?php esc_html_e( $value, 'acf-city-selector' ); ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Delete selected countries', 'acf-city-selector' ); ?>" />
                            </form>

                            <br /><hr />
                        <?php } ?>

                        <form method="post" action="">
                            <input name="woocs_delete_transients" value="<?php echo wp_create_nonce( 'woocs-delete-transients-nonce' ); ?>" type="hidden" />
                            <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Delete transients', 'acf-city-selector' ) ); ?>
                            <?php echo sprintf( '<p>%s</p>', esc_html__( "If you're seeing unexpected results in your dropdowns, try clearing all transients with this option.", 'acf-city-selector' ) ); ?>
                            <input type="submit" class="button button-primary" value="<?php esc_html_e( "Delete transients", 'acf-city-selector' ); ?>" />
                        </form>

                        <br /><hr />

                        <form method="post" action="">
                            <input name="woo_truncate_table_nonce" value="<?php echo wp_create_nonce( 'woo-truncate-table-nonce' ); ?>" type="hidden" />
                            <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Clear the database', 'acf-city-selector' ) ); ?>
                            <?php echo sprintf( '<p>%s</p>', esc_html__( "By selecting this option, you will remove all cities, which are present in the database. This is useful if you don't need the preset cities or you want a fresh start.", 'acf-city-selector' ) ); ?>
                            <input type="submit" class="button button-primary"  onclick="return confirm( 'Are you sure you want to delete all cities ?' )" value="<?php esc_html_e( 'Delete everything', 'acf-city-selector' ); ?>" />
                        </form>

                        <br /><hr />

                        <form method="post" action="">
                            <input name="woo_remove_cities_nonce" value="<?php echo wp_create_nonce( 'woo-remove-cities-nonce' ); ?>" type="hidden" />
                            <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Delete data', 'acf-city-selector' ) ); ?>
                            <?php echo sprintf( '<p>%s</p>', esc_html__( 'When the plugin is deleted, all cities are not automatically deleted. Select this option to delete the cities table as well upon deletion.', 'acf-city-selector' ) ); ?>
                            <?php $checked = get_option( 'woocs_delete_cities_table' ) ? ' checked="checked"' : false; ?>
                            <ul>
                                <li>
                                    <span class="acfcs_input">
                                        <?php echo sprintf( '<label for="remove_cities_table" class="screen-reader-text">%s</label>', esc_attr__( 'Remove cities table on plugin deletion', 'acf-city-selector' ) ); ?>
                                        <input type="checkbox" name="remove_cities_table" id="remove_cities_table" value="1" <?php echo $checked; ?>/> <?php esc_html_e( 'Remove cities table on plugin deletion', 'acf-city-selector' ); ?>
                                    </span>
                                </li>
                            </ul>
                            <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Save settings', 'acf-city-selector' ); ?>" />
                        </form>
                    </div>
                </div>

                <?php include 'admin-right.php'; ?>

            </div>

        </div>
        <?php
    }
