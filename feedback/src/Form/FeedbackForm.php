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
        $this->messenger()->addStatus($this->t('New feedback %label has been created.', $message_arguments));
        $this->logger('feedback')->notice('Created new feedback %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The feedback %label has been updated.', $message_arguments));
        $this->logger('feedback')->notice('Updated feedback %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.feedback.canonical', ['feedback' => $entity->id()]);

    return $result;
  }

}
