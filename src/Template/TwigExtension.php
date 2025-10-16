<?php

namespace Drupal\mc_custom\Template;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Custom Twig functions for the MC Foundational Build.
 *
 * @package Drupal\mc_custom\Template
 */
class TwigExtension extends AbstractExtension  {
  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'mc_custom';
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new TwigFunction('mc_entities', [
        $this,
        'getEntityRefs'
      ]),
      new TwigFunction('mc_icon', [
        $this,
        'getIcon'
      ]),
      new TwigFunction('mc_link', [
        $this,
        'getLink'
      ]),
      new TwigFunction('mc_logo', [
        $this,
        'getThemeLogo'
      ]),
    ];
  }

  /**
   * Returns an array of entity referenced information.
   *
   */
  public function getEntityRefs($field, $type) {
    $entity_info = [];
    foreach ($field as $key => $value) {
      $target_id = $value['target_id'];
      if ($target_id) {
        $entity = \Drupal::entityTypeManager()->getStorage($type)->load($target_id);

        $entity_info[] = [
          'text' => ($type == 'node') ? $entity->title->value : $entity->name->value,
          'url' => $entity->toUrl()->toString(),
        ];
      }
    }
    return $entity_info;
  }

  /**
   * Returns an array of icon information for frontend integration (usually icon.twig).
   *
   * @param string $icon
   *   The icon identifier in the format 'icon-set:icon-name'.
   *   Currently only supports phosphor, the MC Foundational Build default.
   * @param string|null $background
   *   Optional background information for the icon.
   *
   * @return array
   *   Icon information including 'name' and 'background' attributes.
   */
  public function getIcon($icon, $background = NULL, $modifiers = NULL) {
    $icon_parts = explode(':', $icon);
    $icon_classes = $icon; // Default value for $icon_classes

    if ($icon_parts[0] == 'phosphor') {
      $icon_classes = 'ph ph-' . $icon_parts[1] . ' bg-transparent';
    }
    elseif($icon_parts[0] == 'phosphor_fill') {
      $icon_classes = 'ph-fill ph-' . trim($icon_parts[1], '-fill') . ' bg-transparent';
    }

    return [
      'name' => $icon_classes,
      'background' => $background ?? NULL,
      'modifiers' => $modifiers ?? NULL,
    ];
  }

  /**
   * Returns an array of link information for frontend integration (usually link.twig).
   *
   * @return array
   *   Link information including 'url' and 'text' attributes.
   */
  public function getLink($url, $text, $targetString = NULL, $modifier = NULL) {
    if ($url && $text) {
      return [
        'url' => $url,
        'text' => $text,
      ];
    }
  }

  /**
   * Returns an array of logo information for frontend integration (usually logo.twig).
   *
   * @return array
   *   Logo information including 'src' and 'alt' attributes.
   *   Defaults to current theme if parameters are not provided.
   */
  public function getThemeLogo($logo_src = NULL, $logo_alt = NULL, $url = NULL) {
    $homepage = $url ?? \Drupal::config('system.site')->get('page.front');
    $logo_src = $logo_src ?? theme_get_setting('logo.url');
    $logo_alt = $logo_alt ?? t(':site_name Logo', [
      ':site_name' => \Drupal::config('system.site')->get('name'),
    ]);

    if ($logo_src) {
      return [
        'src' => $logo_src,
        'alt' => $logo_alt,
        'homepage' => $homepage,
      ];
    }
  }

}
