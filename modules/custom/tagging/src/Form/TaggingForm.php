<?php

namespace Drupal\tagging\Form;

use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * TaggingForm.
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
    $tagging = \Drupal::service('tagging.service');
    $workorder = \Drupal::service('workorder.service');
    $employee = \Drupal::service('employee.service');

    $mode = $libobj->getActionMode();

    $form_title = 'Add Tagging details';

    if ($mode == 'edit') {
      $pk = $libobj->getIdFromUrl();
      $pk = $encrypt->decode($pk);
      $form_title = 'Edit Tagging details';
      $data = $tagging->getTaggingDetailsById($pk);

      // Echo "<pre/>";print_r($data);die;
    }

    $form['#attached']['library'][] = 'singleportal/master-validation';
    $form['#attached']['library'][] = 'singleportal/autocomplete';
    $form['#attributes']['class'] = 'form-horizontal';
    $form['#attributes']['autocomplete'] = 'off';

    $form['tagging']['empid'] = [
      '#type'          => 'textfield',
      '#title'         => t('Employee Id:'),
      '#attributes'    => ['class' => ['validate[required,custom[onlyLetterSp]]']],
      '#disabled'      => 'disabled',
      '#prefix'        => '<div class="row">',
      '#default_value' => $data->empid,
    ];

    $form['tagging']['userpk'] = [
      '#type'          => 'hidden',
      '#value' => $data->userpk,
    ];

    $form['tagging']['empname'] = [
      '#type'          => 'textfield',
      '#title'         => t('Name:'),
      '#attributes'    => ['class' => ['validate[required,custom[onlyLetterSp]]']],
      '#disabled'      => 'disabled',
      '#suffix'        => '</div>',
      '#default_value' => $data->firstname . ' ' . $data->lastname,
    ];

    $project_list = $workorder->getWorkorderList();
    $won_list[''] = 'Select Project';
    foreach ($project_list as $item) {
      $won_list[$item->codename] = $item->codevalues;
    }

    $form['tagging']['won'] = [
      '#type'          => 'select',
      '#title'         => t('Project :'),
      '#options'       => $won_list,
      '#attributes'    => ['class' => ['validate[required]']],
      '#prefix'        => '<div class="row">',
      '#default_value' => isset($data) ? $data->won : '',
      '#ajax' => [
        'callback' => '::getTeamList',
        'wrapper' => 'teamlist',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => t(''),
        ],
      ],
    ];

    if (!empty($form_state->getValue('won'))) {
      $wonNo = $form_state->getValue('won');
    }
    else {
      $wonNo = isset($data) ? $data->won : '';
    }

    $team_list = [];
    $team_list = $workorder->getTeamListByWorkOrder($wonNo);

    $form['tagging']['ton'] = [
      '#type'          => 'select',
      '#title'         => t('Team :'),
      '#options'       => $team_list,
      '#attributes'    => ['class' => ['validate[required]']],
      '#prefix'        => '<div id="teamlist">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data) ? $data->ton : '',
    ];

    $supervisor_default_value = '';
    if (isset($data->supervisor)) {
      $supervisor_default_value = $employee->getEmployeeAutoCompleteValueById($data->supervisor);
    }

    $form['tagging']['supervisor'] = [
      '#type'          => 'textfield',
      '#title'         => t('Supervisor :'),
      '#autocomplete_route_name' => 'employee.autocomplete',
      // '#autocomplete_route_parameters' => ['pwon' => 'hello'],
      '#attributes'    => ['class' => ['validate[required]', 'MYCUSTOM-autocomplete']],
      '#prefix'        => '<div class="row">',
      '#default_value' => $supervisor_default_value,
    ];
    $form['tagging']['supervisor_value'] = [
      '#type' => 'hidden',
      '#default_value' => $data->supervisor,
    ];

    $hr_default_value = '';
    if (isset($data->hr)) {
      $hr_default_value = $employee->getEmployeeAutoCompleteValueById($data->hr);
    }
    $form['tagging']['hr'] = [
      '#type'          => 'textfield',
      '#title'         => t('HR Manager :'),
      '#autocomplete_route_name' => 'employee.autocomplete',
      '#attributes'    => ['class' => ['validate[required]', 'MYCUSTOM-autocomplete']],
      '#suffix'        => '</div>',
      '#default_value' => $hr_default_value,
    ];
    $form['tagging']['hr_value'] = [
      '#type' => 'hidden',
      '#default_value' => $data->hr,
    ];

    $form['tagging']['role'] = [
      '#type'          => 'textfield',
      '#title'         => t('Role:'),
      '#attributes'    => ['class' => ['validate[required,custom[onlyLetterSp]]']],
      '#prefix'        => '<div class="row">',
      '#default_value' => $data->role,
    ];

    // Blank field which is not required. in order to make proper align of previous field
    // I have added one hidden field.
    $form['tagging']['role_hidden'] = [
      '#type' => 'hidden',
      '#suffix'        => '</div>',
    ];

    $form['tagging']['submit'] = [
      '#type'          => 'submit',
      '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
      '#button_type'   => 'primary',
      '#attributes'    => ['class' => ['btn btn-info']],
      '#prefix'        => '<br/><div class="row"><div class="col-md-6"></div><div class="col-md-6">',
      '#suffix'        => '&nbsp; &nbsp; &nbsp;',
    ];

    $form['tagging']['cancel'] = [
      '#type' => 'link',
      '#title' => t('Cancel'),
      '#attributes' => ['class' => ['btn btn-default']],
      '#prefix'    => '',
      '#suffix'    => '</div></div>',
      '#url' => Url::fromRoute('untag.list'),
    ];

    $form_state->setCached(FALSE);

    return $form;

  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $tagging = \Drupal::service('tagging.service');

    $field = $form_state->getValues();

    $data = [
      'userpk'    => $field['userpk'],
      'won'       => $field['won'],
      'ton'       => $field['ton'],
      'role'      => $field['role'],
      'supervisor' => $field['supervisor_value'],
      'hr'        => $field['hr_value'],
      'status'    => 1,
    ];
    // Echo "<pre/>";print_r($data);die;
    $tagging->updateTagging($data);

    \Drupal::messenger()->addMessage($field['empname'] . " is successfully Tagged.");

    $form_state->setRedirect('untag.list');
  }

  /**
   *
   */
  public function getTeamList(array $form, FormStateInterface $form_state) {
    return $form['tagging']['ton'];
  }

  /**
   *
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $field = $form_state->getValues();

    if (trim($field['userpk']) == $field['supervisor_value']) {
      $form_state->setErrorByName('supervisor', $this->t('You can not tag same person as supervisor'));
    }
    if (trim($field['userpk']) == $field['hr_value']) {
      $form_state->setErrorByName('hr', $this->t('You can not tag same person as HR'));
    }

  }

}
