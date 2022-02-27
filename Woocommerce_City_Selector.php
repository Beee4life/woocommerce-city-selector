<?php
    /*
    Plugin Name:    Woocommerce City Selector
    Description:    An extension for Woocommerce which allows you to select a city based on country and province/state.
    Version:        0.1
    Tested up to:   5.9.1
    Requires PHP:   7.0
    Author:         Beee
    Author URI:     https://berryplasman.com
    Text Domain:    woocommerce-city-selector
    Domain Path:    /languages
    License:        GPLv2 or later
    License URI:    https://www.gnu.org/licenses/gpl.html
    */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( ! class_exists( 'Woocommerce_City_Selector' ) ) {

        /*
         * Main class
         */
        class Woocommerce_City_Selector {

            /*
             * __construct
             *
             * This function will setup the class functionality
             */
            public function __construct() {

                $this->settings = array(
                    'db_version' => '1.0',
                    'url'        => plugin_dir_url( __FILE__ ),
                    'version'    => '0.1',
                );

                load_plugin_textdomain( 'woocommerce-city-selector', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

                register_activation_hook( __FILE__,             array( $this, 'woocs_plugin_activation' ) );
                register_deactivation_hook( __FILE__,           array( $this, 'woocs_plugin_deactivation' ) );

                add_action( 'admin_enqueue_scripts',                array( $this, 'woocs_add_scripts' ) );
                add_action( 'wp_enqueue_scripts',                   array( $this, 'woocs_add_scripts' ) );

                add_action( 'admin_menu',                           array( $this, 'woocs_add_admin_pages' ), 20 );
                add_action( 'admin_init',                           array( $this, 'woocs_admin_menu' ) );
                add_action( 'admin_init',                           array( $this, 'woocs_errors' ) );
                add_action( 'admin_init',                           array( $this, 'woocs_check_table' ) );
                add_action( 'plugins_loaded',                       array( $this, 'woocs_check_for_woo' ), 6 );

                // add_action( 'acf/input/admin_l10n',                 array( $this, 'acfcs_error_messages' ) );
                add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'woocs_settings_link' ) );

                // functions & hooks
                include 'inc/form-handling.php';
                include 'inc/woocs-actions.php';
                include 'inc/woocs-functions.php';
                include 'inc/woocs-help-tabs.php';
                include 'inc/woocs-i18n.php';
                include 'inc/woocs-ajax.php';
                include 'inc/woo-filters.php';
                include 'inc/wp-actions.php';

                // admin pages
                include 'admin/woocs-dashboard.php';
                include 'admin/woocs-preview.php';
                include 'admin/woocs-settings.php';
                include 'admin/woocs-search.php';
                include 'admin/woocs-info.php';
                include 'admin/woocs-countries.php';

            }


            /*
             * Do stuff upon plugin activation
             */
            public function woocs_plugin_activation() {
                $this->woocs_check_table();
                $this->woocs_check_uploads_folder();
                if ( false == get_option( 'woocs_preserve_settings' ) ) {
                    $this->woocs_fill_database();
                }
            }


            /*
             * Do stuff upon plugin activation
             */
            public function woocs_plugin_deactivation() {
                delete_option( 'woocs_db_version' );
                // this hook is here because didn't want to create a new hook for an existing action
                do_action( 'woocs_delete_transients' );
                // other important stuff gets done in uninstall.php
            }


            /*
             * Prepare database upon plugin activation
             */
            public function woocs_fill_database() {
                $countries = array( 'nl', 'be' );
                foreach( $countries as $country ) {
                    woocs_import_data( $country . '.csv', ACFCS_PLUGIN_PATH . 'import/' );
                }
            }


            /*
             * Check if table exists
             */
            public function woocs_check_table() {
                $woocs_db_version = get_option( 'woocs_db_version', false );
                if ( false == $woocs_db_version || $woocs_db_version != $this->settings[ 'db_version' ] ) {
                    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
                    ob_start();
                    global $wpdb;
                    ?>
                    CREATE TABLE <?php echo $wpdb->prefix; ?>cities (
                    id int(6) unsigned NOT NULL auto_increment,
                    city_name varchar(50) NULL,
                    state_code varchar(3) NULL,
                    state_name varchar(50) NULL,
                    country_code varchar(2) NULL,
                    country varchar(50) NULL,
                    PRIMARY KEY  (id)
                    )
                    COLLATE <?php echo $wpdb->collate; ?>;
                    <?php
                    $sql = ob_get_clean();
                    dbDelta( $sql );
                    update_option( 'woocs_db_version', $this->settings[ 'db_version' ] );
                }
            }


            /*
             * Check if (upload) folder exists
             * If not, create it.
             */
            public static function woocs_check_uploads_folder() {
                $target_folder = woocs_upload_folder( '/' );
                if ( ! file_exists( $target_folder ) ) {
                    mkdir( $target_folder, 0755 );
                }
            }


            /*
             * Error function
             *
             * @return WP_Error
             */
            public static function woocs_errors() {
                static $wp_error;

                return isset( $wp_error ) ? $wp_error : ( $wp_error = new WP_Error( null, null, null ) );
            }


            /*
             * Displays error messages from form submissions
             */
            public static function woocs_show_admin_notices() {
                if ( $codes = Woocommerce_City_Selector::woocs_errors()->get_error_codes() ) {
                    if ( is_wp_error( Woocommerce_City_Selector::woocs_errors() ) ) {
                        $span_class = false;
                        foreach ( $codes as $code ) {
                            if ( strpos( $code, 'success' ) !== false ) {
                                $span_class = 'notice--success ';
                            } elseif ( strpos( $code, 'error' ) !== false ) {
                                $span_class = 'error ';
                            } elseif ( strpos( $code, 'warning' ) !== false ) {
                                $span_class = 'notice--warning ';
                            } elseif ( strpos( $code, 'info' ) !== false ) {
                                $span_class = 'notice--info ';
                            } else {
                                $span_class = 'notice--error ';
                            }
                        }
                        echo sprintf( '<div id="message" class="notice %s is-dismissible">', $span_class );
                        foreach ( $codes as $code ) {
                            echo sprintf( '<p>%s</p>', Woocommerce_City_Selector::woocs_errors()->get_error_message( $code ) );
                        }
                        echo '</div>';
                    }
                }
            }


            /*
             * Add settings link on plugin page
             *
             * @param $links
             *
             * @return array
             */
            public function woocs_settings_link( $links ) {
                $settings_link = [ 'settings' => '<a href="options-general.php?page=woocs-dashboard">' . esc_html__( 'Settings', 'woocommerce-city-selector' ) . '</a>' ];

                return array_merge( $settings_link, $links );
            }


            /*
             * Admin menu
             */
            public static function woocs_admin_menu() {
                $admin_url      = admin_url( 'options-general.php?page=' );
                $current_class  = ' class="current_page"';
                $url_array      = parse_url( esc_url_raw( $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ] ) );
                $acfcs_subpage = ( isset( $url_array[ 'query' ] ) ) ? substr( $url_array[ 'query' ], 11 ) : false;

                $pages = [
                    'dashboard' => esc_html__( 'Dashboard', 'woocommerce-city-selector' ),
                    'settings'  => esc_html__( 'Settings', 'woocommerce-city-selector' ),
                    'search'    => esc_html__( 'Search', 'woocommerce-city-selector' ),
                    'preview'   => esc_html__( 'Preview', 'woocommerce-city-selector' ),
                    'info'      => esc_html__( 'Info', 'woocommerce-city-selector' ),
                ];
                if ( true === woocs_has_cities() ) {
                    $pages[ 'search' ] = esc_html__( 'Search', 'woocommerce-city-selector' );
                }
                if ( ! empty ( woocs_check_if_files() ) ) {
                    $pages[ 'preview' ] = esc_html__( 'Preview', 'woocommerce-city-selector' );
                }
                if ( current_user_can( 'manage_options' ) ) {
                    $pages[ 'info' ] = esc_html__( 'Info', 'woocommerce-city-selector' );
                }

                // $pages[ 'countries' ] = esc_html__( 'Get more countries', 'woocommerce-city-selector' );

                ob_start();
                foreach( $pages as $slug => $label ) {
                    $current_page = ( $acfcs_subpage == $slug ) ? $current_class : false;
                    $current_page = ( 'countries' == $slug ) ? ' class="cta"' : $current_page;
                    echo ( 'dashboard' != $slug ) ? ' | ' : false;
                    echo '<a href="' . $admin_url . 'woocs-' . $slug . '"' . $current_page . '>' . $label . '</a>';
                }
                $menu_items = ob_get_clean();
                $menu       = sprintf( '<p class="acfcs-admin-menu">%s</p>', $menu_items );

                return $menu;
            }


            /*
             * Check if ACF is active and if not add an admin notice
             */
            public function woocs_check_for_woo() {
                if ( ! class_exists( 'Woocommerce' ) ) {
                    add_action( 'admin_notices', function () {
                        $message = sprintf( __( '"%s" is not activated. This plugin <strong>must</strong> be activated, because without it "%s" won\'t work. Activate it <a href="%s">here</a>.', 'woocommerce-city-selector' ),
                            'Woocommerce',
                            'Woocommerce City Selector',
                            esc_url( admin_url( 'plugins.php?s=woocommerce&plugin_status=inactive' ) ) );
                        echo sprintf( '<div class="notice notice-error"><p>%s</p></div>', $message );
                    });
                }
            }


            /*
             * Add admin pages
             */
            public function woocs_add_admin_pages() {
                // add_submenu_page( 'admin.php?page=wc-admin', 'Woocommerce City Selector', 'City Selector', 'manage_options', 'woocs-dashboard', 'woocs_dashboard', 50 );
                add_options_page( 'Woocommerce City Selector', 'Woo City Selector', apply_filters( 'woocs_user_cap', 'manage_options' ), 'woocs-dashboard', 'woocs_dashboard' );
                add_submenu_page( null, 'Preview data', 'Preview data', apply_filters( 'woocs_user_cap', 'manage_options' ), 'woocs-preview', 'woocs_preview_page' );
                add_submenu_page( null, __( 'Settings', 'woo-city-selector' ), __( 'Settings', 'woo-city-selector' ), apply_filters( 'woocs_user_cap', 'manage_options' ), 'woocs-settings', 'woocs_settings' );
                add_submenu_page( null, 'Info', 'Info', apply_filters( 'woocs_user_cap', 'manage_options' ), 'woocs-info', 'woocs_info_page' );
                add_submenu_page( null, 'Get countries', 'Get countries', apply_filters( 'woocs_user_cap', 'manage_options' ), 'woocs-countries', 'woocs_country_page' );

                if ( true === woocs_has_cities() ) {
                    add_submenu_page( null, 'City Overview', 'City Overview', apply_filters( 'woocs_user_cap', 'manage_options' ), 'woocs-search', 'woocs_search' );
                }
            }


            /*
             * Adds CSS on the admin side
             */
            public function woocs_add_scripts() {
                wp_enqueue_style( 'woocs-general', plugins_url( 'assets/css/general.css', __FILE__ ), [], $this->settings[ 'version' ] );

                wp_register_script( 'woocs-init', plugins_url( 'assets/js/init.js', __FILE__ ), [ 'jquery', 'woocommerce' ], $this->settings[ 'version' ] );
                wp_enqueue_script( 'woocs-init' );

                if ( is_admin() ) {
                    wp_enqueue_style( 'woocs-admin', plugins_url( 'assets/css/admin.css', __FILE__ ), [], $this->settings[ 'version' ] );
                    wp_register_script( 'woocs-admin', plugins_url( 'assets/js/upload-csv.js', __FILE__ ), [ 'jquery' ], $this->settings[ 'version' ] );
                    wp_enqueue_script( 'woocs-admin' );
                }
            }
        }

        new Woocommerce_City_Selector();

    }
