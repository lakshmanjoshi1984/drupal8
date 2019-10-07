<?php

namespace Drupal\conditional_fields;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides dynamic permissions of the auto_entitylabel module.
 */
class ConditionalFieldsPermissionController implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs a new ConditionalFieldsPermissionController instance.
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
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.manager'));
  }

  /**
   * Returns an array of conditional_fields permissions.
   *
   * @return array
   *   Array with permissions.
   */
  public function conditionalFieldsPermissions() {
    $permissions = [];

    foreach ($this->entityManager->getDefinitions() as $entity_type_id => $entity_type) {
      // Create a permission for each entity type to manage the entity
      // labels.
      if ($entity_type->hasLinkTemplate('conditional-fields') && $entity_type->hasKey('label')) {
        $permissions['view ' . $entity_type_id . ' conditional fields'] = [
          'title' => $this->t('%entity_label: View conditional fields', ['%entity_label' => $entity_type->getLabel()]),
          'restrict access' => TRUE,
        ];
      }
    }
    return $permissions;
  }

}
