(function ($, Drupal) {

  'use strict';

  /**
   * Override drupal selectHandler function
   */
  function customSelectHandler(event, ui) {
    var valueField = $(event.target);
    if($(event.target).hasClass('MYCUSTOM-autocomplete')) {
      var valueFieldName = event.target.name+'_value';
      if($('input[name='+valueFieldName+']').length > 0) {
        valueField = $('input[name='+valueFieldName+']');
        // update the labels too
        const labels = Drupal.autocomplete.splitValues(event.target.value);
        labels.pop();
        labels.push(ui.item.label);
        event.target.value = labels.join(', ');
      }
    }
    const terms = Drupal.autocomplete.splitValues(valueField.val());
    // Remove the current input.
    terms.pop();
    // Add the selected item.
    terms.push(ui.item.value);

    valueField.val(terms.join(', '));
    // Return false to tell jQuery UI that we've filled in the value already.
    return false;
  }

  Drupal.behaviors.myCustomAutocomplete = {
    attach: function(context, settings) {
      // attach custom select handler to fields with class
      $('input.MYCUSTOM-autocomplete').autocomplete({
        select: customSelectHandler,
      });
    }
  };

})(jQuery, Drupal);
