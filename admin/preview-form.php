<?php
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
?>
<h2><?php esc_html_e( 'Preview data', 'woocommerce-city-selector' ); ?></h2>
<p><?php esc_html_e( 'Here you can preview any uploaded csv files.', 'woocommerce-city-selector' ); ?></p>
<p><?php esc_html_e( 'Please keep in mind that all csv files are verified before displaying (and therefor can be deleted, when errors are encountered).', 'woocommerce-city-selector' ); ?></p>

<div class="acfcs__section acfcs__section--preview">
    <form name="select-preview-file" id="settings-form" action="" method="post">
        <div class="acfcs__process-file">
            <div class="acfcs__process-file-element">
                <?php echo sprintf( '<label for="woocs_file_name">%s</label>', esc_attr__( 'File', 'woocommerce-city-selector' ) ); ?>
                <select name="woocs_file_name" id="woocs_file_name">
                    <?php if ( count( $file_index ) > 1 ) { ?>
                        <option value=""><?php esc_html_e( 'Select a file', 'woocommerce-city-selector' ); ?></option>
                    <?php } ?>
                    <?php foreach ( $file_index as $file ) { ?>
                        <?php $selected = ( $file_name == $file ) ? ' selected="selected"' : false; ?>
                        <option value="<?php echo $file; ?>"<?php echo $selected; ?>><?php echo $file; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="acfcs__process-file-element">
                <?php $delimiters = [ ';', ',', '|' ]; ?>
                <?php echo sprintf( '<label for="woocs_delimiter">%s</label>', esc_attr__( 'Delimiter', 'woocommerce-city-selector' ) ); ?>
                <select name="woocs_delimiter" id="woocs_delimiter">
                    <?php foreach( $delimiters as $delimiter_value ) { ?>
                        <?php $selected_delimiter = ( $delimiter_value == $delimiter ) ? ' selected' : false; ?>
                        <option value="<?php echo $delimiter_value; ?>"<?php echo $selected_delimiter; ?>><?php echo $delimiter_value; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="acfcs__process-file-element">
                <?php echo sprintf( '<label for="woocs_max_lines">%s</label>', esc_attr__( 'Max lines', 'woocommerce-city-selector' ) ); ?>
                <input type="number" name="woocs_max_lines" id="woocs_max_lines" value="<?php echo $max_lines_value; ?>" />
            </div>
        </div>

        <div>
            <input type="submit" class="button button-primary" value="<?php esc_html_e( 'View this file', 'woocommerce-city-selector' ); ?>"/>
        </div>
    </form>
</div>
