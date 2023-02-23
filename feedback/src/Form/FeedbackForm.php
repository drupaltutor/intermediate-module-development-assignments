<?php

namespace Drupal\feedback\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the feedback entity edit forms.
 */
class FeedbackForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $form['revision']['#default_value'] = TRUE;
    if (!\Drupal::currentUser()->hasPermission('edit feedback')) {
      $form['uid']['#access'] = FALSE;
      $form['created']['#access'] = FALSE;
      $form['status']['#access'] = FALSE;
      $form['revision_log']['#access'] = FALSE;
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $address = $form_state->getValue('address');
    if (!str_contains($address[0]['value'], 'Springfield')) {
      $form_state->setError(
        $form['address'],
        $this->t("We're sorry. We can only service requests from Springfield.")
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus(
          $this->t('Thank you for submitting your feedback. You should receive a response in 5 - 10 business days.')
        );
        $this->logger('feedback')->notice('Created new feedback %label', $logger_arguments);

        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The feedback %label has been updated.', $message_arguments));
        $this->logger('feedback')->notice('Updated feedback %label.', $logger_arguments);
        break;
    }

    if (!\Drupal::currentUser()->hasPermission('view feedback')) {
      $form_state->setRedirect('<front>');
    }
    else {
      $form_state->setRedirect('entity.feedback.canonical', ['feedback' => $entity->id()]);
    }

    return $result;
  }

}
