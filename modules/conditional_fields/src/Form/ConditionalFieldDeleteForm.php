<?php

namespace Drupal\conditional_fields\Form;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\EntityFormBuilder;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ConditionalFieldDeleteForm.
 *
 * @package Drupal\conditional_fields\Form
 */
class ConditionalFieldDeleteForm extends ConfirmFormBase {

  protected $entityType;
  private $bundle;
  private $fieldName;
  private $uuid;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;


  /**
   * ConditionalFieldDeleteForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   */
  public function __construct(EntityTypeManager $entityTypeManager)
  {
    $this->entityTypeManager = $entityTypeManager;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the %field_name condition?', [
      '%field_name' => $this->fieldName,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('conditional_fields.conditions_list', [
      'entity_type' => $this->entityType,
      'bundle' => $this->bundle,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'conditional_field_delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if (empty($this->entityType) || empty($this->bundle) || empty($this->fieldName) || empty($this->uuid)) {
      return;
    }
    /**
     * @var EntityFormDisplay $entity
     */
    $entity = \Drupal::entityTypeManager()
      ->getStorage('entity_form_display')
      ->load($this->entityType . '.' . $this->bundle . '.default');
    if (!$entity) {
      return;
    }
    $field = $entity->getComponent($this->fieldName);
    unset($field['third_party_settings']['conditional_fields'][$this->uuid]);
    $entity->setComponent($this->fieldName, $field);
    $entity->save();
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type = NULL, $bundle = NULL, $field_name = NULL, $uuid = NULL) {
    $this->entityType = $entity_type;
    $this->bundle = $bundle;
    $this->fieldName = $field_name;
    $this->uuid = $uuid;

    return parent::buildForm($form, $form_state);
  }

}
