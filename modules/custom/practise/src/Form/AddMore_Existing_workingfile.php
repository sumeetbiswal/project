<?php
/**
 * @file
 * Contains \Drupal\practise\Form\Multistep\AddMore
 */

namespace Drupal\practise\Form;
  use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
class AddMore extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'add_more_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $name_field = $form_state->get('num_names');
    
    $form['#tree'] = TRUE;
        
    $form['names_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Peoples'),
      '#prefix' => "<div id='names-fieldset-wrapper'>",
      '#suffix' => '</div>',
    ];

    if (empty($name_field)) {
      $name_field = $form_state->set('num_names', 1);
      
    }

    for ($i = 0; $i < $form_state->get('num_names'); $i++) {
      $form['names_fieldset'][$i]['first_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('First name'),
        '#maxlength' => 64,
        '#size' => 64,
        '#prefix' => "<div class='inner-fieldset'><legend><span class='fieldset-legend'>People {$i}</span></legend>"
      ];
      $form['names_fieldset'][$i]['last_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Last name'),
        '#maxlength' => 64,
        '#size' => 64,
      ];
    }
      $form['names_fieldset']['actions'] = [
        '#type' => 'actions',
      ];
      $form['names_fieldset']['actions']['add_name'] = [
        '#type' => 'submit',
        '#value' => t('Add one more'),
        '#submit' => array('::addOne'),
        '#ajax' => [
          'callback' => '::addmoreCallback',
          'wrapper' => "names-fieldset-wrapper",
        ],
      ];
      if ($form_state->get('num_names') > 1) {
        $form['names_fieldset']['actions']['remove_name'] = [
          '#type' => 'submit',
          '#value' => t('Remove one'),
          '#submit' => array('::removeCallback'),
          '#ajax' => [
            'callback' => '::addmoreCallback',
            'wrapper' => "names-fieldset-wrapper",
          ],
        ];
      }
    $form_state->setCached(FALSE);

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
    ];

    return $form;
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_names');
    return $form['names_fieldset'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addOne(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_names');
    $add_button = $name_field + 1;
    $form_state->set('num_names', $add_button);
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove one" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeCallback(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_names');
    if ($name_field > 1) {
      $remove_button = $name_field - 1;
      $form_state->set('num_names', $remove_button);
    }
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    dsm($form_state->getValue(array('names_fieldset')));
    dsm(count($form_state->getValue(array('names_fieldset'))));
    foreach ($form_state->getValue(array('names_fieldset')) as $key => $value) {
      //drupal_set_message($key . ': ' . $value);
      if(is_numeric($key))
       dsm($form_state->getValue(array('names_fieldset', $key, 'first_name')));
      
    
    }
  }

}