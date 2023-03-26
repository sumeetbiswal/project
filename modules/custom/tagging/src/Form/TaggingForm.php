<?php
/**
 * @file
 * Contains \Drupal\tagging\Form\TaggingForm.
 */


namespace Drupal\tagging\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

/**
 * TaggingForm
 */
class TaggingForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tagging_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $libobj = \Drupal::service('library.service');
    $encrypt = \Drupal::service('encrypt.service');

    $mode = $libobj->getActionMode();

    $form_title = 'Add Tagging details';

    if($mode == 'edit'){
      $pk = $libobj->getIdFromUrl();
      $pk = $encrypt->decode($pk);
      $form_title = 'Edit Tagging details';
      //$data = $brnobj->getTaggingDetailsById($pk);
    }

    $form['#attached']['library'][] = 'singleportal/master-validation';
    $form['#attributes']['class'] = 'form-horizontal';
    $form['#attributes']['autocomplete'] = 'off';


    $form['tagging']['workname'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Work order Name:'),
      '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
      //'#prefix'        => '<div class="row">',
      '#default_value' => 'Hello',
    );

  	$form_state->setCached(FALSE);

    return $form;

    }

  public function validateForm(array &$form, FormStateInterface $form_state) {

  }


  public function submitForm(array &$form, FormStateInterface $form_state) {


  }


}
?>
