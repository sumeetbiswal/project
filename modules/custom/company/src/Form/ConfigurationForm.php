<?php

namespace Drupal\company\Form;

use Drupal\company\Model\ConfigurationModel;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ConfigurationForm creates the Form for the Co  Configuration.
 */
class ConfigurationForm extends FormBase {
  /**
   * Include the messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Include the configuration service.
   *
   * @var \Drupal\company\Model\ConfigurationModel
   */
  protected $configuration;

  /**
   * Current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * ConfigurationForm constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\company\Model\ConfigurationModel $configuration
   *   The configuration service.
   * @param \Drupal\Core\Session\AccountInterface $currentUser
   *   Current User.
   */
  public function __construct(MessengerInterface $messenger, ConfigurationModel $configuration, AccountInterface $currentUser) {
    $this->messenger = $messenger;
    $this->configuration = $configuration;
    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('configuration.service'),
      $container->get('current_user'),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'configuration_form';

  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['company']['#attributes']['enctype'] = "multipart/form-data";
    $form['#attached']['library'][] = 'singleportal/bootstrap-toggle';
    $form['company']['#suffix'] = '</div>';
    $data = $this->configuration->getEmpIdType();
    $form['company']['empidtype'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Auto EmpID'),
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
      '#disabled' => ($this->currentUser->hasPermission('admin configuration')) ? FALSE : TRUE,
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="The code which is being used for employee ID generation. For EX:- If your code is ABC then Employee ID will be ABC001, ABC021, ABC0156" data-toggle="tooltip"></i>',
    ];

    $form['company']['codeformat'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Code Format:'),
      '#attributes' => ['class' => ['form-control']],
      '#states' => [
        'visible' => [
          ':input[name="empidtype"]' => ['checked' => TRUE],
        ],
      ],
      // '#prefix' => '<div class="row">',
      '#suffix' => '</div>',
      '#default_value' => !empty($data) ? $data->description : '',
      '#disabled' => ($this->currentUser->hasPermission('admin configuration')) ? FALSE : TRUE,
    ];

    $brch = $this->configuration->getBranchCodeConfig();
    $form['company']['Branchcode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Branch Code'),
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
      '#disabled' => ($this->currentUser->hasPermission('admin configuration')) ? FALSE : TRUE,
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Make it ON If you need to enter your branch code" data-toggle="tooltip"></i>',
    ];

    $dept = $this->configuration->getDepartmentCodeConfig();
    $form['company']['Departmentcode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Department Code'),
      // '#required' => TRUE,
      '#attributes' => [
        'class' => ['form-control'],
        'data-toggle' => 'toggle',
        'data-on' => 'ON',
        'data-off' => 'OFF',
        'data-onstyle' => 'info',
      ],
      '#default_value' => !empty($dept) ? ($dept->codevalues == 'on') ? 1 : 0 : '',
      '#disabled' => ($this->currentUser->hasPermission('admin configuration')) ? FALSE : TRUE,
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Make it ON If you need to enter your Department code" data-toggle="tooltip"></i>',
      '#suffix' => '</div>',
    ];

    $desg = $this->configuration->getDesignationCodeConfig();

    $form['company']['Designationcode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Designation Code'),
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
      '#disabled' => ($this->currentUser->hasPermission('admin configuration')) ? FALSE : TRUE,
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Make it ON If you need to enter your Designation code" data-toggle="tooltip"></i>',
    ];

    $wrkord_conf = $this->configuration->getWorkorderCodeConfig();

    $form['company']['Workordercode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Workorder code'),
      // '#required' => TRUE,
      '#attributes' => [
        'class' => ['form-control'],
        'data-toggle' => 'toggle',
        'data-on' => 'ON',
        'data-off' => 'OFF',
        'data-onstyle' => 'info',
      ],
      '#default_value' => !empty($wrkord_conf) ? ($wrkord_conf->codevalues == 'on') ? 1 : 0 : '',
      '#disabled' => ($this->currentUser->hasPermission('admin configuration')) ? FALSE : TRUE,
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
      '#disabled' => ($this->currentUser->hasPermission('admin configuration')) ? FALSE : TRUE,
    ];
    return $form;

  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

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

    $this->configuration->updatAllConfig($field);
    $this->messenger->addMessage("All Configuration has been updated.");

  }

}
