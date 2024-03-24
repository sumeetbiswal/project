<?php
/**
 * @file
 * Contains \Drupal\leave\Form\LeaveTypeForm.
 */

namespace Drupal\leave\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
/**
 * LeaveTypeForm
 */
class LeaveTypeForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
	return 'leavetype_form';

  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $libobj = \Drupal::service('library.service');
    $encrypt = \Drupal::service('encrypt.service');
    $leave = \Drupal::service('leave.service');

    $mode = $libobj->getActionMode();
    $form_state->setCached(FALSE);
    $form_title = 'Add Leave Type';
    if($mode == 'edit'){
      $pk = $libobj->getIdFromUrl();
      $pk = $encrypt->decode($pk);

      $data = $leave->getLeaveTypeDetailsById($pk);

      $form_title = 'Edit - ' . $data->codevalues;
      $libobj->setPageTitle($form_title);
    }



    $form['#attached']['library'][] = 'singleportal/master-validation';
    $form['#attributes']['class'] = 'form-horizontal';
    $form['#attributes']['autocomplete'] = 'off';


    $form['leavetype']['name'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Leave Type:'),
      '#attributes'    => ['class' => ['validate[required,custom[onlyLetterSp]]']],
      '#prefix'        => '<div class="row">',
      '#suffix'        => '</div>',
      '#default_value' => isset($data)? $data->codevalues : '',

    );


    $form['leavetype']['code'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Leave Code:'),
      '#attributes'    => ['class' => ['validate[required,custom[onlyLetterSp]]']],
      '#prefix'        => '<div class="row">',
      '#suffix'        => '</div>',
      '#default_value' => isset($data)? $data->codename : '',

    );

    $form['leavetype']['allotment'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Leave Allot:'),
      '#attributes'    => ['class' => ['validate[required,custom[onlyLetterSp]]']],
      '#prefix'        => '<div class="row">',
      //'#suffix'        => '</div>',
      '#default_value' => isset($data)? $data->weight : ''

    );

    $form['leavetype']['allotmentper'] = array(
      '#type'          => 'select',
      '#title'         => t('Per:'),
      '#options'       => ['M' => 'Month', 'Y' => 'Year'],
      '#attributes'    => ['class' => ['validate[required,custom[onlyLetterSp]]']],
     // '#prefix'        => '<div class="row">',
      '#suffix'        => '</div>',
      '#default_value' => isset($data)? $data->email : $form_state->getValue('allotmentper')
    );


    $carryforward = [];
    if(!empty($form_state->getValue('carryforward'))){
      $carryforward = $form_state->getValue('carryforward');
    }

    $form['leavetype']['carryforward'] = array(
      '#type'          => 'checkboxes',
      '#title'         => t('Carry Forward:'),
      '#options'       => ['m' => 'Next Month', 'y' => 'Next Year'],
      '#attributes'    => ['class' => ['validate[required,custom[onlyLetterSp]]']],
       '#prefix'        => '<div class="row">',
      '#suffix'        => '</div>',
      '#default_value' => isset($data)? explode(',', $data->location) : $carryforward

    );

    $form['leavetype']['status'] = array(
      '#type'          => 'select',
      '#title'         => t('Status:'),
      '#options'       => ['1' => 'Active', '0' => 'Inactive'],
      '#attributes'    => ['class' => ['validate[required,custom[onlyLetterSp]]']],
      '#prefix'        => '<div class="row">',
      '#suffix'        => '</div>',
      '#default_value' => isset($data)? $data->status : $form_state->getValue('status'),
    );

    $form['leavetype']['#type'] = 'actions';
    $form['leavetype']['submit'] = array(
      '#type'          => 'submit',
      '#default_value' => ($mode == 'add') ? $this->t('Create') : $this->t('Update'),
      '#button_type'   => 'primary',
      '#attributes'    => ['class' => ['btn btn-info']],
      '#prefix'        => '<div class="row m-t-40"><div class="col-md-2 text-right">',
      '#suffix'        => '</div><div class="col-md-6">',
    );

    $form['leavetype']['cancel'] = array(
      '#type' => 'link',
      '#title' => t('Cancel'),
      '#attributes' => ['class' => ['btn btn-default']],
      '#prefix' => '',
      '#suffix' => '</div></div>',
      '#url' => \Drupal\Core\Url::fromRoute('leavetype.list'),
    );

    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
	  $field = $form_state->getValues();
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $libobj = \Drupal::service('library.service');
    $leave = \Drupal::service('leave.service');
    $encrypt = \Drupal::service('encrypt.service');

    $values = $form_state->getValues();

    $leavetype_name = $values['name'];
    $leavetype_code = $values['code'];
    $allotment = $values['allotment'];
    $allotmentper = $values['allotmentper'];

    $carryforward_arr = $values['carryforward'];
    $carryforward = implode(',', $carryforward_arr);
    $carryforward = rtrim($carryforward, ',0');

    $status = $values['status'];

    $data  = array(
      'codevalues' =>  $leavetype_name,
      'codename' => $leavetype_code,
      'codetype' => 'leavetype',
      'weight' =>  $allotment,
      'email' =>  $allotmentper,
      'location' =>  $carryforward,
      'status' =>  $status,
    );

    $mode = $libobj->getActionMode();

    if($mode == 'add')
    {
      $leave->setLeaveType($data);
      \Drupal::messenger()->addMessage($data['codevalues'] . " has been created.");
    }
    if($mode == 'edit')
    {
      $pk = $libobj->getIdFromUrl();
      $pk = $encrypt->decode($pk);
      $leave->updateLeaveType($data, $pk);
      \Drupal::messenger()->addMessage($data['codevalues'] . " has succesfully Updated.");
    }
    $form_state->setRedirect('leavetype.list');
  }
}
?>
