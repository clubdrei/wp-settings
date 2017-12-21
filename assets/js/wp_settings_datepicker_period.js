/**
 * @author Christoph Bessei
 */
jQuery(document).ready(function () {
  var commonOptions = {
    // Show the 'close' and 'today' buttons
    showButtonPanel: true
  };

  if (typeof objectL10n !== 'undefined') {
    jQuery.extend(commonOptions, objectL10n);
  }
  var startOptions = {
    onClose: function (selectedDate) {
      jQuery('.wp_settings_datepicker_period_end').datepicker('option', 'minDate', selectedDate);
    }
  };

  var endOptions = {
    onClose: function (selectedDate) {
      jQuery('.wp_settings_datepicker_period_start').datepicker('option', 'maxDate', selectedDate);
    }
  };

  startOptions = jQuery.extend({}, commonOptions, startOptions);
  endOptions = jQuery.extend({}, commonOptions, endOptions);

  jQuery('.wp_settings_datepicker_period_start').datepicker(startOptions);

  jQuery('.wp_settings_datepicker_period_end').datepicker(endOptions);
});
