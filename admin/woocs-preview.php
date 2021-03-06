<?php
    /**
     * Content for the settings page
     */
    function woocs_preview_page() {

        if ( ! current_user_can( apply_filters( 'woocs_user_cap', 'manage_options' ) ) ) {
            wp_die( esc_html__( 'Sorry, you do not have sufficient permissions to access this page.', 'woocommerce-city-selector' ) );
        }

        Woocommerce_City_Selector::woocs_show_admin_notices();
        ?>

        <div class="wrap woocs">
            <h1>Woocommerce City Selector</h1>

            <?php echo Woocommerce_City_Selector::woocs_admin_menu(); ?>

            <?php
                $file_index      = woocs_check_if_files();
                $file_name       = ( isset( $_POST[ 'woocs_file_name' ] ) ) ? $_POST[ 'woocs_file_name' ] : false;
                $max_lines       = ( isset( $_POST[ 'woocs_max_lines' ] ) ) ? (int) $_POST[ 'woocs_max_lines' ] : false;
                $max_lines_value = ( false != $max_lines ) ? $max_lines : 100;
                $delimiter       = ( isset( $_POST[ 'woocs_delimiter' ] ) ) ? sanitize_text_field( $_POST[ 'woocs_delimiter' ] ) : apply_filters( 'woocs_delimiter', ';' );

                // Get imported data
                if ( $file_name ) {
                    $csv_info   = woocs_csv_to_array( $file_name, '', $delimiter, true, $max_lines );
                    $file_index = woocs_check_if_files();
                }
            ?>

            <div class="woocs__container">
                <div class="admin_left">
                    <div class="content">
                        <?php if ( ! empty( $file_index ) ) { ?>
                            <?php include 'preview-form.php'; ?>

                        <?php } else { ?>
                            <div>
                                <?php esc_html_e( 'You have no files to preview.', 'woocommerce-city-selector' ); ?>
                                <?php echo sprintf( __( 'Upload a csv file from your %s.', 'woocommerce-city-selector' ), sprintf( '<a href="%s">%s</a>', esc_url( admin_url( '/admin.php?page=woocs-dashboard' ) ), __( 'dashboard', 'woocommerce-city-selector' ) ) ); ?>
                            </div>
                        <?php } ?>

                        <?php
                            if ( $file_name ) {
                                echo '<div class="woocs__section woocs__section--results">';
                                if ( array_key_exists( 'error', $csv_info ) ) {
                                    if ( 'file_deleted' == $csv_info[ 'error' ] ) {
                                        $dismiss_button = sprintf( '<button type="button" class="notice-dismiss"><span class="screen-reader-text">%s</span></button>', esc_html__( 'Dismiss this notice', 'woocommerce-city-selector' ) );
                                        $error_message  = sprintf( esc_html__( 'You either have errors in your CSV or there is no data. In case of an error, the file is deleted. Please check "%s".', 'woocommerce-city-selector' ), $file_name );
                                        echo sprintf( '<div class="notice notice-error is-dismissable"><p>%s</p>%s</div>', $error_message, $dismiss_button );

                                    } elseif ( ! isset( $csv_info[ 'data' ] ) || ( isset( $csv_info[ 'data' ] ) && empty( $csv_info[ 'data' ] ) ) ) {
                                        $message = esc_html__( 'There appears to be no data in the file. Are you sure it has content and you selected the correct delimiter ?', 'woocommerce-city-selector' );
                                        echo sprintf( '<div class="notice notice-error">%s</div>', $message );

                                    }
                                } elseif ( isset( $csv_info[ 'data' ] ) && ! empty( $csv_info[ 'data' ] ) ) {
                                    echo sprintf( '<h2>%s</h2>', esc_html__( 'CSV contents', 'woocommerce-city-selector' ) );
                                    echo sprintf( '<p class="hide640"><small>%s</small></p>', esc_html__( 'Table scrolls horizontally.', 'woocommerce-city-selector' ) );
                                    echo woocs_render_preview_results();
                                }
                                echo '</div>';
                            }
                        ?>
                    </div>
                </div>

                <?php include 'admin-right.php'; ?>

            </div>
        </div>
        <?php
    }
