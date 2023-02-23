<?php

namespace Drupal\feedback\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SimpleFeedbackForm extends FormBase {

  /**
   * @var MailManagerInterface
   */
  protected MailManagerInterface $mailManager;

  public function __construct(MailManagerInterface $mail_manager) {
    $this->mailManager = $mail_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.mail')
    );
  }

  public function getFormId() {
    return 'form_examples.simple_feedback_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your Name'),
      '#required' => TRUE,
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Your Email Address'),
      '#required' => TRUE,
    ];
    $form['address'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Address of the Issue'),
      '#required' => TRUE,
    ];
    $form['issue_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type of Issue'),
      '#options' => [
        'Repair Needed' => $this->t('Repair Needed'),
        'Poor Service' => $this->t('Poor Service Received'),
        'Other' => $this->t('Other')
      ],
      '#required' => TRUE,
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description of the Issue'),
      '#rows' => 10,
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $address = $form_state->getValue('address');
    if (!str_contains($address, 'Springfield')) {
      $form_state->setError(
        $form['address'],
        $this->t("We're sorry. We can only service requests from Springfield.")
      );
    }
    parent::validateForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->mailManager->mail(
      'feedback',
      'city_feedback_form',
      'cityfeedback@example.com',
      \Drupal::languageManager()->getDefaultLanguage()->getId(),
      $form_state->getValues(),
      $form_state->getValue('email')
    );

    $this->messenger()->addStatus(
      $this->t('Thank you for submitting your feedback. You should receive a response in 5 - 10 business days.')
    );
  }
}
