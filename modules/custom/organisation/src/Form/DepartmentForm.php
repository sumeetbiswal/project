<?php

namespace Drupal\organisation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Drupal\library\Controller\Encrypt;
use Drupal\library\Lib\LibController;
use Drupal\organisation\Model\ConfigurationModel;
use Drupal\organisation\Model\DepartmentModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * DepartmentForm creates the Form for Branch.
 */
class DepartmentForm extends FormBase {
  /**
   * Include the messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Include the custom library service.
   *
   * @var \Drupal\library\Lib\LibController
   */
  protected $library;

  /**
   * Include the department service.
   *
   * @var \Drupal\organisation\Model\DepartmentModel
   */
  protected $department;

  /**
   * Include the custom library service.
   *
   * @var \Drupal\library\Controller\Encrypt
   */
  protected $encrypt;

  /**
   * Include the custom configuration service.
   *
   * @var \Drupal\organisation\Model\ConfigurationModel
   */
  protected $configuration;

  /**
   * DesignationForms constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\library\Lib\LibController $library
   *   The library service.
   * @param \Drupal\organisation\Model\DepartmentModel $department
   *   The department service.
   * @param \Drupal\library\Controller\Encrypt $encrypt
   *   The library service.
   * @param \Drupal\organisation\Model\ConfigurationModel $configuration
   *   The configuration service.
   */
  public function __construct(MessengerInterface $messenger, LibController $library, DepartmentModel $department, Encrypt $encrypt, ConfigurationModel $configuration) {
    $this->messenger = $messenger;
    $this->library = $library;
    $this->department = $department;
    $this->encrypt = $encrypt;
    $this->configuration = $configuration;
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
      $container->get('library.service'),
      $container->get('department.service'),
      $container->get('encrypt.service'),
      $container->get('configuration.service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'department_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $mode = $this->library->getActionMode();

    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $this->encrypt->decode($pk);
      $data = $this->department->getDepartmentDetailsById($pk);

      $form_title = 'Edit Department - ' . $data->codevalues;
      $this->library->setPageTitle($form_title);
    }

    $form['#attached']['library'][] = 'singleportal/master-validation';
    $form['#attributes']['class'] = 'form-horizontal';
    $form['#attributes']['autocomplete'] = 'off';

    $form['department']['name'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Department Name:'),
      '#attributes'    => [
        'class' => ['validate[required,custom[onlyLetterSp]]'],
      ],
      '#prefix'        => '<div class="row">',
      '#suffix'        => '</div>',
      '#default_value' => isset($data) ? $data->codevalues : '',
    ];

    $dptcode_config = $this->configuration->getDepartmentcodeConfig();
    $dpt_conf = [];
    $dpt_conf['disabled'] = '';
    $dpt_conf['departmentcode'] = '';
    $dpt_conf['helpmsg'] = 'Mention Department Code of the person';
    if ($dptcode_config->codevalues == 'off') {
      $dpt_conf['disabled'] = 'disabled';
      $dpt_conf['departmentcode'] = 'XXXXXXX';
      $dpt_conf['helpmsg'] = 'Department Code will be auto generate';
    }

    $form['department']['code'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Department Code:'),
      '#attributes'    => [
        'class' => ['validate[required,custom[onlyLetterSp]]'],
      ],
      '#prefix'        => '<div class="row">',
      '#suffix'        => '</div>',
      '#default_value' => isset($data) ? $data->codename : $dpt_conf['departmentcode'],
      '#disabled'      => $dpt_conf['disabled'],
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="' . $dpt_conf['helpmsg'] . '" data-toggle="tooltip"></i>',

    ];

    $form['department']['submit'] = [
      '#type'          => 'submit',
      '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
      '#button_type'   => 'primary',
      '#attributes'    => ['class' => ['btn btn-info']],
      '#prefix'        => '<br/><div class="row"><div class="col-md-2"></div><div class="col-md-4">',
      '#suffix'        => '',
    ];

    $form['department']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#attributes'               => ['class' => ['btn btn-default']],
      '#prefix'                   => '',
      '#suffix'                   => '</div></div>',
      '#url' => Url::fromRoute('view.department.page'),
    ];
    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $deptname = trim($form_state->getValue('name'));
    $dept_exist = $this->department->deptIsExist($deptname);

    if ($dept_exist) {
      $form_state->setErrorByName('name', $this->t('Department has already Exist. Duplicate is not allowed.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $field = $form_state->getValues();
    $code_config = $this->configuration->getDepartmentCodeConfig();

    // Check codevalues OFF then auto generate the code values.
    $code = ($code_config->codevalues == 'on') ? $field['code'] : $this->library->generateCode('DPT', $field['name']);

    $name = $field['name'];

    $field = [
      'codevalues' => $name,
      'codename'   => $code,
      'codetype'   => 'department',
    ];

    $mode = $this->library->getActionMode();
    if ($mode == 'add') {
      $this->department->setDepartment($field);
      $this->messenger->addMessage("succesfully saved.");
    }
    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $this->encrypt->decode($pk);
      $this->department->updateDepartment($field, $pk);
      $this->messenger->addMessage("succesfully Updated.");
    }

    $form_state->setRedirect('view.department.page');

  }

}
