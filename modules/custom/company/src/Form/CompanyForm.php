<?php
/**
 * @file
 * Contains \Drupal\company\Form\CompanyForm.
 */

namespace Drupal\company\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\library\Lib\LibController;
use Drupal\file\Entity\File;

class CompanyForm extends FormBase {

  public function getFormId() {
	return 'company_form';

  }

public function buildForm(array $form, FormStateInterface $form_state) {

  $libobj = \Drupal::service('library.service');
	$compobj = \Drupal::service('company.service');
	$encrypt = new \Drupal\library\Controller\Encrypt;

	$mode = $libobj->getActionMode();
	$title = 'Add Company Details';
   if($mode == 'edit'){
		$pk = $libobj->getIdFromUrl();
		$pk = $encrypt->decode($pk);
		$data = $compobj->getCompanyDetailsById($pk);
		$title = 'Edit Company Details';
   }
  $form['#attributes']['class'] = 'form-horizontal';
  $form['#attributes']['autocomplete'] = 'off';
  $form['#attached']['library'][] = 'singleportal/master-validation';
  $form['company']['#attributes']['enctype'] = "multipart/form-data";

	$form['company']['#prefix'] = '<div class="row"> <div class="panel panel-inverse">
                            <div class="panel-heading"> '.$title.'</div><div class="panel-body">';

	$form['company']['cname'] = array(
      '#type' => 'textfield',
      '#title' => t('Organisation Name:'),
      //'#required' => TRUE,
	 '#attributes' => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
	 '#prefix' => '<div class="row">',
	 //'#suffix' => '</div>',
	 '#default_value' => isset($data)? $data->companyname : '',

    );

	$complist = $compobj->getCompanyTypeList();
	$comp_option[''] = 'Select Type of Organisation';
	foreach($complist AS $item)
	{
		$comp_option[$item->codename]  = $item->codevalues;
	}

	$form['company']['ctype'] = array(
      '#type' => 'select',
      '#title' => t('Organisation Type:'),
      //'#required' => TRUE,
	  '#options' => $comp_option,
 	  '#attributes' => ['class' => ['form-control', 'validate[required]']],
	  //'#prefix' => '<div class="col-md-6">',
	 '#suffix' => '</div>',
	 '#default_value' => isset($data)? $data->companytype : '',
   '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Category which your organisation belongs to" data-toggle="tooltip"></i>',
    );
	$form['company']['cemail'] = array(
      '#type' => 'email',
      '#title' => t('Email:'),
      //'#required' => TRUE,
	  '#attributes' => ['class' => ['form-control', 'validate[required,custom[email]]']],
	   '#prefix' => '<div class="row">',
	// '#suffix' => '</div>',
	 	 '#default_value' => isset($data)? $data->email : '',

    );

	$form['company']['cphone'] = array(
      '#type' => 'tel',
      '#title' => t('Phone number:'),
    //  '#required' => TRUE,
	  '#attributes' => ['class' => ['form-control', 'validate[required][custom[phone]]']],
	  //'#prefix' => '<div class="col-md-6">',
	 '#suffix' => '</div>',
	 	 	 '#default_value' => isset($data)? $data->phone : '',

    );
	$form['company']['caddress1'] = array(
      '#type' => 'textfield',
      '#title' => t('Address Line-1:'),
    //  '#required' => TRUE,
	  '#attributes' => ['class' => ['form-control', 'validate[required]']],
	  '#prefix' => '<div class="row">',
	 //'#suffix' => '</div>',
	 '#default_value' => isset($data)? $data->address1 : '',

    );

	$form['company']['caddress2'] = array(
      '#type' => 'textfield',
      '#title' => t('Address Line-2:'),
    //  '#required' => TRUE,
	  '#attributes' => ['class' => ['form-control']],
	  //'#prefix' => '<div class="row">',
	 '#suffix' => '</div>',
	 '#default_value' => isset($data)? $data->address2 : '',

    );

	$statelist = $libobj->getStateList();

	$form['company']['state'] = array(
		'#type'    => 'select',
		'#title'   => t('State:'),
		'#options' => $statelist,
		//'#required'=> TRUE,
		'#attributes'    => ['class' => ['form-control', 'validate[required]']],
		'#prefix'        => '<div class="row">',
		'#default_value' => isset($data)? $data->state : $form_state->getValue('state'),
		'#ajax' => [
					'callback' => '::getCityList',
					'wrapper' => 'citylist',
					'event' => 'change',
					'progress' => [
					  'type' => 'throbber',
					  'message' => t(''),
					],
				  ],
    );

	if (!empty($form_state->getValue('state'))) {
		$statePk = $form_state->getValue('state');
    }
	else{
		$statePk = isset($data)? $data->state : '';
	}

	$cityLst = [];
	$cityLst = $libobj->getCityListByState($statePk);

	$form['company']['city'] = array(
      '#type'          => 'select',
      '#title'         => t('City:'),
      '#options'       => $cityLst,
      //'#required'      => TRUE,
      '#attributes'    => ['class' => ['form-control', 'validate[required]']],
      '#prefix'        => '<div id="citylist">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data)? $data->city : $form_state->getValue('city'),
    );

	$form['company']['country'] = array(
			'#type'          => 'select',
			'#title'         => t('Country:'),
			'#options' => [
				'101' => $this->t('India'),
			],
		'#attributes'    => ['class' => ['form-control', 'validate[required]']],
		'#default_value' => isset($data)? $data->country : '',
		'#prefix' => '<div class="row">',
		);

	  $form['company']['pincode'] = array(
		'#type'          => 'textfield',
		'#title'         => t('Pincode'),
		'#attributes'    => ['class' => ['form-control', 'validate[required]']],
		'#default_value' => isset($data)? $data->pincode : '',
		'#suffix'        => '</div>',

	  );

	$form['company']['clogo'] = array(
     '#type' => 'managed_file',
     '#title' => t('Organisation logo:'),
	 '#description' 	=> t('Upload your company Logo png only <br/> Reslution should be 140 X 25'),
	 '#upload_location' => 'public://temp-img',
	 '#attributes' => ['class' => ['form-control']],
	 '#upload_validators'=> array('file_validate_extensions' => array('png'),),//  'file_validate_image_resolution' => array('140x25', '100x25')),
	 //'#default_value'=> isset($data) ? array($data->clogo) : '',
	 '#theme' => 'image_widget',
	 '#preview_image_style' => 'medium',
	 '#suffix' => '</div>',
	 '#prefix' => '<div class="row">',
    );
	// $form['#suffix'] =  '</div>';


    $form['company']['#type'] = 'actions';
    $form['company']['submit'] = array(
      '#type' => 'submit',
      '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
      '#button_type' => 'primary',
	  '#attributes' => ['class' => ['btn btn-info']],
	  '#prefix' => '<div class="row"></div><div class="row"><div class="col-md-5"></div><div class="col-md-4">',
	  '#suffix' => '',
		  );

	/*$form['company']['cancel'] = array(
      '#type' => 'submit',
      '#value' => t('Cancel'),
	  '#attributes' => ['class' => ['btn btn-default']],
	  '#limit_validation_errors' => array(),
	  '#prefix' => '',
	  '#suffix' => '</div></div>',
		  );*/

	$form['company']['cancel'] = [
		  '#title' => $this->t('Cancel'),
		  '#type' => 'link',
		  '#attributes' => ['class' => ['btn btn-default']],
		  '#url' => \Drupal\Core\Url::fromRoute('company.view'),
		  '#prefix' => '&nbsp; &nbsp; &nbsp; &nbsp;',
		  '#suffix' => '</div></div>',
		];
	$form['company']['cancel']['#submit'][] = '::ActionCancel';

	$form['company']['#suffix'] = '</div></div></div></div>';

    return $form;


  }

  public function ActionCancel(array &$form, FormStateInterface $form_state)
  {

	$form_state->setRedirect('company.view');
  }


  public function validateForm(array &$form, FormStateInterface $form_state) {

	  if (trim($form_state->getValue('cname')) == '' ) {
        $form_state->setErrorByName('cname', $this->t('Enter your Organisation Name'));
      }
    else if(!preg_match("/^[A-Za-z]+((\s)?([A-Za-z])+)*$/", $form_state->getValue('cname'))) {
        $form_state->setErrorByName('cname', $this->t('Enter a valid Organisation Name'));
    }

	 if (trim($form_state->getValue('ctype')) == '' ) {
        $form_state->setErrorByName('ctype', $this->t('Enter your Organisation Type'));
      }

	if (trim($form_state->getValue('cemail')) == '' ) {
        $form_state->setErrorByName('cemail', $this->t('Enter Organisation Email Id'));
    }
    else if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $form_state->getValue('cemail'))) {
        $form_state->setErrorByName('cemail', $this->t('Enter a valid Email Id'));
    }

	if (trim($form_state->getValue('cphone')) == '' ) {
        $form_state->setErrorByName('cphone', $this->t('Enter Organisation phone number'));
    }
    else if(!preg_match("/^[6-9][0-9]{9}$/", $form_state->getValue('cphone'))) {
        $form_state->setErrorByName('cphone', $this->t('Enter a valid phone number'));
    }
	  if (strlen($form_state->getValue('cphone')) < 10) {
        $form_state->setErrorByName('cphone', $this->t('Mobile number is too short.'));
		echo "<div>errorrrr</div>";
      }

	 if (trim($form_state->getValue('caddress1')) == '' ) {
        $form_state->setErrorByName('caddress1', $this->t('Enter your Address '));
    }

	if (trim($form_state->getValue('pincode')) == '' ) {
        $form_state->setErrorByName('pincode', $this->t('Enter your pincode '));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $libobj = \Drupal::service('library.service');
	  $compobj = \Drupal::service('company.service');
	$encrypt = new \Drupal\library\Controller\Encrypt;

   $field = $form_state->getValues();

	 $data  = array(
              'companyname' =>  $field['cname'],
              'companycode' =>  $field['cname'],
              'companytype' =>  $field['ctype'],
              'email'		=>  $field['cemail'],
              'phone' 		=>  $field['cphone'],
              'address1' 	=>  $field['caddress1'],
              'address2' 	=>  $field['caddress2'],
              'state' 		=>  $field['state'],
              'city' 		=>  $field['city'],
              'country' 	=>  $field['country'],
              'pincode' 	=>  $field['pincode'],
          );

		$mode = $libobj->getActionMode();

		if($mode == 'add' )
		{
			$compobj->setCompany($data);
      \Drupal::messenger()->addMessage($data['companyname'] . " has succesfully created.");
		}
		if($mode == 'edit' )
		{
			$compobj->updateCompany($data);
      \Drupal::messenger()->addMessage($data['companyname'] . "  has succesfully Updated.");
		}

		/*
		* upload image int temp path 'temp-img' while choosing the image
		* once you submit move the image from temp-img to logo.png
		*/
		if(!empty($field['clogo'][0]) && isset($field['clogo'][0]))
		{
			$logo_file = file::load($field['clogo'][0]);
			$logo_path = \Drupal::service('file_system')->realpath($logo_file->getFileUri());
			$move_to_path = \Drupal::service('file_system')->realpath('public://logo.png');
			rename($logo_path, $move_to_path);

			//clearing only render cache to reflect logo immiditaely
			\Drupal::service('cache.render')->invalidateAll();
		}


		$form_state->setRedirect('company.view');
  }

  public function getCityList(array $form, FormStateInterface $form_state)
  {
	return $form['company']['city'];
  }
}
?>
