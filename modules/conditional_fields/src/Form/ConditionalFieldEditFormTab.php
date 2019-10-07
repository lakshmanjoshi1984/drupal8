<?php

namespace Drupal\conditional_fields\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConditionalFieldEditFormTab.
 *
 * @package Drupal\conditional_fields\Form
 */
class ConditionalFieldEditFormTab extends ConditionalFieldEditForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'conditional_field_edit_form_tab';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $values = $form_state->cleanValues()->getValues();

    $entity_type = $values['entity_type'];
    if ($bundle_type = $this->entityTypeManager->getDefinition($entity_type)->getBundleEntityType()) {
      $entity_type = $bundle_type;
    }

    $form_state->setRedirect("entity.$entity_type.conditionals", [$entity_type => $values['bundle']]);
  }

}
