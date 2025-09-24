<?php
namespace Drupal\mc_alerts\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {
  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Change canonical route for sitewide_alert entity type to use view instead of edit.
    if ($route = $collection->get('entity.sitewide_alert.canonical')) {
      $route->setDefaults([
        '_entity_view' => 'sitewide_alert.page',
        '_title_callback' => '\Drupal\mc_alerts\Controller\AlertDetailController::getTitle',
      ]);
      $route->setRequirements([
        '_entity_access' => 'sitewide_alert.view',
      ]);
      $route->setPath('/sitewide_alert/{sitewide_alert}');
      $route->setOption('_admin_route', FALSE);
    }
  }
}
