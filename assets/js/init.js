(function ($) {
  $(document).ready(function () {

    $("#billing_country").change(function() {
      $('#billing_state_field').removeClass('woocommerce-invalid');
    });
  });

})(jQuery);
