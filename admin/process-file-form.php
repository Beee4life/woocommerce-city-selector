<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
?>
<form method="post">
    <input name="woocs_select_file_nonce" type="hidden" value="<?php echo wp_create_nonce( 'woocs-select-file-nonce' ); ?>" />

    <div class="woocs__process-file">
        <div class="woocs__process-file-element woocs__process-file-element--file">
            <?php echo sprintf( '<label for="woocs_file_name">%s</label>', esc_attr__( 'File', 'woocommerce-city-selector' ) ); ?>
            <select name="woocs_file_name" id="woocs_file_name">
                <?php if ( count( $file_index ) > 1 ) { ?>
                    <?php echo sprintf( '<option value="">%s</option>', esc_attr__( 'Select a file', 'woocommerce-city-selector' ) ); ?>
                <?php } ?>
                <?php foreach ( $file_index as $file_name ) { ?>
                    <?php $selected = ( isset( $_POST[ 'woocs_file_name' ] ) && $_POST[ 'woocs_file_name' ] == $file_name ) ? ' selected="selected"' : false; ?>
                    <?php echo sprintf( '<option value="%s"%s>%s</option>', $file_name, $selected, $file_name ); ?>
                <?php } ?>
            </select>
        </div>

        <div class="woocs__process-file-element woocs__process-file-element--delimiter">
            <?php $delimiters = [ ';', ',', '|' ]; ?>
            <?php echo sprintf( '<label for="woocs_delimiter">%s</label>', esc_attr__( 'Delimiter', 'woocommerce-city-selector' ) ); ?>
            <select name="woocs_delimiter" id="woocs_delimiter">
                <?php foreach( $delimiters as $delimiter ) { ?>
                    <?php $selected_delimiter = ( $delimiter == apply_filters( 'woocs_delimiter', ';' ) ) ? ' selected' : false; ?>
                    <?php echo sprintf( '<option value="%s"%s>%s</option>', $delimiter, $selected_delimiter, $delimiter ); ?>
                <?php } ?>
            </select>
        </div>

        <div class="woocs__process-file-element woocs__process-file-element--maxlines">
            <?php echo sprintf( '<label for="woocs_max_lines">%s</label>', esc_attr__( 'Max lines', 'woocommerce-city-selector' ) ); ?>
            <input type="number" name="woocs_max_lines" id="woocs_max_lines" />
        </div>
    </div>

    <?php
        echo sprintf( '<input name="woocs_verify" type="submit" class="button button-primary" value="%s" />', esc_attr__( 'Verify selected file', 'woocommerce-city-selector' ) );
        echo sprintf( '<input name="woocs_import" type="submit" class="button button-primary" value="%s" />', esc_attr__( 'Import selected file', 'woocommerce-city-selector' ) );
        echo sprintf( '<input name="woocs_remove" type="submit" class="button button-primary" value="%s" />', esc_attr__( 'Remove selected file', 'woocommerce-city-selector' ) );
    ?>
</form>
