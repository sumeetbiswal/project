<?php
namespace Drupal\login\Controller;
use Drupal\Core\Form\FormStateInterface;
use  Drupal\Core\Url;

class LoginController {

	public static function Authentication(&$form, FormStateInterface $form_state)
	{

        $form_state->setRedirect('dashboard.dash');

		//echo "<pre/>";print_r($form_state->getValues());die;
	}

	public static function Validate(array &$form, FormStateInterface $form_state)
	{
		$errors = $form_state->getErrors();
		if (!empty($errors['name'])) {
		  $string_error = $errors['name']->__tostring();
		  if (strpos($string_error, 'Unrecognized username or password') !== FALSE) {
			$name_value = $form_state->getValue('name');
			$form_state->clearErrors();
			$form_state->setErrorByName('name', 'Invalid Username & Password.');
        if (isset($form['more-links']['forgot_password_link'])) {
          unset ($form['more-links']['forgot_password_link']);
        }
		  }
		}
	}
}

