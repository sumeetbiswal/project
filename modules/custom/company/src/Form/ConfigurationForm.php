<?php

namespace Drupal\company\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
class ConfigurationForm extends FormBase {

  /**
   *
   */
  public function getFormId() {
    return 'configuration_form';

  }

  /**
   *
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;
    $user = \Drupal::currentUser();
    $configobj = \Drupal::service('configuration.service');
    $form['company']['#attributes']['enctype'] = "multipart/form-data";
    $form['#attached']['library'][] = 'singleportal/bootstrap-toggle';
    $form['company']['#suffix'] = '</div>';
    $data = $configobj->getEmpIdType();
    $form['company']['empidtype'] = [
      '#type' => 'checkbox',
      '#title' => t('Auto EmpID'),
        // '#required' => TRUE,
      '#attributes' => [
        'class' => ['form-control'],
        'data-toggle' => 'toggle',
        'data-on' => 'ON',
        'data-off' => 'OFF',
        'data-onstyle' => 'info',
      ],
      '#prefix' => '<div class="row">',
      '#default_value' => !empty($data) ? ($data->codevalues == 'Automatic') ? 1 : 0 : '',
      '#disabled' => ($user->hasPermission('admin configuration')) ? FALSE : TRUE,
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="The code which is being used for employee ID generation. For EX:- If your code is ABC then Employee ID will be ABC001, ABC021, ABC0156" data-toggle="tooltip"></i>',
    ];

    $form['company']['codeformat'] = [
      '#type' => 'textfield',
      '#title' => t('Code Format:'),
      '#attributes' => ['class' => ['form-control']],
      '#states' => [
        'visible' => [
          ':input[name="empidtype"]' => ['checked' => TRUE],
        ],
      ],
      // '#prefix' => '<div class="row">',
      '#suffix' => '</div>',
      '#default_value' => !empty($data) ? $data->description : '',
      '#disabled' => ($user->hasPermission('admin configuration')) ? FALSE : TRUE,
    ];

    $brch = $configobj->getBranchCodeConfig();
    $form['company']['Branchcode'] = [
      '#type' => 'checkbox',
      '#title' => t('Branch Code'),
      // '#required' => TRUE,
      '#attributes' => [
        'class' => ['form-control'],
        'data-toggle' => 'toggle',
        'data-on' => 'ON',
        'data-off' => 'OFF',
        'data-onstyle' => 'info',
      ],
      '#prefix' => '<div class="row">',
      '#default_value' => !empty($brch) ? ($brch->codevalues == 'on') ? 1 : 0 : '',
      '#disabled' => ($user->hasPermission('admin configuration')) ? FALSE : TRUE,
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Make it ON If you need to enter your branch code" data-toggle="tooltip"></i>',
    ];

    $dept = $configobj->getDepartmentCodeConfig();
    $form['company']['Departmentcode'] = [
      '#type' => 'checkbox',
      '#title' => t('Department Code'),
      // '#required' => TRUE,
      '#attributes' => [
        'class' => ['form-control'],
        'data-toggle' => 'toggle',
        'data-on' => 'ON',
        'data-off' => 'OFF',
        'data-onstyle' => 'info',
      ],
      '#default_value' => !empty($dept) ? ($dept->codevalues == 'on') ? 1 : 0 : '',
      '#disabled' => ($user->hasPermission('admin configuration')) ? FALSE : TRUE,
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Make it ON If you need to enter your Department code" data-toggle="tooltip"></i>',
      '#suffix' => '</div>',
    ];

    $desg = $configobj->getDesignationCodeConfig();

    $form['company']['Designationcode'] = [
      '#type' => 'checkbox',
      '#title' => t('Designation Code'),
      // '#required' => TRUE,
      '#attributes' => [
        'class' => ['form-control'],
        'data-toggle' => 'toggle',
        'data-on' => 'ON',
        'data-off' => 'OFF',
        'data-onstyle' => 'info',
      ],
      '#prefix' => '<div class="row">',
      '#default_value' => !empty($desg) ? ($desg->codevalues == 'on') ? 1 : 0 : '',
      '#disabled' => ($user->hasPermission('admin configuration')) ? FALSE : TRUE,
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Make it ON If you need to enter your Designation code" data-toggle="tooltip"></i>',
    ];

    $wrkord_conf = $configobj->getWorkorderCodeConfig();

    $form['company']['Workordercode'] = [
      '#type' => 'checkbox',
      '#title' => t('Workorder code'),
      // '#required' => TRUE,
      '#attributes' => [
        'class' => ['form-control'],
        'data-toggle' => 'toggle',
        'data-on' => 'ON',
        'data-off' => 'OFF',
        'data-onstyle' => 'info',
      ],
      '#default_value' => !empty($wrkord_conf) ? ($wrkord_conf->codevalues == 'on') ? 1 : 0 : '',
      '#disabled' => ($user->hasPermission('admin configuration')) ? FALSE : TRUE,
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Make it ON If you need to enter your Designation code" data-toggle="tooltip"></i>',
      '#suffix' => '</div>',
    ];

    $form['company']['#type'] = 'actions';
    $form['company']['submit'] = [
      '#type' => 'submit',
      '#default_value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#attributes' => ['class' => ['btn btn-info']],
      '#prefix' => '<br/><div class="row"><div class="col-md-2"></div><div class="col-md-4">',
      '#suffix' => '',
      '#disabled' => ($user->hasPermission('admin configuration')) ? FALSE : TRUE,
    ];
    return $form;

  }

  /**
   *
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // $configobj = new \Drupal\company\Model\ConfigurationModel;
    $configobj = \Drupal::service('configuration.service');

    $field = $form_state->getValues();
    $employeeIdType = ($field['empidtype']) ? 'Automatic' : 'Manual';
    $branchCodeType = ($field['Branchcode']) ? 'on' : 'off';
    $designationCodeType = ($field['Designationcode']) ? 'on' : 'off';
    $departmentCodeType = ($field['Departmentcode']) ? 'on' : 'off';
    $workorderCodeType = ($field['Workordercode']) ? 'on' : 'off';

    $field = [
      [
        'codetype'      => 'employeeid',
        'codename'         => 'EMPID',
        'codevalues'    => $employeeIdType,
      ],
      [
        'codetype'      => 'branchcode',
        'codename'         => 'BRNCD',
        'codevalues'    => $branchCodeType,
      ],
               [
                 'codetype'      => 'designationcode',
                 'codename'         => 'DSGCD',
                 'codevalues'    => $designationCodeType,
               ],
      [
        'codetype'      => 'departmentcode',
        'codename'         => 'DPTCD',
        'codevalues'    => $departmentCodeType,
      ],
              [
                'codetype'      => 'workordercode',
                'codename'         => 'WRKCD',
                'codevalues'    => $workorderCodeType,
              ],
    ];

    $configobj->updatAllConfig($field);
    \Drupal::messenger()->addMessage("All Configuration has been updated.");

  }

}
