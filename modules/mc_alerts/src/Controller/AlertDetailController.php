<?php

namespace Drupal\mc_alerts\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\sitewide_alert\Entity\SitewideAlertInterface;

/**
 * Controller for the public view of a sitewide alert.
 */
class AlertDetailController extends ControllerBase {
  /**
   * Page title callback for a Sitewide Alert.
   *
   * @param \Drupal\sitewide_alert\Entity\SitewideAlertInterface $sitewide_alert
   * The Sitewide Alert entity.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   * The page title.
   */
  public function getTitle(SitewideAlertInterface $sitewide_alert): TranslatableMarkup {
    return new TranslatableMarkup('@title', ['@title' => $sitewide_alert->label()]);
  }

}