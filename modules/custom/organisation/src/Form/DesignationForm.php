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
use Drupal\organisation\Model\DesignationModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * DesignationForm creates the Form for Branch.
 */
class DesignationForm extends FormBase {
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
   * Include the designation service.
   *
   * @var \Drupal\organisation\Model\DesignationModel
   */
  protected $designation;

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
   * @param \Drupal\organisation\Model\DesignationModel $designation
   *   The designation service.
   * @param \Drupal\library\Controller\Encrypt $encrypt
   *   The library service.
   * @param \Drupal\organisation\Model\ConfigurationModel $configuration
   *   The configuration service.
   */
  public function __construct(MessengerInterface $messenger, LibController $library, DepartmentModel $department, DesignationModel $designation, Encrypt $encrypt, ConfigurationModel $configuration) {
    $this->messenger = $messenger;
    $this->library = $library;
    $this->department = $department;
    $this->designation = $designation;
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
      $container->get('designation.service'),
      $container->get('encrypt.service'),
      $container->get('configuration.service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'designation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    global $base_url;

    $mode = $this->library->getActionMode();
    $form_title = 'Add Designation Details';
    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $this->encrypt->decode($pk);
      $data = $this->designation->getDesignationDetailsById($pk);

      $form_title = 'Edit Designation - ' . $data->codevalues;
      $this->library->setPageTitle($form_title);
    }

    $form['#attached']['library'][] = 'singleportal/master-validation';
    $form['#attributes']['class'] = 'form-horizontal';
    $form['#attributes']['autocomplete'] = 'off';

    $form['designation']['name'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Designation Name:'),
      '#attributes'    => [
        'class' => ['validate[required,custom[onlyLetterSp]]'],
      ],
      '#prefix'        => '<div class="row"><div class="col-md-12">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data) ? $data->codevalues : '',
    ];

    $descode_config = $this->configuration->getDesignationCodeConfig();
    $desconf = [];
    $desconf['disabled'] = '';
    $desconf['departmentcode'] = '';
    $desconf['helpmsg'] = 'Mention Designation Code of the person';

    if ($descode_config->codevalues == 'off') {
      $desconf['disabled'] = 'disabled';
      $desconf['designationcode'] = 'XXXXXXX';
      $desconf['helpmsg'] = 'Designation Code will be auto generate';
    }

    $form['designation']['code'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Designation Code:'),
      '#attributes'    => [
        'class' => ['validate[required,custom[onlyLetterSp]]'],
      ],
      '#prefix'        => '<div class="row"><div class="col-md-12">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data) ? $data->codename : $desconf['designationcode'],
      '#disabled'      => $desconf['disabled'],
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="' . $desconf['helpmsg'] . '" data-toggle="tooltip"></i>',
    ];

    $deplist = $this->department->getAllDepartmentDetails();
    $dept_option[''] = 'Select Department';
    foreach ($deplist as $item) {
      $dept_option[$item->codename] = $item->codevalues;
    }

    if ($mode == 'edit') {
      $codepk = $data->parent;
      $res = $this->department->getDepartmentDetailsById($codepk);
      $dept = $res->codename;
    }

    $form['designation']['department'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Department :'),
      '#options'       => $dept_option,
      '#attributes'    => ['class' => ['validate[required]']],
      '#prefix'        => '<div class="row"><div class="col-md-12">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data) ? $dept : '',
      '#field_suffix' => '<a href="' . $base_url . '/department/modal" class="use-ajax button"><i class="fadehide mdi mdi-settings fa-fw"></i></a>',
    ];
    $form['designation']['submit'] = [
      '#type'          => 'submit',
      '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
      '#button_type'   => 'primary',
      '#attributes'    => ['class' => ['btn btn-info']],
      '#prefix'        => '<br/><div class="row"><div class="col-md-2"></div><div class="col-md-6">',
      '#suffix'        => '',
    ];

    $form['designation']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#attributes' => ['class' => ['btn btn-default']],
      '#prefix'    => '',
      '#suffix'    => '</div></div>',
      '#url' => Url::fromRoute('view.designation.page'),
    ];
    $form['designation']['cancel']['#submit'][] = '::ActionCancel';

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $code_config = $this->configuration->getDesignationCodeConfig();
    $field = $form_state->getValues();

    // Check codevalues OFF then auto generate the code values.
    $code = ($code_config->codevalues == 'on') ? $field['code'] : $this->library->generateCode('DSG', $field['name']);

    $name = $field['name'];
    $parent = $field['department'];

    $parent = $this->department->getDepartmentId($parent);

    $field = [
      'codevalues' => $name,
      'codename'   => $code,
      'parent'   => $parent->codepk,
      'codetype'   => 'designation',
    ];

    $mode = $this->library->getActionMode();
    if ($mode == 'add') {
      $this->designation->setDesignation($field);
      $this->messenger->addMessage($field['codevalues'] . " has been succesfully created.");
    }
    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $this->encrypt->decode($pk);
      $this->designation->updateDesignation($field, $pk);
      $this->messenger->addMessage($field['codevalues'] . " has succesfully Updated.");
    }

    $form_state->setRedirect('view.designation.page');

  }

}
