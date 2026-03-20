<?php

declare(strict_types=1);

namespace Drupal\mc_custom\Hook;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Drupal\taxonomy\Entity\Term;

class ViewsHooks {

  /**
   * Constructs a new ViewsHooks object.
   *
   * @param \Drupal\Core\Database\Connection $database
   * The database connection.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cacheDefault
   * The default cache backend.
   */
  public function __construct(
    protected Connection $database,
    #[Autowire(service: 'cache.default')]
    protected CacheBackendInterface $cache,
  ) {}

  /**
   * Implements hook_preprocess_views_view().
   * @param array $variables
   *
   * @return void
   */
  #[Hook('preprocess_views_view')]
  public function preprocessViewsView(array &$variables): void {
    $view = $variables['view'];


    // Look for a views argument with the format columns:<number>
    if ($view->id() == 'news' && !empty($view->args)) {
      foreach ($view->args as $arg) {
        if (preg_match('/^columns:(\\d+)$/', $arg, $matches)) {
          $columns = $matches[1];
          break;
        }
      }
    }
    // Otherwise set columns based on view mode
    if (!isset($variables['columns']) && !isset($columns)) {
      if ($row_plugin = $view->display_handler->getPlugin('row')) {
        if (array_key_exists('view_mode', $row_plugin->options)) {
          if ($row_plugin->options['view_mode'] == 'card') {
            $columns = 3;
          }
          elseif ($row_plugin->options['view_mode'] == 'card_featured') {
            $columns = 2;
          }
          else {
            $columns = 1;
          }
        }
      }
      if ($view->current_display == 'block_card') {
        $columns = 3;
      }
    }
    $variables['columns'] = $columns ?? 1;



    // Get selected values from exposed filters.
    // Used as "Selected filters" in secondary exposed form.
    $selected = array();

    foreach ($view->exposed_data as $key => $value) {
      if (is_array($value)) {
        $value = array_filter($value, function($value) {
          return $value != 0;
        });
          $selected[$key] = $value;

      }
    }

    // Uses the Better Exposed Filters "secondary" option to split the exposed form into two forms.
    // The primary "exposed" form will contain the typical views exposed filters.
    // Anything added to the "secondary" option will be moved to a separate form.
    // This allows the "exposed" and "secondary" forms to be split into two sections in the template.
    if (array_key_exists('secondary', $variables['exposed'])) {
      // Clone exposed form for secondary options.
      $variables['exposed']['#split_form'] = 'primary'; // used for theming
      $exposed = $variables['exposed'];
      $secondary = $exposed;

      // ==========
      // PRIMARY EXPOSED FORM
      // ==========
      // Hide secondary options from primary form.
      // (elements should still exist in primary form for proper filtering)
      // unset($variables['exposed']['secondary']);
      $variables['exposed']['secondary']['#attributes']['class'][] = 'hidden';

      // Remove "Sort by" from primary form.
      unset($variables['exposed']['sort_by']);

      // Hide submit button in primary form.
      $variables['exposed']['actions']['submit']['#attributes']['class'][] = 'hidden';
      $variables['exposed']['#attributes']['class'][] = 'view-filter';

      // ==========
      // SECONDARY EXPOSED FORM
      // ==========
      // Remove reset button from secondary form, move actions to secondary fieldset.
      unset($secondary['actions']['reset']);
      $secondary['secondary']['actions'] = $secondary['actions'];
      unset($secondary['actions']);
      unset($secondary['filter_view_mode']);

      // Add selected filters to secondary form.
      $selected_options = array();
      foreach ($selected as $key => $value) {
        if (!empty($value)) {
          $selected_options[] = array_intersect_key($secondary[$key], $value);
        }
        unset($secondary[$key]);
      }
      if ($selected_options) {
        $secondary['selected'] = array(
          '#id' => 'selected-filters',
          '#name' => 'selected-filters',
          '#type' => 'checkboxes',
          '#title' => t('Selected filters'),
          '#title_display' => 'before',
          '#theme_wrappers' => ['bef_checkboxes'],
          '#bef_display_inline' => TRUE,
          '#attributes' => ['inline' => TRUE],
        );
        $secondary['selected'] = array_merge($secondary['selected'], $selected_options);
      }

      // Unique IDs for accessibility.
      // $secondary['#id'] = $exposed['#id'] . '--secondary';
      // $secondary['actions']['#id'] = $exposed['actions']['#id'] . '--secondary';
      // $secondary['actions']['submit']['#id'] = $exposed['actions']['submit']['#id'] . '--secondary';

      // Update from details to fieldset.
      $secondary['#split_form'] = 'secondary'; // used for theming
      $secondary['#attributes']['class'][] = 'secondary-exposed-form';
      $secondary['#type'] = 'fieldset';
      $secondary['secondary']['#type'] = 'fieldset';
      $secondary['secondary']['#theme_wrappers'] = ['fieldset'];
      $secondary['secondary']['#attributes']['inline'] = TRUE;
    }

    // Make the `secondary` variable available to the template.
    $variables['secondary'] = $secondary ?? NULL;

    // Use exposed_raw_input as the authoritative keyword source.
    // $variables['exposed']['keys'] is unreliable here because BEF nests the
    // 'keys' field inside its 'secondary' group, so the top-level key is absent
    // after unset($variables['exposed']['secondary']) above. Reading it always
    // returned '' and then immediately overwrote exposed_raw_input with that
    // empty value, actively clearing any submitted keyword.
    $keyword = $view->exposed_raw_input['keys'] ?? '';

    // Sync the keyword value back into the secondary form's visible input so it
    // remains populated after an AJAX-driven checkbox submission.
    $variables['secondary']['keys']['#value'] = $keyword;
    $variables['view']->exposed_data['keys'] = $keyword;

  } /* END: preprocessViewsView */

  /**
   * Implements hook_preprocess_views_exposed_form().
   *
   * @param array $variables
   *
   * @return void
   */
  #[Hook('preprocess_views_exposed_form')]
  public function preprocessViewsExposedForm(array &$variables): void {
  }

  /**
   * Implements hook_preprocess_fieldset().
   *
   * @param array $variables
   *   The variables for the template.
   *
   * @return void
   */
  #[Hook('preprocess_fieldset')]
  public function preprocessFieldset(array &$variables): void {
    // Directory filters should default to closed
    if (isset($variables['element']['#context']['#view_id'])) {
      if ($variables['element']['#context']['#view_id'] == 'directory' && $variables['element']['#context']['#display_id'] == 'block') {
        $variables['closed'] = TRUE;
      }
    }

  }
  /**
   * Implements hook_form_views_exposed_form_alter().
   */
  #[Hook('form_views_exposed_form_alter')]
  public function alterExposedForm(&$form, FormStateInterface $form_state, $form_id): void {
    $view = $form_state->get('view');
    $exposed = $view->getExposedInput();
    $keys = $exposed['keys'] ?? '';
    $form['keys']['#value'] = $keys;
    // Also set the value when BEF has nested 'keys' inside its secondary fieldset.
    if (isset($form['secondary']['keys'])) {
      $form['secondary']['keys']['#value'] = $keys;
      $form['secondary']['keys']['#default_value'] = $keys;
    }

    // If the Views form API has AJAX configured on the submit button, change
    // the progress type to 'none'. This prevents orphaned fullscreen throbbers
    // that occur when the split form renders two elements sharing the same
    // data-drupal-selector inside the same js-view-dom-id-* AJAX wrapper.
    if (isset($form['actions']['submit']['#ajax'])) {
      $form['actions']['submit']['#ajax']['progress'] = ['type' => 'none'];
    }

    // Attach the cleanup library as a belt-and-suspenders fallback for the
    // common case where BEF triggers Drupal.ajax directly (bypassing the form
    // API #ajax property above) and fullscreen progress indicators are
    // orphaned when the AJAX-replaced DOM loses the success handler reference.
    $form['#attached']['library'][] = 'mc_custom/views_bef_cleanup';
  }

  /**
   * Implements hook_views_query_alter().
   * Converting year filter to match only year portion of ISO datetime.
   */
  #[Hook('views_query_alter')]
  public function alterViewsQuery($view, $query): void {

  }

  /**
   * Implements hook_views_data_alter().
   */
  #[Hook('views_data_alter')]
  public function alterViewsData(array &$data): void {
    // Target the specific field table and column.
    if (isset($data['node__field_publication_date']['field_publication_date_value'])) {
      $data['node__field_publication_date']['field_publication_date_value']['filter']['id'] = 'publication_year_filter';
    }
  }

}
