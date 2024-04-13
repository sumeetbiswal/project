<?php

namespace Drupal\organisation\Form;

use Drupal\organisation\Model\ConfigurationModel;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\library\Controller\Encrypt;
use Drupal\library\Lib\LibController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * TimingConfigurationForm creates form for the time config.
 */
class TimingConfigurationForm extends FormBase {
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
   * BranchForm constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\library\Lib\LibController $library
   *   The library service.
   * @param \Drupal\library\Controller\Encrypt $encrypt
   *   The library service.
   * @param \Drupal\organisation\Model\ConfigurationModel $configuration
   *   The configuration service.
   */
  public function __construct(MessengerInterface $messenger, LibController $library, Encrypt $encrypt, ConfigurationModel $configuration) {
    $this->messenger = $messenger;
    $this->library = $library;
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
      $container->get('encrypt.service'),
      $container->get('configuration.service'),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'tming_configuration_form';

  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    global $base_url;

    $result = $this->configuration->getShiftTimingList();

    $form['organisation']['#attributes']['enctype'] = "multipart/form-data";
    $form['#attached']['library'][] = 'singleportal/time-picker';

    $form['organisation']['#suffix'] = '</div>';

    $mode = $this->library->getActionMode();

    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $this->encrypt->decode($pk);
      $data = $this->configuration->getShiftDetailsById($pk);
    }

    $form['organisation']['shiftname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Shift Name:'),
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>',
      '#default_value' => isset($data) ? $data->codevalues : '',
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="For EX: Morning Shift / Evenging Shift / Night Shift" data-toggle="tooltip"></i>',

    ];

    $form['organisation']['fromtime'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Time From:'),
      '#attributes' => ['id' => 'time1'],
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>',
      '#default_value' => isset($data) ? $data->description : '',
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Shift Start Timing" data-toggle="tooltip"></i>',
    ];

    $form['organisation']['totime'] = [
      '#type' => 'textfield',
      '#title' => $this->t('To Time:'),
      '#attributes' => ['id' => 'time2'],
      '#prefix' => '<div class="row">',
      '#suffix' => '</div>',
      '#default_value' => isset($data) ? $data->email : '',
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Shift End Timing" data-toggle="tooltip"></i>',
    ];

    $form['organisation']['#type'] = 'actions';
    $form['organisation']['submit'] = [
      '#type' => 'submit',
      '#default_value' => ($mode == 'config') ? $this->t('Submit') : $this->t('Update'),
      '#button_type' => 'primary',
      '#attributes' => ['class' => ['btn btn-info']],
      '#prefix' => '<br/><div class="row"><div class="col-md-2"></div><div class="col-md-4">',
      '#suffix' => '</div></div>',
    ];

    if ($mode == 'edit') {
      $form['organisation']['submit']['#suffix'] = '';

      $form['organisation']['cancel'] = [
        '#title' => $this->t('Cancel'),
        '#type' => 'link',
        '#attributes' => ['class' => ['btn btn-default']],
        '#url' => Url::fromRoute('organisation.configuration_shift'),
        '#prefix' => '&nbsp; &nbsp; &nbsp;',
        '#suffix' => '</div></div>',
      ];
    }

    $rows = [];
    $sl = 0;
    foreach ($result as $item) {
      $sl++;
      $codepk_encoded = $this->encrypt->encode($item->codepk);

      $url = $base_url . '/shift/edit/' . $codepk_encoded;
      $name = new FormattableMarkup('<i class="icon-note" title="" data-toggle="tooltip" data-original-title="Edit"></i>', []);
      $edit = new FormattableMarkup(
        '<a href=":link" style="text-align:center" >@name</a>',
        [':link' => $url, '@name' => $name]);

      $rows[] = [
        'data' => [
          $sl, $item->codevalues, $item->description . ' - ' . $item->email, $edit,
        ],
      ];
    }

    $form['organisation']['shiftlist'] = [
      '#type'         => 'table',
      '#header'       => [
        $this->t('SL'),
        $this->t('Shift Name'),
        $this->t('Timing'),
        $this->t('Action'),
      ],
      '#rows'            => $rows,
      '#attributes' => ['class' => ['table text-center table-hover table-striped table-bordered dataTable']],
      '#prefix'     => '<br/><br/><br/>',
    ];

    return $form;

  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    if (trim($form_state->getValue('shiftname')) == ' ') {
      $form_state->setErrorByName('shiftname', $this->t('Enter your shift Name'));
    }
    elseif (!preg_match("/^[a-zA-Z'-]+$/", $form_state->getValue('shiftname'))) {
      $form_state->setErrorByName('shiftname', $this->t('Enter a valid Shift Name'));
    }
    if (empty($form_state->getValue('fromtime'))) {
      $form_state->setErrorByName('fromtime', $this->t('Enter your shift time'));
    }
    if (empty($form_state->getValue('totime')) == ' ') {
      $form_state->setErrorByName('totime', $this->t('Enter your shift ending time'));
    }

  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $fieldval = $form_state->getValues();

    $codename = $this->library->generateCode('SHFT', $fieldval['shiftname']);

    $field = [
      'codetype'    => 'jobshift',
      'codename'    => $codename,
      'codevalues'  => $fieldval['shiftname'],
      'description' => $fieldval['fromtime'],
      'email'        => $fieldval['totime'],
    ];

    $mode = $this->library->getActionMode();

    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $this->encrypt->decode($pk);
      $this->configuration->updateShiftTiming($field, $pk);
      $this->messenger->addMessage($field['codevalues'] . " has been updated.");
    }
    else {
      $this->configuration->setShiftTiming($field);
      $this->messenger->addMessage($field['codevalues'] . " has been created.");
    }

    $form_state->setRedirect('organisation.configuration_shift');
  }

}
