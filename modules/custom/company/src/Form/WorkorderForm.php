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
    $form['workorder']['workname'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Work order Name:'),
      '#attributes'    => [
        'class' => ['form-control', 'validate[required,custom[onlyLetterSp]]'],
      ],
      '#prefix'        => '<div class="row">',
      '#default_value' => isset($data) ? $data->codevalues : '',
    ];

    $workcode_config = $this->configuration->getWorkorderCodeConfig();
    $work_config = [];
    $work_config['disabled'] = '';
    $work_config['workordercode'] = '';
    $work_config['helpmsg'] = 'Mention Workorder Code of the person';

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
      '#suffix'        => '</div>',
      '#default_value' => isset($data) ? $data->codename : $work_config['workordercode'],
      '#disabled'      => $work_config['disabled'],
      '#default_value' => isset($data) ? $data->codevalues : '',
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="' . $work_config['helpmsg'] . '" data-toggle="tooltip"></i>',
    ];

    // Repeater Field for Team Form.
    $team_field_count = $form_state->get('num_team');

    if (empty($team_field_count)) {
      $team_field_count = $form_state->set('num_team', 1);
    }

    $form['addmore']['actions'] = [
      '#type' => 'actions',
    ];
    $form['addmore']['actions']['add_team'] = [
      '#type' => 'submit',
      '#name' => 'Team',
      '#prefix' => $html,
      '#value' => ' ',
      '#limit_validation_errors' => [],
      '#submit' => ['::addOneTeam'],
      '#attributes' => ['class' => ['addmore']],
      '#ajax' => [
        'callback' => '::addmoreCallbackTeam',
        'wrapper' => "team-id",
      ],
    ];

    $form['team']['teamorder'] = [
      // '#prefix' => $html,
      '#prefix' => '<div id="team-id"><div class="panel-body">',
      '#suffix' => '</div></div>',
      '#attributes' => ['class' => ['']],
      '#type' => 'table',
      '#title' => 'Sample Table',
      '#header' => [
        ['data' => 'SLNO', 'class' => 'text-center', 'width' => '1%'],
        ['data' => 'Team Name', 'class' => 'text-center', 'width' => '13%'],
        ['data' => 'Team Order No', 'class' => 'text-center', 'width' => '13%'],
        ['data' => 'Action', 'class' => 'text-left', 'width' => '13%'],
      ],
    ];

    for ($i = 0; $i < $form_state->get('num_team'); $i++) {
      $cntq = $i + 1;

      $form['team']['teamorder'][$i]['slno'] = [
        '#type'            => 'item',
        '#markup' => $cntq,
        '#title_display' => 'invisible',
      ];

      $form['team']['teamorder'][$i]['name'] = [
        '#type'             => 'textfield',
        '#title'             => $this->t('Team Name'),
        '#default_value'     => '',
        '#title_display' => 'invisible',
        '#attributes'    => ['class' => ['form-control']],
        '#prefix'    => '',
      ];
      $form['team']['teamorder'][$i]['order'] = [
        '#type'             => 'textfield',
        '#title'             => $this->t('Team Order No'),
        '#default_value'     => '',
        '#title_display' => 'invisible',
        '#attributes'    => ['class' => ['form-control']],
      ];

      if ($i == $form_state->get('num_team') - 1) {
        $form['team']['teamorder'][$i]['actions']['remove_name_team'] = [
          '#type' => 'submit',
          '#name' => 'qualification_remove' . $i,
          '#value' => '.',
          '#attributes' => ['class' => ['removeitem']],
          '#limit_validation_errors' => [],
          '#submit' => ['::removeCallbackTeam'],
          '#ajax' => [
            'callback' => '::addmoreCallbackTeam',
            'wrapper' => "team-id",
            'progress' => [
              'type' => 'throbber',
            ],
          ],
        ];
      }
    }

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
      'workorder' => [
        'codename'    => $code,
        'codevalues' => $field['workname'],
      ],
      'teamorder'    => [],
    ];

    // Looping team repeater array and collecting data.
    foreach ($field['teamorder'] as $team) {
      $data['teamorder'][] = [
        'codename'     => $team['order'],
        'codevalues' => $team['name'],
      ];
    }

    $this->workorder->setWorkOrder($data);

    $this->messenger->addMessage("Word order has been created.");

    $form_state->setRedirect('view.workorder.page');
  }

}
