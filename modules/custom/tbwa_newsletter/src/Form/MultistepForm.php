<?php

namespace Drupal\tbwa_newsletter\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Query\Insert;
use Drupal\Core\User;
use Drupal\Core\Database\Connection;
use Drupal\Core\Url;
/**
 * Provides a form with two steps.
 *
 * This example demonstrates a multistep form with text input elements. We
 * extend FormBase which is the simplest form base class used in Drupal.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class MultistepForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tbwa_multistep_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    if ($form_state->has('page_num') && $form_state->get('page_num') == 2) {
      return self::fapiExamplePageTwo($form, $form_state);
    }
    if ($form_state->has('page_num') && $form_state->get('page_num') == 3) {
      return self::fapiExamplePageThree($form, $form_state);
    }
    $form_state->set('page_num', 1);

    $form['first_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#description' => $this->t('Enter your first name.'),
      '#default_value' => $form_state->getValue('first_name', ''),
      '#required' => TRUE,
    ];

    $form['last_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#default_value' => $form_state->getValue('last_name', ''),
      '#description' => $this->t('Enter your last name.'),
    ];
    $form['street_address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Street address'),
      '#default_value' => $form_state->getValue('street_address', ''),
      '#description' => $this->t('Enter your Street Address.'),
    ];
    $getCountry = \Drupal::service('country');
    $countries = $getCountry->getCountryList();
    $form['country'] = [
      '#type' => 'select',
      '#options' => $countries,
      '#title' => $this->t('Country'),
      '#default_value' => $form_state->getValue('country', ''),
      '#description' => $this->t('Enter your Country'),
    ];

    // Group submit handlers in an actions element with a key of "actions" so
    // that it gets styled correctly, and so that other modules may add actions
    // to the form. This is not required, but is convention.
    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['next'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Next'),
      // Custom submission handler for page 1.
      '#submit' => ['::fapiExampleMultistepFormNextSubmit'],
      // Custom validation handler for page 1.
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $step = $form_state->get('step');
    $step_two = $form_state->get('step_two');
    $age = $form_state->getValue('age');
    $description = $form_state->getValue('description_person');
    $uuid_service = \Drupal::service('uuid');
    $uuid = $uuid_service->generate();
    $db = \Drupal\Core\Database\Database::getConnection();
    $db->insert('tbwa_newsletter')->fields(
      array(
        'uuid' => $uuid,
        'first_name' => $step['first_name'],
        'last_name' => $step['last_name'],
        'street_address' => $step['street_address'],
        'country' => $step['country'],
        'department' => $step_two['region'],
        'state_civil' => $step_two['state_civil'],
        'phone_number' => $step_two['phone_number'],
        'age' => $age,
        'description__value' => $description['value'],
        'description__format' => $description['format'],
        'terms_conditions' => $form_state->getValue('terms_conditions'),
      )
    )->execute();
    drupal_set_message($this->t('Save Complete'));
    $url = Url::fromRoute('entity.tbwa_newsletter.collection');
    $form_state->setRedirectUrl($url);
  }

  /**
   * Provides custom validation handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function fapiExampleMultistepFormNextValidate(array &$form, FormStateInterface $form_state) {
    $age = $form_state->getValue('age');

    if ($age != '' && ($age < 18 || $age > 23)) {
      // Set an error for the form element with a key of "birth_year".
      $form_state->setErrorByName('age', $this->t('Enter a age between 18 and 23.'));
    }
  }

  /**
   * Provides custom submission handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function fapiExampleMultistepFormNextSubmit(array &$form, FormStateInterface $form_state) {
    $form_state
      ->set('step', [
        // Keep only first step values to minimize stored data.
        'first_name' => $form_state->getValue('first_name'),
        'last_name' => $form_state->getValue('last_name'),
        'street_address' => $form_state->getValue('street_address'),
        'country' => $form_state->getValue('country'),
      ])
      ->set('page_num', 2)
      ->setRebuild(TRUE);
  }
  public function fapiExampleMultistepFormNextSubmitTwo(array &$form, FormStateInterface $form_state) {
    $form_state
      ->set('step_two', [
        'region' => $form_state->getValue('region'),
        'phone_number' => $form_state->getValue('phone_number'),
        'state_civil' => $form_state->getValue('state_civil'),
      ])
      ->set('page_num', 3)
      ->setRebuild(TRUE);
  }
  /**
   * Builds the second step form (page 2).
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function fapiExamplePageTwo(array &$form, FormStateInterface $form_state) {

    $getDepartment = \Drupal::service('country');
    $departments = $getDepartment->getDepartmentList($form_state->get('step')['country']);
    $form['region'] = [
      '#type' => 'select',
      '#options' => $departments,
      '#title' => $this->t('Region'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('region', ''),
    ];
    $form['phone_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone number'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('phone_number', ''),
    ];
    $form['state_civil'] = [
      '#type' => 'radios',
      '#options' => array("single", "couple"),
      '#title' => $this->t('State Civil'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('state_civil', ''),
    ];
    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::fapiExamplePageTwoBack'],
      // We won't bother validating the required 'color' field, since they
      // have to come back to this page to submit anyway.
      '#limit_validation_errors' => [],
    ];
    $form['actions']['next'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Next'),
      // Custom submission handler for page 1.
      '#submit' => ['::fapiExampleMultistepFormNextSubmitTwo'],

    ];

    return $form;
  }

  public function fapiExamplePageThree(array &$form, FormStateInterface $form_state) {

    $form['age'] = [
      '#type' => 'number',
      '#title' => $this->t('Age'),
      '#default_value' => $form_state->getValue('age', ''),
      '#description' => $this->t('Enter your Age'),
      '#maxlength' => "10000",
      '#attributes' => [
        "min" => 18,
        "max" => 23
      ],
      '#default_value' => 18
    ];
    $form['description_person'] = array(
      '#type' => 'text_format',
      '#title' => $this->t('Description to person'),
      '#format' => 'full_html',
      '#default_value' => $form_state->getValue('description_person')['value'],
    );
    $form['terms_conditions'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Accept Terms and Conditions'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('terms_conditions', ''),
    ];
    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::fapiExamplePageThreeBack'],
      // We won't bother validating the required 'color' field, since they
      // have to come back to this page to submit anyway.
      '#limit_validation_errors' => [],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Submit'),
      '#validate' => ['::fapiExampleMultistepFormNextValidate'],
    ];

    return $form;
  }

  /**
   * Provides custom submission handler for 'Back' button (page 2).
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function fapiExamplePageTwoBack(array &$form, FormStateInterface $form_state) {
    $form_state
      // Restore values for the first step.
      ->setValues($form_state->get('step'))
      ->set('page_num', 1)
      ->setRebuild(TRUE);
  }
  public function fapiExamplePageThreeBack(array &$form, FormStateInterface $form_state) {
    $form_state
      // Restore values for the first step.
      ->setValues($form_state->get('step'))
      ->setValues($form_state->get('step_two'))
      ->set('page_num', 2)
      ->setRebuild(TRUE);
  }
}
