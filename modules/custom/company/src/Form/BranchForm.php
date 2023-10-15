<?php

namespace Drupal\company\Form;

use Drupal\company\Model\BranchModel;
use Drupal\company\Model\ConfigurationModel;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\library\Controller\Encrypt;
use Drupal\library\Lib\LibController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Messenger\MessengerInterface;

/**
 * BranchForm creates the Form for Branch.
 */
class BranchForm extends FormBase {
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
   * Include the branch service.
   *
   * @var \Drupal\company\Model\BranchModel
   */
  protected $branch;

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
   * BranchForm constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\library\Lib\LibController $library
   *   The library service.
   * @param \Drupal\company\Model\BranchModel $branch
   *   The branch service.
   * @param \Drupal\library\Controller\Encrypt $encrypt
   *   The library service.
   * @param \Drupal\company\Model\ConfigurationModel $configuration
   *   The configuration service.
   */
  public function __construct(MessengerInterface $messenger, LibController $library, BranchModel $branch, Encrypt $encrypt, ConfigurationModel $configuration) {
    $this->messenger = $messenger;
    $this->library = $library;
    $this->branch = $branch;
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
      $container->get('branch.service'),
      $container->get('encrypt.service'),
      $container->get('configuration.service'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'branch_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $mode = $this->library->getActionMode();
    $form_state->setCached(FALSE);
    $form_title = 'Add Branch details';
    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $this->encrypt->decode($pk);

      $data = $this->branch->getBranchDetailsById($pk);

      $form_title = 'Edit Branch - ' . $data->codevalues;
      $this->library->setPageTitle($form_title);
    }

    $form['#attached']['library'][] = 'singleportal/master-validation';
    $form['#attributes']['class'] = 'form-horizontal';
    $form['#attributes']['autocomplete'] = 'off';
    $form['branch']['name'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Branch Name:'),
      '#attributes'    => [
        'class' => ['form-control', 'validate[required,custom[onlyLetterSp]]'],
      ],
      '#prefix'        => '<div class="row">',
      '#default_value' => isset($data) ? $data->codevalues : '',
      '#field_suffix'  => '<i class="fadehide mdi mdi-help-circle" title="Branch name of your company " data-toggle="tooltip"></i>',
    ];

    $branchcode_config = $this->configuration->getBranchCodeConfig();
    $brnch_config = [];
    $brnch_config['disabled'] = '';
    $brnch_config['branchcode'] = '';
    $brnch_config['helpmsg'] = 'Mention Branch Code of the person';

    if ($branchcode_config->codevalues == 'off') {
      $brnch_config['disabled'] = 'disabled';
      $brnch_config['branchcode'] = 'XXXXXXX';
      $brnch_config['helpmsg'] = 'Branch Code will be auto generate';
    }
    $form['branch']['code'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Branch Code:'),
      '#attributes'    => [
        'class' => ['form-control', 'validate[required,custom[onlyLetterSp]]'],
      ],
      '#suffix'        => '</div>',
      '#default_value' => isset($data) ? $data->codename : $brnch_config['branchcode'],
      '#disabled' => $brnch_config['disabled'],
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="' . $brnch_config['helpmsg'] . '" data-toggle="tooltip"></i>',
    ];

    $statelist = $this->library->getStateList();

    $form['branch']['state'] = [
      '#type'    => 'select',
      '#title'   => $this->t('State:'),
      '#options' => $statelist,
      '#attributes'    => ['class' => ['form-control', 'validate[required]']],
      '#prefix'        => '<div class="row">',
      '#default_value' => isset($data) ? $data->state : $form_state->getValue('state'),
      '#ajax' => [
        'callback' => '::getCityList',
        'wrapper' => 'citylist',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
        ],
      ],
    ];
    if (!empty($form_state->getValue('state'))) {
      $statePk = $form_state->getValue('state');
    }
    else {
      $statePk = isset($data) ? $data->state : '';
    }

    $cityLst = [];
    $cityLst = $this->library->getCityListByState($statePk);

    $form['branch']['city'] = [
      '#type'          => 'select',
      '#title'         => $this->t('City:'),
      '#options'       => $cityLst,
      '#attributes'    => ['class' => ['form-control', 'validate[required]']],
      '#prefix'        => '<div id="citylist">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data) ? $data->city : $form_state->getValue('city'),
    ];

    $form['branch']['location'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Location:'),
      '#attributes'    => ['class' => ['form-control', 'validate[required]']],
      '#prefix'        => '<div class="row">',
      '#default_value' => isset($data) ? $data->location : '',
    ];

    $form['branch']['pincode'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Pincode'),
      '#attributes'    => [
        'class' => ['form-control', 'validate[required]'],
        'id' => ['pincode'],
      ],
      '#default_value' => isset($data) ? $data->pincode : '',
      '#suffix'        => '</div>',
    ];

    $form['branch']['#type'] = 'actions';
    $form['branch']['submit'] = [
      '#type'          => 'submit',
      '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
      '#button_type'   => 'primary',
      '#attributes'    => ['class' => ['btn btn-info']],
      '#prefix'        => '<div class="row"><div class="col-md-5"></div><div class="col-md-4">',
      '#suffix'        => '',
    ];

    $form['branch']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#attributes' => ['class' => ['btn btn-default']],
      '#prefix' => '',
      '#suffix' => '</div></div>',
      '#url' => Url::fromRoute('view.branch.page'),
    ];

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
    $code_config = $this->configuration->getBranchCodeConfig();

    // Check codevalues OFF then auto generate the code values.
    $code = ($code_config->codevalues == 'on') ? $field['code'] : $this->library->generateCode('BR', $field['name']);

    $name = $field['name'];
    $location = $field['location'];
    $city = $field['city'];
    $state = $field['state'];
    $pincode = $field['pincode'];

    $data = [
      'codevalues' => $name,
      'codename' => $code,
      'codetype' => 'branch',
      'location' => $location,
      'city' => $city,
      'state' => $state,
      'pincode' => $pincode,
    ];

    $mode = $this->library->getActionMode();

    if ($mode == 'add') {
      $this->branch->setBranch($data);
      $this->messenger->addMessage($data['codevalues'] . " has been created.");
    }
    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $this->encrypt->decode($pk);
      $this->branch->updateBranch($data, $pk);
      $this->messenger->addMessage($data['codevalues'] . " has succesfully Updated.");
    }
    $form_state->setRedirect('view.branch.page');

  }

  /**
   * Get City List via Ajax.
   */
  public function getCityList(array $form, FormStateInterface $form_state) {
    return $form['branch']['city'];
  }

}
