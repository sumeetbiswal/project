<?php
/**
 * @file
 * Contains \Drupal\practise\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\practise\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;

class MultistepOneForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_one';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	parent::deleteStore();
    $form = parent::buildForm($form, $form_state);

    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Your name'),
      '#default_value' => $this->store->get('name') ? $this->store->get('name') : '',
    );

    $form['email'] = array(
      '#type' => 'email',
      '#title' => $this->t('Your email address'),
      '#default_value' => $this->store->get('email') ? $this->store->get('email') : '',
    );

    $form['actions']['submit']['#value'] = $this->t('Next');
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('email', $form_state->getValue('email'));
    $this->store->set('name', $form_state->getValue('name'));
	 $this->store->set('pass', '22');
    $form_state->setRedirect('practise.multistep_two');
  }
}