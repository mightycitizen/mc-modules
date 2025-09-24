<?php

declare(strict_types=1);

namespace Drupal\mc_icons\Plugin\GraphQLCompose\FieldType;

use Drupal\graphql_compose\Plugin\GraphQLCompose\GraphQLComposeFieldTypeBase;
use Drupal\graphql_compose\Plugin\GraphQL\DataProducer\FieldProducerTrait;

/**
 * {@inheritDoc}
 *
 * @GraphQLComposeFieldType(
 *   id = "ui_icon",
 *   type_sdl = "String",
 * )
 */
class UiIconType extends GraphQLComposeFieldTypeBase {

  use FieldProducerTrait;

  /**
   * Value to return to getProducerProperty in FieldProducerTrait.
   *
   * This could be value, entity, something_id, whatever.
   * It's equivalent to $entity->field_abc->value
   *
   * @var string
   */
  public $producerProperty = 'target_id';
}
