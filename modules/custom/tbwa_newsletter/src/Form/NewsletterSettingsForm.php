<?php

namespace Drupal\tbwa_newsletter\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ContentEntityExampleSettingsForm.
 *
 * @package Drupal\tbwa_newsletter\Form
 *
 * @ingroup tbwa_newsletter
 */
class NewsletterSettingsForm extends FormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'tbwa_newsletter_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Empty implementation of the abstract submit class.
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    return $this->redirect('entity.tbwa_newsletter.collection');
    //$form_state->setRedirect('entity.tbwa_newsletter.collection');
    //$form['contact_settings']['#markup'] = 'Settings form for ContentEntityExample. Manage field settings here.';
    //return $form;
  }

}
