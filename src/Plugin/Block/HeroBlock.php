<?php

namespace Drupal\mc_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a Hero block.
 *
 * @Block(
 *   id = "hero_block",
 *   admin_label = @Translation("Hero Block"),
 *   category = @Translation("MC Custom"),
 * )
 */
class HeroBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'hero_block',
    ];
  }

}
