<?php

namespace Drupal\organisation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Drupal\library\Controller\Encrypt;
use Drupal\library\Lib\LibController;
use Drupal\organisation\Model\ConfigurationModel;
use Drupal\organisation\Model\WorkorderModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * TeamorderForm creates the Form for Team.
 */
class TeamorderForm extends FormBase {
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
   * @var \Drupal\organisation\Model\WorkorderModel
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
   * @param \Drupal\organisation\Model\WorkorderModel $workorder
   *   The workorder service.
   * @param \Drupal\library\Controller\Encrypt $encrypt
   *   The library service.
   * @param \Drupal\organisation\Model\ConfigurationModel $configuration
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
    return 'teamorder_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $mode = $this->library->getActionMode();

    $form['#attached']['library'][] = 'singleportal/master-validation';
    $form['#attached']['library'][] = 'organisation/workorder-lib';
    $form['#attributes']['class'] = 'form-horizontal';
    $form['#attributes']['autocomplete'] = 'off';

    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $this->encrypt->decode($pk);

      $data = $this->workorder->getTeamorderById($pk);

    }

    $form['workorder']['teamname'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Team order Name:'),
      '#attributes'    => [
        'class' => ['validate[required,custom[onlyLetterSp]]'],
      ],
      '#prefix'        => '<div class="row"><div class="col-md-12">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data) ? $data->codevalues : '',
    ];

    $workcode_config = $this->configuration->getWorkorderCodeConfig();
    $work_config = [];
    $work_config['disabled'] = '';
    $work_config['workordercode'] = '';
    $work_config['helpmsg'] = 'Mention Teamorder Number';

    if ($workcode_config->codevalues == 'off') {
      $work_config['disabled'] = 'disabled';
      $work_config['branchcode'] = 'XXXXXXX';
      $work_config['helpmsg'] = 'Workorder Code will be auto generate';
    }

    $form['workorder']['teamcode'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Team order No:'),
      '#attributes'    => [
        'class' => ['validate[required,custom[onlyLetterSp]]'],
      ],
      '#prefix'        => '<div class="row"><div class="col-md-12">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data) ? $data->codename : $work_config['workordercode'],
      '#disabled'      => $work_config['disabled'],
      '#default_value' => isset($data) ? $data->codename : '',
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="' . $work_config['helpmsg'] . '" data-toggle="tooltip"></i>',
    ];

    $worklist = $this->workorder->getWorkorderList();
    $work_option[''] = 'Select WorkOrder';
    foreach ($worklist as $item) {
      $work_option[$item->codepk] = $item->codevalues;
    }

    if ($mode == 'edit') {
      $codepk = $data->parent;
      $res = $this->workorder->getWorkorderById($codepk);
      $workList = $res->codepk;
    }

    $form['workorder']['workorder'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Work Order :'),
      '#options'       => $work_option,
      '#attributes'    => ['class' => ['validate[required]']],
      '#prefix'        => '<div class="row"><div class="col-md-12">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data) ? $workList : '',
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
      '#url' => Url::fromRoute('view.workorder.teamorder'),
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
    $code = ($code_config->codevalues == 'on') ? $field['teamcode'] : $this->library->generateCode('WR', $field['workname']);

    $data = [
      'codename'     => $code,
      'codevalues' => $field['teamname'],
      'parent' => $field['workorder'],
    ];

    $mode = $this->library->getActionMode();

    if ($mode == 'add') {
      $this->workorder->setTeamOrder($data);
      $this->messenger->addMessage($data['codevalues'] . " is created.");
    }
    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $this->encrypt->decode($pk);
      $this->workorder->updateTeamorder($data, $pk);
      $this->messenger->addMessage($data['codevalues'] . " is succesfully Updated.");
    }

    $form_state->setRedirect('view.workorder.teamorder');
  }

}
