// JS File for upload csv field
(function($) {
    $(document).ready(function () {

        $('.woocs_upload_button').click(function () {
            var type = $(this).data('type');
            $("#" + type).trigger('click');
        });

        $("input[name='woocs_csv_upload']").change(function () {
            var type = $(this).attr('id');
            $('.form--' + type + ' .val').text(this.value.replace(/C:\\fakepath\\/i, ''))
        });

    });
})(jQuery);
