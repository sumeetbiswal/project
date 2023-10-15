<?php

namespace Drupal\company\Form;

use Drupal\company\Model\ConfigurationModel;
use Drupal\company\Model\WorkorderModel;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\library\Controller\Encrypt;
use Drupal\library\Lib\LibController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * WorkorderForm creates the Form for Workorder.
 */
class WorkorderForm extends FormBase {
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
   * Include the workorder service.
   *
   * @var \Drupal\company\Model\WorkorderModel
   */
  protected $workorder;

  /**
   * Include the custom library service.
   *
   * @var \Drupal\library\Controller\Encrypt
   */
  protected $encrypt;

  /**
   * Include the custom configuration service.
   *
   * @var \Drupal\company\Model\ConfigurationModel
   */
  protected $configuration;

  /**
   * DesignationForms constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\library\Lib\LibController $library
   *   The library service.
   * @param \Drupal\company\Model\WorkorderModel $workorder
   *   The workorder service.
   * @param \Drupal\library\Controller\Encrypt $encrypt
   *   The library service.
   * @param \Drupal\company\Model\ConfigurationModel $configuration
   *   The configuration service.
   */
  public function __construct(MessengerInterface $messenger, LibController $library, WorkorderModel $workorder, Encrypt $encrypt, ConfigurationModel $configuration) {
    $this->messenger = $messenger;
    $this->library = $library;
    $this->workorder = $workorder;
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
      $container->get('workorder.service'),
      $container->get('encrypt.service'),
      $container->get('configuration.service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'workorder_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $mode = $this->library->getActionMode();

    $form['#attached']['library'][] = 'singleportal/master-validation';
    $form['#attached']['library'][] = 'company/workorder-lib';
    $form['#attributes']['class'] = 'form-horizontal';
    $form['#attributes']['autocomplete'] = 'off';
    $form['department']['#prefix'] = '<div class="row"> <div class="panel panel-inverse"><h3 class="box-title">Work Order</h3>
                                      <hr/><div class="panel-body">';

    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $this->encrypt->decode($pk);

      $data = $this->workorder->getWorkorderById($pk);

    }

    $form['workorder']['workname'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Work order Name:'),
      '#attributes'    => [
        'class' => ['form-control', 'validate[required,custom[onlyLetterSp]]'],
      ],
      '#prefix'        => '<div class="row">',
      '#suffix'        => '</div>',
      '#default_value' => isset($data) ? $data->codevalues : '',
    ];

    $workcode_config = $this->configuration->getWorkorderCodeConfig();
    $work_config = [];
    $work_config['disabled'] = '';
    $work_config['workordercode'] = '';
    $work_config['helpmsg'] = 'Mention Workorder number or Project number';

    if ($workcode_config->codevalues == 'off') {
      $work_config['disabled'] = 'disabled';
      $work_config['branchcode'] = 'XXXXXXX';
      $work_config['helpmsg'] = 'Workorder Code will be auto generate';
    }

    $form['workorder']['workcode'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Work order No:'),
      '#attributes'    => [
        'class' => ['form-control', 'validate[required,custom[onlyLetterSp]]'],
      ],
      '#prefix'        => '<div class="row">',
      '#suffix'        => '</div>',
      '#default_value' => isset($data) ? $data->codename : $work_config['workordercode'],
      '#disabled'      => $work_config['disabled'],
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="' . $work_config['helpmsg'] . '" data-toggle="tooltip"></i>',
    ];

    // End of Repeater Field.
    $form['save']['submit'] = [
      '#type'          => 'submit',
      '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
      '#button_type'   => 'primary',
      '#attributes'    => ['class' => ['btn btn-info']],
      '#prefix'        => '<br/><div class="row"><div class="col-md-2"></div><div class="col-md-4">',
      '#suffix'        => '',
    ];

    $form['save']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#attributes'               => ['class' => ['btn btn-default']],
      '#suffix'                   => '</div></div>',
      '#url' => Url::fromRoute('view.workorder.page'),
    ];
    $form_state->setCached(FALSE);
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

    $field = $form_state->getValues();
    $code_config = $this->configuration->getWorkorderCodeConfig();

    // Check codevalues OFF then auto generate the code values.
    $code = ($code_config->codevalues == 'on') ? $field['workcode'] : $this->library->generateCode('WR', $field['workname']);

    $data = [
      'codename'    => $code,
      'codevalues' => $field['workname'],
    ];

    $mode = $this->library->getActionMode();

    if ($mode == 'add') {
      $this->workorder->setWorkOrder($data);
      $this->messenger->addMessage($data['codevalues'] . " is created.");
    }
    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $this->encrypt->decode($pk);
      $this->workorder->updateWorkorder($data, $pk);
      $this->messenger->addMessage($data['codevalues'] . " is succesfully Updated.");
    }

    $form_state->setRedirect('view.workorder.page');
  }

}
