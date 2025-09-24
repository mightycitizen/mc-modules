<?php

namespace Drupal\mc_alerts\Controller;

use Drupal\config_pages\Entity\ConfigPages;
use Drupal\Core\Block\BlockManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Alerts.
 */
class AlertsListingController extends ControllerBase {

  /**
   * The AlertsListingController class construct.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param BlockManagerInterface $blockManager
   *   The block plugin manager.
   * @param EntityDisplayRepositoryInterface $displayRepository
   *   The entity display repository.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    protected BlockManagerInterface $blockManager,
    protected EntityDisplayRepositoryInterface $displayRepository
  ) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.block'),
      $container->get('entity_display.repository')
    );
  }

  /**
   * Builds title for 404 error page.
   *
   * @return string
   */
  public function getAlertsListingTitle() {
    return $this->buildTitle();
  }

  /**
   * Builds 404 error page
   *
   * @return array
   */
  public function getAlertsListingPage() {
    return $this->buildPage();
  }

  /**
   * Builds status pages by Config Pages entity.
   *
   * @param string $id
   *   The config pages type.
   * @param int $code
   *   The status code.
   * @return array
   *   The render array.
   */
  protected function buildPage() {
    $config = ConfigPages::config('alerts');
    if ($config) {
      $values = $config->toArray();

      $config_pages = [
        'active_heading' => $config->get('field_alerts_active_heading')->value,
        'active_description' => $config->get('field_alerts_active_body')->view(['label'=>'hidden']),
        'inactive_heading' => $config->get('field_alerts_inactive_heading')->value,
        'inactive_description' => $config->get('field_alerts_inactive_body')->view(['label'=>'hidden']),
      ];

    }
    return $build = [
      '#theme' => 'sitewide_alerts',
      '#config_pages' => $config_pages ?? NULL,
    ];
  }

  /**
   * Builds title for status pages.
   *
   * @param string $id
   *   The config pages type.
   * @return string
   *   The status page title.
   */
  protected function buildTitle() {
    $config = ConfigPages::config('alerts');
    if ($config) {
      $heading = $config->get('field_heading')->value;
    }
    return $heading ?? 'Alerts';
  }

}
