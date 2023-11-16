<?php

namespace Drupal\login\Controller;

use Drupal\Core\Form\FormStateInterface;

/**
 * Controller for the Login module.
 */
class LoginController {

  /**
   * Function to call after User get successfully authenticated.
   */
  public static function authentication(&$form, FormStateInterface $form_state) {
    $form_state->setRedirect('dashboard.dash');
  }

  /**
   * Overriding the validate method.
   */
  public static function validate(array &$form, FormStateInterface $form_state) {
    $errors = $form_state->getErrors();
    if (!empty($errors['name'])) {
      $string_error = $errors['name']->__tostring();
      if (strpos($string_error, 'Unrecognized username or password') !== FALSE) {
        $form_state->clearErrors();
        $form_state->setErrorByName('name', 'Invalid Username & Password.');
        if (isset($form['more-links']['forgot_password_link'])) {
          unset($form['more-links']['forgot_password_link']);
        }
      }
    }
  }

}
