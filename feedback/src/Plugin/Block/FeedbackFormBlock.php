<?php

namespace Drupal\feedback\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\feedback\Form\SimpleFeedbackForm;

/**
 * Provides a 'Feedback Form' block.
 *
 * @Block(
 *   id = "feedback_form_block",
 *   admin_label = @Translation("City Feedback Form"),
 *   category = @Translation("DrupalTutor Training")
 * )
 */
class FeedbackFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $feedback = \Drupal::entityTypeManager()->getStorage('feedback')->create();
    $form = \Drupal::service('entity.form_builder')->getForm($feedback, 'add');
    return $form;
  }
}
