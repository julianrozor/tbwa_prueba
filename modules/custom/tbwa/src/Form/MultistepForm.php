<?php

namespace Drupal\tbwa\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

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
      return self::tbwaNewsletterPageTwo($form, $form_state);
    }
    if ($form_state->has('page_num') && $form_state->get('page_num') == 3) {
      return self::tbwaNewsletterPageThree($form, $form_state);
    }

    $form_state->set('page_num', 1);
    $form['step'] = [
      '#type' => 'hidden',
      '#value' => $form_state->get('page_num'),
    ];
    $form['description'] = [
      '#type' => 'item',
      '#title' => $this->t('A basic multistep form (page 1)'),
    ];

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
    $form['street_adress'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Street address'),
      '#default_value' => $form_state->getValue('street_adress', ''),
      '#description' => $this->t('Enter your Street Address'),
    ];
    $getCountry = \Drupal::service('country');
    $countries = $getCountry->getCountryList();
    $form['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country'),
      '#options' => $countries,
      '#default_value' => $form_state->getValue('country', 'co'),
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
      '#submit' => ['::tbwaNewsletterMultistepFormNextSubmit'],
      // Custom validation handler for page 1.
      '#validate' => ['::tbwaNewsletterMultistepFormNextValidate'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $page_values = $form_state->get('page_values');

    drupal_set_message($this->t('The form has been submitted. name="@first @last", year of birth=@street_adress', [
      '@first' => $page_values['first_name'],
      '@last' => $page_values['last_name'],
      '@year_of_birth' => $page_values['country'],
    ]));

    drupal_set_message($this->t('And the favorite color is @color', ['@color' => $form_state->getValue('color')]));
  }

  /**
   * Provides custom validation handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function tbwaNewsletterMultistepFormNextValidate(array &$form, FormStateInterface $form_state) {
    $birth_year = $form_state->getValue('birth_year');

    // if ($birth_year != '' && ($birth_year < 1900 || $birth_year > 2000)) {
    //   // Set an error for the form element with a key of "birth_year".
    //   $form_state->setErrorByName('birth_year', $this->t('Enter a year between 1900 and 2000.'));
    // }
  }

  /**
   * Provides custom submission handler for page 1.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function tbwaNewsletterMultistepFormNextSubmit(array &$form, FormStateInterface $form_state) {
    $form_state
      ->set('page_values', [
        // Keep only first step values to minimize stored data.
        'first_name' => $form_state->getValue('first_name'),
        'last_name' => $form_state->getValue('last_name'),
        'country' => $form_state->getValue('country'),
        'birth_year' => $form_state->getValue('birth_year'),
      ])
      ->set('page_num', $form_state->get("page_num")+1)
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
  public function tbwaNewsletterPageTwo(array &$form, FormStateInterface $form_state) {

    $form['description'] = [
      '#type' => 'item',
      '#title' => $this->t('A basic multistep form (page 2)'),
    ];
    $form['step'] = [
      '#type' => 'hidden',
      '#value' => $form_state->get('page_num'),
    ];

    $getDepartment = \Drupal::service('country');
    $departments = $getDepartment->getDepartmentList($form_state->getValue('country'));
    $form['department'] = [
      '#type' => 'select',
      '#options' => $departments,
      '#title' => $this->t('Department'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('department', ''),
    ];
    $form['pic_user'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Department'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('pic_user', ''),
    ];
    $form['telephone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('telephone', ''),
    ];
    $form['state_civil'] = [
      '#type' => 'radios',
      '#options' => array("single" => "single", "couple" => "couple"),
      '#title' => $this->t('State Civil'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('state_civil', ''),
    ];
    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::tbwaNewsletterPageBack'],
      // We won't bother validating the required 'color' field, since they
      // have to come back to this page to submit anyway.
      '#limit_validation_errors' => [],
    ];
    $form['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Next'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::tbwaNewsletterMultistepFormNextSubmit'],
      // We won't bother validating the required 'color' field, since they
      // have to come back to this page to submit anyway.
      '#limit_validation_errors' => [],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }
  public function tbwaNewsletterPageThree(array &$form, FormStateInterface $form_state) {

    $form['description'] = [
      '#type' => 'item',
      '#title' => $this->t('A basic multistep form (page 3)'),
    ];
    $form['step'] = [
      '#type' => 'hidden',
      '#value' => $form_state->get('page_num'),
    ];
    $form['department'] = [
      '#type' => 'select',
      '#options' => array(1 => 1, 2 =>2),
      '#title' => $this->t('Department'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('department', ''),
    ];
    $form['pic_user'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Department'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('pic_user', ''),
    ];
    $form['telephone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('telephone', ''),
    ];
    $form['state_civil'] = [
      '#type' => 'radios',
      '#options' => array("single" => "single", "couple" => "couple"),
      '#title' => $this->t('State Civil'),
      '#required' => TRUE,
      '#default_value' => $form_state->getValue('state_civil', ''),
    ];
    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::tbwaNewsletterPageBack'],
      // We won't bother validating the required 'color' field, since they
      // have to come back to this page to submit anyway.
      '#limit_validation_errors' => [],
    ];
    $form['next'] = [
      '#type' => 'submit',
      '#value' => $this->t('Next'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::tbwaNewsletterMultistepFormNextSubmit'],
      // We won't bother validating the required 'color' field, since they
      // have to come back to this page to submit anyway.
      '#limit_validation_errors' => [],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Submit'),
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
  public function tbwaNewsletterPageBack(array &$form, FormStateInterface $form_state) {
    $form_state
      // Restore values for the first step.
      ->setValues($form_state->get('page_values'))
      ->set('page_num', $form_state->get("page_num")-1)
      ->setRebuild(TRUE);
  }

}
