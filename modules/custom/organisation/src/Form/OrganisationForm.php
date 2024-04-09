<?php

namespace Drupal\organisation\Form;

use Drupal\organisation\Model\OrganisationModel;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Drupal\library\Controller\Encrypt;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\library\Lib\LibController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\File\FileSystemInterface;

/**
 * OrganisationForm creates the Form for Organisation.
 */
class OrganisationForm extends FormBase {
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
   * Include the organisation service.
   *
   * @var \Drupal\organisation\Model\OrganisationModel
   */
  protected $organisation;

  /**
   * Include the cache service.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $cache;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * OrganisationForm constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\library\Lib\LibController $library
   *   The library service.
   * @param \Drupal\organisation\Model\OrganisationModel $organisation
   *   The organisation service.
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $cache
   *   The organisation service.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The filesystem service.
   */
  public function __construct(MessengerInterface $messenger, LibController $library, OrganisationModel $organisation, CacheBackendInterface $cache, FileSystemInterface $file_system) {
    $this->messenger = $messenger;
    $this->library = $library;
    $this->organisation = $organisation;
    $this->cache = $cache;
    $this->fileSystem = $file_system;
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
      $container->get('organisation.service'),
      $container->get('cache.render'),
      $container->get('file_system'),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'organisation_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $encrypt = new Encrypt();
    $mode = $this->library->getActionMode();
    $title = 'Add Organisation Details';
    if ($mode == 'edit') {
      $pk = $this->library->getIdFromUrl();
      $pk = $encrypt->decode($pk);
      $data = $this->organisation->getOrganisationDetailsById($pk);
      $title = 'Edit Organisation Details';
    }
    $form['#attributes']['class'] = 'form-horizontal';
    $form['#attributes']['autocomplete'] = 'off';
    $form['#attached']['library'][] = 'singleportal/master-validation';
    $form['organisation']['#attributes']['enctype'] = "multipart/form-data";
    $form['organisation']['#prefix'] = '<div class="row"> <div class="panel panel-inverse">
                            <div class="panel-heading"> ' . $title . '</div><div class="panel-body">';
    $form['organisation']['cname'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Organisation Name:'),
      '#attributes' => [
        'class' => ['validate[required,custom[onlyLetterSp]]'],
      ],
      '#prefix' => '<div class="row">',
      '#default_value' => isset($data) ? $data->organisationname : '',
    ];

    $complist = $this->organisation->getOrganisationTypeList();
    $comp_option[''] = 'Select Type of Organisation';
    foreach ($complist as $item) {
      $comp_option[$item->codename] = $item->codevalues;
    }

    $form['organisation']['ctype'] = [
      '#type' => 'select',
      '#title' => $this->t('Organisation Type:'),
      '#options' => $comp_option,
      '#attributes' => ['class' => ['validate[required]']],
      '#suffix' => '</div>',
      '#default_value' => isset($data) ? $data->organisationtype : '',
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Category which your organisation belongs to" data-toggle="tooltip"></i>',
    ];
    $form['organisation']['cemail'] = [
      '#type' => 'email',
      '#title' => $this->t('Email:'),
      '#attributes' => [
        'class' => ['validate[required,custom[email]]'],
      ],
      '#prefix' => '<div class="row">',
      '#default_value' => isset($data) ? $data->email : '',
    ];

    $form['organisation']['cphone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone number:'),
      '#attributes' => [
        'class' => ['validate[required][custom[phone]]'],
      ],
      '#suffix' => '</div>',
      '#default_value' => isset($data) ? $data->phone : '',
    ];
    $form['organisation']['caddress1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address Line-1:'),
      '#attributes' => ['class' => ['validate[required]']],
      '#prefix' => '<div class="row">',
      '#default_value' => isset($data) ? $data->address1 : '',

    ];
    $form['organisation']['caddress2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address Line-2:'),
      '#suffix' => '</div>',
      '#default_value' => isset($data) ? $data->address2 : '',

    ];
    $statelist = $this->library->getStateList();
    $form['organisation']['state'] = [
      '#type'    => 'select',
      '#title'   => $this->t('State:'),
      '#options' => $statelist,
      '#attributes'    => ['class' => ['validate[required]']],
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
    $form['organisation']['city'] = [
      '#type'          => 'select',
      '#title'         => $this->t('City:'),
      '#options'       => $cityLst,
      '#attributes'    => ['class' => ['validate[required]']],
      '#prefix'        => '<div id="citylist">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data) ? $data->city : $form_state->getValue('city'),
    ];

    $form['organisation']['country'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Country:'),
      '#options' => [
        '101' => $this->t('India'),
      ],
      '#attributes'    => ['class' => ['validate[required]']],
      '#default_value' => isset($data) ? $data->country : '',
      '#prefix' => '<div class="row">',
    ];

    $form['organisation']['pincode'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Pincode'),
      '#attributes'    => ['class' => ['validate[required]']],
      '#default_value' => isset($data) ? $data->pincode : '',
      '#suffix'        => '</div>',

    ];

    $form['organisation']['clogo'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Organisation logo:'),
      '#description'     => $this->t('Upload your organisation Logo png only <br/> Reslution should be 140 X 25'),
      '#upload_location' => 'public://temp-img',
    // 'file_validate_image_resolution' => array('140x25', '100x25')),
      '#upload_validators' => ['file_validate_extensions' => ['png']],
      '#theme' => 'image_widget',
      '#preview_image_style' => 'medium',
      '#suffix' => '</div>',
      '#prefix' => '<div class="row">',
    ];

    $form['organisation']['#type'] = 'actions';
    $form['organisation']['submit'] = [
      '#type' => 'submit',
      '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
      '#button_type' => 'primary',
      '#attributes' => ['class' => ['btn btn-info']],
      '#prefix' => '<div class="row"></div><div class="row"><div class="col-md-5"></div><div class="col-md-4">',
      '#suffix' => '',
    ];
    $form['organisation']['cancel'] = [
      '#title' => $this->t('Cancel'),
      '#type' => 'link',
      '#attributes' => ['class' => ['btn btn-default']],
      '#url' => Url::fromRoute('organisation.view'),
      '#prefix' => '&nbsp; &nbsp; &nbsp; &nbsp;',
      '#suffix' => '</div></div>',
    ];
    $form['organisation']['cancel']['#submit'][] = '::actionCancel';
    $form['organisation']['#suffix'] = '</div></div></div></div>';
    return $form;
  }

  /**
   * Helper function to redirect user on clicking of cancel button.
   */
  public function actionCancel(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('organisation.view');
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (trim($form_state->getValue('cname')) == '') {
      $form_state->setErrorByName('cname', $this->t('Enter your Organisation Name'));
    }
    elseif (!preg_match("/^[A-Za-z]+((\s)?([A-Za-z])+)*$/", $form_state->getValue('cname'))) {
      $form_state->setErrorByName('cname', $this->t('Enter a valid Organisation Name'));
    }
    if (trim($form_state->getValue('ctype')) == '') {
      $form_state->setErrorByName('ctype', $this->t('Enter your Organisation Type'));
    }

    if (trim($form_state->getValue('cemail')) == '') {
      $form_state->setErrorByName('cemail', $this->t('Enter Organisation Email Id'));
    }
    elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $form_state->getValue('cemail'))) {
      $form_state->setErrorByName('cemail', $this->t('Enter a valid Email Id'));
    }

    if (trim($form_state->getValue('cphone')) == '') {
      $form_state->setErrorByName('cphone', $this->t('Enter Organisation phone number'));
    }
    elseif (!preg_match("/^[6-9][0-9]{9}$/", $form_state->getValue('cphone'))) {
      $form_state->setErrorByName('cphone', $this->t('Enter a valid phone number'));
    }
    if (strlen($form_state->getValue('cphone')) < 10) {
      $form_state->setErrorByName('cphone', $this->t('Mobile number is too short.'));
      echo "<div>errorrrr</div>";
    }

    if (trim($form_state->getValue('caddress1')) == '') {
      $form_state->setErrorByName('caddress1', $this->t('Enter your Address.'));
    }

    if (trim($form_state->getValue('pincode')) == '') {
      $form_state->setErrorByName('pincode', $this->t('Enter your pincode.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $field = $form_state->getValues();

    $data = [
      'organisationname' => $field['cname'],
      'organisationcode' => $field['cname'],
      'organisationtype' => $field['ctype'],
      'email'        => $field['cemail'],
      'phone'         => $field['cphone'],
      'address1'     => $field['caddress1'],
      'address2'     => $field['caddress2'],
      'state'         => $field['state'],
      'city'         => $field['city'],
      'country'     => $field['country'],
      'pincode'     => $field['pincode'],
    ];

    $mode = $this->library->getActionMode();

    if ($mode == 'add') {
      $this->organisation->setOrganisation($data);
      $this->messenger->addMessage($data['organisationname'] . " has succesfully created.");
    }
    if ($mode == 'edit') {
      $this->organisation->updateOrganisation($data);
      $this->messenger->addMessage($data['organisationname'] . "  has succesfully Updated.");
    }

    /*
     * upload image int temp path 'temp-img' while choosing the image
     * once you submit move the image from temp-img to logo.png
     */
    if (!empty($field['clogo'][0]) && isset($field['clogo'][0])) {
      $logo_file = file::load($field['clogo'][0]);
      $logo_path = $this->fileSystem->realpath($logo_file->getFileUri());
      $move_to_path = $this->fileSystem->realpath('public://logo.png');
      rename($logo_path, $move_to_path);

      // Clearing only render cache to reflect logo immiditaely.
      $this->cache->invalidateAll();
    }

    $form_state->setRedirect('organisation.view');
  }

  /**
   * Helper function to get city list through ajax.
   */
  public function getCityList(array $form, FormStateInterface $form_state) {
    return $form['organisation']['city'];
  }

}
