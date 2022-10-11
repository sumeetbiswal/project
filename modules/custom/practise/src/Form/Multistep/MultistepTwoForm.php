<?php
/**
 * @file
 * Contains \Drupal\practise\Form\Multistep\MultistepTwoForm.
 */

namespace Drupal\practise\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MultistepTwoForm extends MultistepFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_two';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	  drupal_set_message($this->store->get('pass'));
	if($this->store->get('pass') != '22')
	{
		//echo "hi";die;practise.multistep_one
		$response = new RedirectResponse('/portal/practise/stepone');
		$response->send();
		exit;
	}
    $form = parent::buildForm($form, $form_state);

    $form['age'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Your age'),
      '#default_value' => $this->store->get('age') ? $this->store->get('age') : '',
    );

    $form['location'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Your location'),
      '#default_value' => $this->store->get('location') ? $this->store->get('location') : '',
    );

    $form['actions']['previous'] = array(
      '#type' => 'link',
      '#title' => $this->t('Previous'),
      '#attributes' => array(
        'class' => array('button'),
      ),
      '#weight' => 0,
      '#url' => Url::fromRoute('practise.multistep_one'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('age', $form_state->getValue('age'));
    $this->store->set('location', $form_state->getValue('location'));

    // Save the data
    parent::saveData();
    $form_state->setRedirect('some_route');
  }
}