<?php

namespace Drupal\mc_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\config_pages\Entity\ConfigPages;

/**
 * Provides a Footer block.
 *
 * @Block(
 *   id = "footer_block",
 *   admin_label = @Translation("Footer Block"),
 *   category = @Translation("MC Custom"),
 * )
 */
class FooterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $global_settings = ConfigPages::config('global_settings');

    if ($global_settings) {
      $social_links = $this->getSocialLinks($global_settings);
      $cta_links = $this->getCtaLinks($global_settings);
      $contact_info = $this->getContactInfo($global_settings);
    }

    return [
      '#theme' => 'footer_block',
      '#social' => $social_links ?? NULL,
      '#ctas' => $cta_links ?? NULL,
      '#contact' => $contact_info ?? NULL,
    ];
  }

  private function getSocialLinks($global_settings) {
    $social_links = [];
    $social_fields = [
      'field_social_facebook',
      'field_social_x',
      'field_social_instagram',
      'field_social_linkedin',
      'field_social_youtube',
    ];

    foreach ($social_fields as $field) {
      if ($global_settings->hasField($field)) {
        $url = $global_settings->get($field);
        if (!$url->isEmpty()) {
          $handle = str_replace('field_social_', '', $field);
          $social_links[$handle] = [
            'handle' => $handle,
            'icon' => $handle === 'x' ? 'x-logo' : $handle,
            'url' => $url->first()->getUrl()->toString(),
          ];
        }
      }
    }

    return $social_links;
  }

  private function getCtaLinks($global_settings) {
    $cta_links = [];
    $cta_fields = [
      'field_cta_primary',
      'field_cta_secondary',
    ];

    foreach ($cta_fields as $field) {
      if ($global_settings->hasField($field)) {
        $url = $global_settings->get($field);
        if (!$url->isEmpty()) {
          $cta_links[] = [
            'text' => $url->title,
            'url' => $url->first()->getUrl()->toString(),
            'modifier' => 'button ' . str_replace('field_cta_', '', $field),
          ];
        }
      }
    }

    return $cta_links;
  }

  private function getContactInfo($global_settings) {
    $contact_info = [];
    $contact_fields = [
      'field_contact_phone',
      'field_contact_address',
      'field_contact_email',
    ];

    foreach ($contact_fields as $field) {
      if ($global_settings->hasField($field)) {
        $contact = $global_settings->get($field);

        if (!$contact->isEmpty()) {
          $type = str_replace('field_contact_', '', $field);

          switch ($type) {
            case 'phone':
              $icon = 'icon-phone-call';
              $label = t('Phone');
              $text = $contact->first()->value;
              break;
            case 'address':
              // Get Address field, reorder and filter out empty values.
              $address_field = $contact->getValue();
              if (empty($address_field[0]['address_line1'])) {
                continue 2; // Prevents accidental empty values (e.g. if country is set as default but all other values are empty)
              }
              $address_top = array_filter([
                $address_field[0]['address_line1'],
                $address_field[0]['address_line2'],
                $address_field[0]['address_line3'],
              ]);
              $address_bottom = array_filter([
                $address_field[0]['locality'],
                $address_field[0]['administrative_area'],
                $address_field[0]['postal_code'],
              ]);
              // Get user-friendly address string.
              $address_string = implode(', ', array_merge($address_top, $address_bottom));
              $text = implode('<br>', $address_top) . '<br>' . implode(', ', $address_bottom);
              $icon = 'icon-map-pin-line';
              $label = t('Get Directions');
              $url = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address_string);
              break;
            case 'email':
              $icon = 'icon-at';
              $label = t('Email');
              $text = $contact->first()->value;
              $url = 'mailto:' . $text;
              break;
            default:
              $icon = NULL;
              $label = NULL;
              $text = NULL;
              $url = NULL;
          }

          $contact_info[] = [
            'type' => isset($type) ? $type : NULL,
            'icon' => isset($icon) ? $icon : NULL,
            'label' => isset($label) ? $label : NULL,
            'text' => isset($text) ? $text : NULL,
            'url' => isset($url) ? $url : NULL,
          ];
        }
      }
    }

    return $contact_info;
  }
}
