/**
 * @file
 * Removes orphaned fullscreen AJAX progress indicators on split BEF forms.
 *
 * When a Views exposed form is split into two form elements that share the
 * same data-drupal-selector, both live inside the js-view-dom-id-* wrapper
 * that Views AJAX replaces on each request. If the Drupal.Ajax success
 * handler's reference to its progress element becomes stale after the DOM
 * replacement, the .ajax-progress-fullscreen div is left in <body>.
 *
 * Using jQuery's ajaxStop — which fires only when all pending AJAX requests
 * have settled — guarantees that any remaining .ajax-progress-fullscreen
 * elements at that point are truly orphaned and safe to remove.
 */
(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.mcViewsBefProgressCleanup = {
    attach(context, settings) {
      // Use once() on documentElement so the handler is registered exactly
      // once per page lifecycle and survives repeated Drupal.behaviors.attach
      // calls triggered by Views AJAX DOM replacements.
      once('mc-views-bef-cleanup', document.documentElement).forEach(() => {
        $(document).on('ajaxStop.mcViewsBefCleanup', function () {
          // A small timeout lets Drupal's own success/error handlers run first
          // before we sweep up anything they may have missed.
          setTimeout(function () {
            document
              .querySelectorAll('.ajax-progress.ajax-progress-fullscreen')
              .forEach(function (el) {
                el.remove();
              });
          }, 50);
        });
      });
    },
  };
}(jQuery, Drupal, once));
