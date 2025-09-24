<?php

namespace Drupal\mc_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Header block.
 *
 * @Block(
 *   id = "header_block",
 *   admin_label = @Translation("Header Block"),
 *   category = @Translation("MC Custom"),
 * )
 */
class HeaderBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'header_block',
    ];
  }

}
