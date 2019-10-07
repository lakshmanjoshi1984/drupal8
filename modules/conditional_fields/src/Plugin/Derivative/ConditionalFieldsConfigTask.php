<?php

namespace Drupal\conditional_fields\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class for Config Task.
 */
class ConditionalFieldsConfigTask extends DeriverBase implements ContainerDeriverInterface {
  use StringTranslationTrait;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Creates an FieldUiLocalTask object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [];

    foreach ($this->entityManager->getDefinitions() as $entity_type_id => $entity_type) {
      if ($entity_type instanceof ContentEntityType) {
        $base_route = $entity_type->get("field_ui_base_route");
      }
      else {
        // Special handling of Taxonomy. See https://www.drupal.org/node/2822546
        $base_route = $entity_type_id == "taxonomy_vocabulary" ? "entity.taxonomy_vocabulary.overview_form" : "entity.{$entity_type_id}.edit_form";
      }
      if ($entity_type->hasLinkTemplate('conditional-fields')) {
        $this->derivatives["$entity_type_id.conditional_fields_tab"] = [
          'route_name' => "entity.{$entity_type_id}.conditionals",
          'title' => $this->t('Manage dependencies'),
          'base_route' => $base_route,
          'weight' => 10,
        ];
      }
    }

    foreach ($this->derivatives as &$entry) {
      $entry += $base_plugin_definition;
    }
    return $this->derivatives;
  }

}
