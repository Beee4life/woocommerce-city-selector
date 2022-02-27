<?php
    /*
     * Content for the settings page
     */
    function woocs_info_page() {

        if ( ! current_user_can( apply_filters( 'woocs_user_cap', 'manage_options' ) ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
        }

        Woocommerce_City_Selector::woocs_show_admin_notices();

        $countries    = acfcs_get_countries_info();
        $prepare_json = array();
        ?>

        <div class="wrap acfcs">
            <h1>Woocommerce City Selector</h1>

            <?php echo Woocommerce_City_Selector::woocs_admin_menu(); ?>

            <div class="acfcs__container">
                <div class="admin_left">
                    <div class="content">
                        <div class="acfcs__section">
                            <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Info', 'woocommerce-city-selector' ) ); ?>
                            <p>
                                <?php esc_html_e( 'This page shows real-time info about your site and settings.', 'woocommerce-city-selector' ); ?>
                                <br />
                                <?php esc_html_e( 'We might ask for this info if support is helping you fix a problem.', 'woocommerce-city-selector' ); ?>
                            </p>
                        </div>

                        <div class="acfcs__section acfcs__section--countries">
                            <?php if ( ! empty( $countries ) ) { ?>
                                <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Countries in database', 'woocommerce-city-selector' ) ); ?>

                                <table class="acfcs__table acfcs__table--info">
                                    <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Country name', 'woocommerce-city-selector' ); ?></th>
                                        <th><?php esc_html_e( '# cities', 'woocommerce-city-selector' ); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach( $countries as $country_code => $values ) { ?>
                                        <?php $prepare_json[ 'countries' ][ $country_code ] = $values[ 'name' ] . ' (' . $values[ 'count' ] . ')'; ?>
                                        <tr>
                                            <td><?php echo $values[ 'name' ]; ?></td>
                                            <td><?php echo $values[ 'count' ]; ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <?php $prepare_json[ 'countries' ] = 'none'; ?>
                            <?php } ?>

                            <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Server info', 'woocommerce-city-selector' ) ); ?>

                            <table class="acfcs__table acfcs__table--info">
                                <thead>
                                <tr>
                                    <th><?php esc_html_e( 'What ?', 'woocommerce-city-selector' ); ?></th>
                                    <th><?php esc_html_e( 'Value', 'woocommerce-city-selector' ); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <?php $prepare_json[ 'server_info' ][ 'operating_system' ] = $_SERVER[ 'SERVER_SOFTWARE' ]; ?>
                                    <td><?php esc_html_e( 'Operating system', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo $_SERVER[ 'SERVER_SOFTWARE' ]; ?></td>
                                </tr>
                                <tr>
                                    <?php $prepare_json[ 'server_info' ][ 'phpversion' ] = phpversion(); ?>
                                    <td><?php esc_html_e( 'PHP version', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo phpversion(); ?></td>
                                </tr>
                                <tr>
                                    <?php $prepare_json[ 'server_info' ][ 'server_ip' ] = $_SERVER[ 'SERVER_ADDR' ]; ?>
                                    <td><?php esc_html_e( 'Server IP', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo $_SERVER[ 'SERVER_ADDR' ]; ?></td>
                                </tr>
                                <tr>
                                    <?php $prepare_json[ 'server_info' ][ 'server_port' ] = $_SERVER[ 'SERVER_PORT' ]; ?>
                                    <td><?php esc_html_e( 'Server port', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo $_SERVER[ 'SERVER_PORT' ]; ?></td>
                                </tr>
                                <tr>
                                    <?php $prepare_json[ 'server_info' ][ 'scheme' ] = $_SERVER[ 'REQUEST_SCHEME' ]; ?>
                                    <td><?php esc_html_e( 'Scheme', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo $_SERVER[ 'REQUEST_SCHEME' ]; ?></td>
                                </tr>
                                <tr>
                                    <?php $prepare_json[ 'server_info' ][ 'document_root' ] = $_SERVER[ 'DOCUMENT_ROOT' ]; ?>
                                    <td><?php esc_html_e( 'Home path', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo $_SERVER[ 'DOCUMENT_ROOT' ]; ?></td>
                                </tr>
                                </tbody>
                            </table>

                            <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Wordpress info', 'woocommerce-city-selector' ) ); ?>

                            <table class="acfcs__table acfcs__table--info">
                                <thead>
                                <tr>
                                    <th><?php esc_html_e( 'What ?', 'woocommerce-city-selector' ); ?></th>
                                    <th><?php esc_html_e( 'Value', 'woocommerce-city-selector' ); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <?php $prepare_json[ 'wordpress_info' ][ 'wordpress_version' ] = get_bloginfo( 'version' ); ?>
                                    <td><?php esc_html_e( 'WordPress version', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo get_bloginfo( 'version' ); ?></td>
                                </tr>
                                <tr>
                                    <?php $prepare_json[ 'wordpress_info' ][ 'home_url' ] = get_home_url(); ?>
                                    <td><?php esc_html_e( 'Home URL', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo get_home_url(); ?></td>
                                </tr>
                                <tr>
                                    <?php $prepare_json[ 'wordpress_info' ][ 'current_theme' ] = get_option( 'current_theme' ); ?>
                                    <td><?php esc_html_e( 'Current theme', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo get_option( 'current_theme' ); ?></td>
                                </tr>
                                <?php $stylesheet = get_option( 'stylesheet' ); ?>
                                <?php $template   = get_option( 'template' ); ?>
                                <?php if ( $stylesheet != $template ) { ?>
                                    <tr>
                                        <?php $prepare_json[ 'wordpress_info' ][ 'stylesheet' ] = $stylesheet; ?>
                                        <td><?php esc_html_e( 'Stylesheet folder', 'woocommerce-city-selector' ); ?></td>
                                        <td><?php echo $stylesheet; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <?php $prepare_json[ 'wordpress_info' ][ 'template' ] = get_option( 'template' ); ?>
                                    <td><?php esc_html_e( 'Template folder', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo get_option( 'template' ); ?></td>
                                </tr>
                                <tr>
                                    <?php $prepare_json[ 'wordpress_info' ][ 'charset' ] = get_option( 'charset' ); ?>
                                    <td><?php esc_html_e( 'Charset', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo get_bloginfo( 'charset' ); ?></td>
                                </tr>
                                <tr>
                                    <?php $text_direction = is_rtl() ? 'RTL' : 'LTR'; ?>
                                    <?php $prepare_json[ 'wordpress_info' ][ 'text_direction' ] = $text_direction; ?>
                                    <td><?php esc_html_e( 'Text direction', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo $text_direction; ?></td>
                                </tr>
                                <tr>
                                    <?php $prepare_json[ 'wordpress_info' ][ 'language' ] = get_bloginfo( 'language' ); ?>
                                    <td><?php esc_html_e( 'Language', 'woocommerce-city-selector' ); ?></td>
                                    <td><?php echo get_bloginfo( 'language' ); ?></td>
                                </tr>
                                </tbody>
                            </table>

                            <?php if ( is_multisite() ) { ?>
                                <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Multisite', 'woocommerce-city-selector' ) ); ?>

                                <table class="acfcs__table acfcs__table--info">
                                    <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'What ?', 'woocommerce-city-selector' ); ?></th>
                                        <th><?php esc_html_e( 'Value', 'woocommerce-city-selector' ); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <?php $main_site = ( is_main_site() ) ? esc_html__( 'Yes', 'woocommerce-city-selector' ) : esc_html__( 'No', 'woocommerce-city-selector' ); ?>
                                        <?php $prepare_json[ 'multisite' ][ 'main_site' ] = $main_site; ?>
                                        <td><?php esc_html_e( 'Main site', 'woocommerce-city-selector' ); ?></td>
                                        <td><?php echo $main_site; ?> </td>
                                    </tr>
                                    <tr>
                                        <?php $registration = ( get_site_option( 'registration' ) ) ? 'TRUE' : 'FALSE'; ?>
                                        <?php $prepare_json[ 'multisite' ][ 'registration' ] = $registration; ?>
                                        <td><?php esc_html_e( 'Main registration', 'woocommerce-city-selector' ); ?></td>
                                        <td><?php echo $registration; ?> </td>
                                    </tr>
                                    <?php if ( class_exists( 'B3Onboarding' ) ) { ?>
                                        <?php $subsite_registration = ( get_option( 'b3_registration_type' ) ) ? 'TRUE' : 'FALSE'; ?>
                                        <?php $prepare_json[ 'multisite' ][ 'subsite_registration' ] = $subsite_registration; ?>
                                        <tr>
                                            <td><?php esc_html_e( 'Subsite registration', 'woocommerce-city-selector' ); ?></td>
                                            <td><?php echo $subsite_registration; ?> </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>

                            <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Active plugins', 'woocommerce-city-selector' ) ); ?>

                            <?php $plugins = get_plugins(); ?>
                            <?php if ( ! empty( $plugins ) ) { ?>
                                <table class="acfcs__table acfcs__table--info">
                                    <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Name', 'woocommerce-city-selector' ); ?></th>
                                        <th><?php esc_html_e( 'Version', 'woocommerce-city-selector' ); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach( $plugins as $key => $value ) { ?>
                                        <?php if ( is_plugin_active( $key ) ) { ?>
                                            <?php $prepare_json[ 'plugins' ][] = [ 'name' => $value[ 'Name' ], 'version' => $value[ 'Version' ], 'author' => $value[ 'Author' ], 'author_uri' => $value[ 'AuthorURI' ] ]; ?>
                                            <tr>
                                                <td><?php echo $value[ 'Name' ]; ?></td>
                                                <td><?php echo $value[ 'Version' ]; ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>

                        <?php $file_name = acfcs_upload_folder( '/' ) . 'debug.json'; ?>
                        <div class="acfcs__section acfcs__section--export">
                            <?php echo sprintf( '<h2>%s</h2>', esc_html__( 'Download JSON', 'woocommerce-city-selector' ) ); ?>
                            <p>
                                <?php esc_html_e( "If you're in need of support, the info above might helpful for us to fix a problem.", 'woocommerce-city-selector' ); ?>
                                <?php if ( file_exists( $file_name ) ) { ?>
                                <br />
                                <?php esc_html_e( 'You can download the settings to a JSON file below (and send it to us when asked).', 'woocommerce-city-selector' ); ?>
                                <?php } ?>
                            </p>
                            <?php if ( file_exists( $file_name ) ) { ?>
                                <?php $serialized_json = json_encode( $prepare_json ); // encode json before saving ?>
                                <?php file_put_contents( $file_name, $serialized_json ); // write to file ?>
                                <p class="json_button">
                                    <a href="<?php echo wp_upload_dir()['baseurl'] . '/acfcs/debug.json'; ?>" class="button button-primary">
                                        <?php esc_attr_e( 'View JSON file', 'woocommerce-city-selector' ); ?>
                                    </a> <small>(<?php _e( 'left-click to open, right-click to save', 'woocommerce-city-selector' ); ?>)</small>
                                </p>
                            <?php } else { ?>
                                <?php echo sprintf( '<p>%s</p>', esc_html__( 'The debug.json file is missing. Please deactivate and reactivate the plugin.', 'woocommerce-city-selector' ) ); ?>
                            <?php } ?>
                        </div>

                    </div>
                </div>

                <?php include 'admin-right.php'; ?>

            </div>
        </div>
        <?php
    }
