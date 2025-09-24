<?php

namespace Drupal\mc_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Sidebar block.
 *
 * @Block(
 *   id = "sidebar_block",
 *   admin_label = @Translation("Sidebar Block"),
 *   category = @Translation("MC Custom"),
 * )
 */
class SidebarBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'sidebar_block',
    ];
  }

}
