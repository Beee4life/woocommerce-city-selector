<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
?>
<form method="post">
    <input name="woocs_import_raw_nonce" type="hidden" value="<?php echo wp_create_nonce( 'woocs-import-raw-nonce' ); ?>" />
    <?php echo sprintf( '<label for="raw-import">%s</label>', esc_attr__( 'Raw CSV import', 'woocommerce-city-selector' ) ); ?>
    <?php echo sprintf( '<textarea name="woocs_raw_csv_import" id="raw-import" rows="5" placeholder="%s">%s</textarea>', $placeholder, $submitted_raw_data ); ?>
    <br />
    <?php
        echo sprintf( '<input name="woocs_verify" type="submit" class="button button-primary" value="%s" />', esc_attr__( 'Verify CSV data', 'woocommerce-city-selector' ) );
        echo sprintf( '<input name="woocs_import" type="submit" class="button button-primary" value="%s" />', esc_attr__( 'Import CSV data', 'woocommerce-city-selector' ) );
    ?>
</form>
