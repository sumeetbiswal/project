/**
 * @file
 * Provides responsive behaviors to HTML details elements.
 */

 /*eslint-disable */

 (function (Drupal, $, once) {

  /**
   * Initializes the responsive behaviors for details elements.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the responsive behavior to status report specific details elements.
   */
  Drupal.behaviors.responsiveDetails = {
    attach(context) {
      const $details = $(once('responsiveDetails', 'responsive-details', context)).find('details');

      if (!$details.length) {
        return;
      }

      function detailsToggle(matches) {
        if (matches) {
          $details.attr('open', true);
          $summaries.attr('aria-expanded', true);
          $summaries.on('click.details-open', false);
        }
        else {
          // If user explicitly opened one, leave it alone.
          const $notPressed = $details
            .find('> summary[aria-pressed!=true]')
            .attr('aria-expanded', false);
          $notPressed
            .parent('details')
            .attr('open', false);
          // After resize, allow user to close previously opened details.
          $summaries.off('.details-open');
        }
      }

      function handleDetailsMQ(event) {
        detailsToggle(event.matches);
      }

      const $summaries = $details.find('> summary');
      const mql = window.matchMedia('(min-width:769px)');
      mql.addListener(handleDetailsMQ);
      detailsToggle(mql.matches);
    }
  };
}(Drupal, jQuery, once));
