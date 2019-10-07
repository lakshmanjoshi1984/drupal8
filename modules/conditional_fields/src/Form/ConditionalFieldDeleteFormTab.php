<?php

namespace Drupal\conditional_fields\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class ConditionalFieldDeleteFormTab.
 *
 * @package Drupal\conditional_fields\Form
 */
class ConditionalFieldDeleteFormTab extends ConditionalFieldDeleteForm {

  protected $bundle;

  protected $entity_type;

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    $bundle = $this->bundle;
    $entity_type = $this->entity_type;
    if ($bundle_type = $this->entityTypeManager->getDefinition($entity_type)->getBundleEntityType()) {
      $entity_type = $bundle_type;
    }

    return Url::fromRoute("entity.$entity_type.conditionals", [$entity_type => $bundle]);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'conditional_field_delete_form_tab';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type = NULL, $bundle = NULL, $field_name = NULL, $uuid = NULL) {
    $this->entity_type = $entity_type;
    $this->bundle = $bundle;
    return parent::buildForm($form, $form_state, $entity_type, $bundle, $field_name, $uuid);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
