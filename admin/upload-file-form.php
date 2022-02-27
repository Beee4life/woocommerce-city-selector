<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
?>
<form enctype="multipart/form-data" method="post">
    <input name="woocs_upload_csv_nonce" type="hidden" value="<?php echo wp_create_nonce( 'woocs-upload-csv-nonce' ); ?>" />
    <input type="hidden" name="MAX_FILE_SIZE" value="1024000" />

    <div class="upload-element">
        <?php echo sprintf( '<label for="woocs_csv_upload">%s</label>', esc_attr__( 'Choose a (CSV) file to upload', 'woocommerce-city-selector' ) ); ?>
        <div class="form--upload form--woocs_csv_upload">
            <input type="file" name="woocs_csv_upload" id="woocs_csv_upload" accept=".csv" />
            <span class="val"></span>
            <span class="woocs_upload_button button-primary" data-type="woocs_csv_upload">
                <?php _e( 'Select file', 'woocommerce-city-selector' ); ?>
            </span>
        </div>
    </div>
    <input type="submit" class="button button-primary" value="<?php esc_html_e( 'Upload CSV', 'woocommerce-city-selector' ); ?>" />
</form>
