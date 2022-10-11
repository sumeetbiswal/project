<?php
/**
 * @file
 * Contains \Drupal\employee\Form\Multistep\EmployeeContactForm.
 */

namespace Drupal\employee\Form\Multistep;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

class EmployeeContactForm extends EmployeeFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep-form-two';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	
	if(!$this->store->get('personal_bypass'))
	{	
		$response = new RedirectResponse(\Drupal::url('employee.empaddprsnl'));
		$response->send();
	}
	if(!$this->store->get('academic_back'))
	{
		parent::deleteContactStore();
		parent::deleteAcademicStore();
	}
	
    $form = parent::buildForm($form, $form_state);
    $libobj = new \Drupal\library\Lib\LibController;

		$form['#attributes']['autocomplete'] = 'off';

    $form['actions']['submit'] = array();
		$form['#attached']['library'][] = 'singleportal/master-validation';
		$form['#attached']['library'][] = 'employee/emp-contact';

	$form['employee']['#prefix'] = '<ul id="eliteregister">
									<li class="active">Personal Details</li>
									<li class="active">Contact Details</li>
									<li>Academic Details</li>
									<li>Official Details</li>
									</ul><div class="row"><div class="panel-body"><h3 class="box-title">Contact Details</h3><hr class="m-t-0 m-b-40">';
	$form['employee']['phoneno'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Phone number:'),
	  '#attributes'    => ['class' => ['form-control', 'validate[required][custom[phone]]']],
	  '#default_value' => $this->store->get('phoneno') ? $this->store->get('phoneno') : '',
	  '#prefix'        => '<div class="row">',
    );    
  $form['employee']['altphoneno'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Alternative phone number'),
	'#attributes'    => ['class' => ['form-control', 'validate[required][custom[phone]]']],
    '#default_value' => $this->store->get('altphoneno') ? $this->store->get('altphoneno') : '',
	'#suffix'        => '</div>',
  ); 
  $form['employee']['emergencyno'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Emergency Contact:'),
	  '#attributes'    => ['class' => ['form-control', 'validate[required][custom[phone]]']],
   '#default_value' => $this->store->get('emergencyno') ? $this->store->get('emergencyno') : '',
   '#prefix'        => '<div class="row">',
    );    
  $form['employee']['relationship'] = array(
    '#type'          => 'select',
    '#title'         => t('Relationship'),
    '#options'       => [
						  'Father' => $this->t('Father'),
						  'Mother'       => $this->t('Mother'),
						  'Husband'       => $this->t('Husband'),
						  'Wife'       => $this->t('Wife'),
						  'Sibling'       => $this->t('Sibling'),
						],
   '#attributes'    => ['class' => ['form-control', 'validate[required]']],
   '#default_value' => $this->store->get('relationship') ? $this->store->get('relationship') : '',
   '#field_suffix' => '<i class="mdi mdi-help-circle fadehide" title="Relationship with emergency contact person" data-toggle="tooltip"></i>',
   '#suffix'        => '</div>',
  );  
  $form['employee']['email'] = array(
    '#type'          => 'textfield',
    '#title'         => $this->t('Email'),
    '#default_value' => $this->store->get('email') ? $this->store->get('email') : '',	
    '#attributes'    => ['class' => ['form-control', 'validate[required,custom[email]]']],
    '#field_suffix' => '<i class="mdi mdi-help-circle fadehide" title="Your Personal Email ID" data-toggle="tooltip"></i>',
    '#prefix'        => '<div class="row">',  
  );
  	$form['employee']['image'] = array(
    '#type' 		=> 'managed_file',
	'#name' 		=> 'avatar',
    '#title' 		=> t('Profile Picture:'),
	'#description' 	=> t('Upload your profile picture jpg/jpeg/png only'),
	'#upload_validators'=> array('jpg', 'jpeg', 'png'),
	'#upload_location' => 'public://avatar/',
	'#attributes' 	=> ['class' => ['form-control']],
    '#default_value'=> $this->store->get('image') ? array($this->store->get('image')) : '',
	'#suffix'        => '</div>',
    );
  $form['employee']['label'] = array(
    '#type'          => 'title',
    '#title'         => t('Present Address'),
    '#prefix'        => '<hr><div class="panel-body"><h3 class="box-title m-t-40">Present Address<hr><div class="row">',
    '#suffix'        =>  '</h3></div>',
  );      
  $form['employee']['address1'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Address Line 1'),
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
    '#default_value' => $this->store->get('address1') ? $this->store->get('address1') : '',
	'#suffix'        => '</div>',
  );    
  
  $form['employee']['address2'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Address Line 2:'),
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
    '#default_value' => $this->store->get('address2') ? $this->store->get('address2') : '',
	'#prefix'        => '<div class="row">', 
  );    
    
  $statelist = $libobj->getStateList();

	$form['employee']['state'] = array(
		'#type'          => 'select',
		'#title'         => t('State:'),
		'#options' => $statelist,
        '#attributes'    => ['class' => ['form-control', 'validate[required]']],
		'#suffix'        => '</div>',
		'#default_value' => $this->store->get('state') ? $this->store->get('state') : '',
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
		$statePk = $this->store->get('state') ? $this->store->get('state') : '';
	}
	
	$cityLst = [];
	$cityLst = $libobj->getCityListByState($statePk);
  
  $form['employee']['city'] = array(
    '#type'          => 'select',
    '#title'         => t('City:'),
    '#options'       => $cityLst,
    '#attributes'    => ['class' => ['form-control']],
    '#prefix'        => '<div id="citylist">',
    '#suffix'        => '</div>',
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
    '#default_value' => $this->store->get('city') ? $this->store->get('city') : '',
	'#validated' => TRUE
  );
   
	
	  $form['employee']['country'] = array(
		'#type'          => 'select',
		'#title'         => t('Country:'),
		'#options' => [
			'101' => $this->t('India'),
		],
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
    '#default_value' => $this->store->get('country') ? $this->store->get('country') : '',
    );
	  	
  $form['employee']['pincode'] = array(
    '#type'          => 'textfield',
    '#title'         => t('Pincode'),
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
       '#default_value' => $this->store->get('pincode') ? $this->store->get('pincode') : '',
  );  
  $form['employee']['addresscopy'] = array(
    '#type'          => 'checkbox',
    '#title'         => t('Present address same as Permanent address'),
    '#attributes'    => ['class' => ['form-group']],
    '#prefix'        =>  '<div class="col-md-12 form-group">
                          <div class="checkbox checkbox-success">',
    '#suffix'        =>  '</div></div>',
   '#default_value'  => $this->store->get('addresscopy') ? $this->store->get('addresscopy') : '',
  ); 
   

   $form['employee']['permanentlabel2'] = array(
		'#type'          => 'title',
		'#title'         => t('Permanent Address'),
		'#prefix'    => '<hr><div id="permanentAddress"><div class="panel-body"><h3 class="box-title m-t-40">Permanent Address<hr>',
		'#suffix'        =>  '</h3></div>',
		
	  );
		  
	  
	  if (!empty($form_state->getValue('address1'))) {
			$ad = $form_state->getValue('address1');
		}
		else{
	  
			$ad = $form_state->getValue('permanentaddress1') ? $form_state->getValue('permanentaddress1') : '';
		}
		
		
	  $form['employee']['permanentaddress1'] = array(
		'#type'          => 'textfield',
		'#title'         => t('Address Line 1'),
		'#attributes'    => ['class' => ['form-control', 'validate[required]']],
		'#value'        =>  isset($ad)? $ad : '',
		'#default_value' => $this->store->get('permanentaddress1') ? $this->store->get('permanentaddress1') : '', 
	  );    
	  
	  $form['employee']['permanentaddress2'] = array(
		'#type'          => 'textfield',
		'#title'         => t('Address Line 2:'),
		'#attributes'    => ['class' => ['form-control', 'validate[required]']],
		'#default_value' => $this->store->get('permanentaddress2') ? $this->store->get('permanentaddress2') : '',
	  ); 
			$statelist = $libobj->getStateList();

		$form['employee']['permanentstate'] = array(
			'#type'          => 'select',
			'#title'         => t('State:'),
			'#options' => $statelist,
		'#ajax' => [  'callback' => '::getPermCityList',
					  'wrapper' => 'permcitylist',
					  'event' => 'change',
					  'progress' => [
							'type' => 'throbber',
							'message' => t(''),
						],
					  ],
		'#attributes'    => ['class' => ['form-control', 'validate[required]']],
			'#default_value' => isset($permanentstate)? $data->permanentstate : $form_state->getValue('permanentstate'),
		);  
		
		
		if (!empty($form_state->getValue('permanentstate'))) {
			$statePk = $form_state->getValue('permanentstate');
		}
		else{
			$statePk = isset($data)? $data->permanentstate : '';
		}
		
		$cityLst = [];
		$cityLst = $libobj->getCityListByState($statePk);
	  
	  
		$form['employee']['permanentcity'] = array(
		  '#type'          => 'select',
		  '#title'         => t('City:'),
		  '#options'       => $cityLst,
		  '#prefix'        => '<div id="permcitylist">',
		'#suffix'        => '</div>',
		'#attributes'    => ['class' => ['form-control', 'validate[required]']],
		  '#default_value' => $this->store->get('permanentcity') ? $this->store->get('permanentcity') : '',
		);

		  $form['employee']['permanentcountry'] = array(
			'#type'          => 'select',
			'#title'         => t('Country:'),
			'#options' => [
				'101' => $this->t('India'),
			],
		'#attributes'    => ['class' => ['form-control', 'validate[required]']],
		   '#default_value' => $this->store->get('permanentcountry') ? $this->store->get('permanentcountry') : '',
		);
			
	  $form['employee']['permanentpincode'] = array(
		'#type'          => 'textfield',
		'#title'         => t('Pincode'),
		'#attributes'    => ['class' => ['form-control', 'validate[required]']],
		   '#default_value' => $this->store->get('permanentpincode') ? $this->store->get('permanentpincode') : '',
		'#suffix'        => '</div>',

	  );  

	$form['employee']['cancel'] = [
		  '#title' => $this->t('Back'),
		  '#type' => 'link',
		  '#attributes' => ['class' => ['btn btn-default']],
		  '#url' => \Drupal\Core\Url::fromRoute('employee.empaddprsnl'),
		  '#prefix' => '<br/><div class="row"><div class="col-md-5"></div><div class="col-md-4">',
		];
	
	
	$form['employee']['#type'] = 'actions';
    $form['employee']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Next'),
      '#button_type' => 'primary',
	  '#attributes' => ['class' => ['btn btn-info']],
	  '#prefix' => '',
	  '#suffix' => '</div></div>',
		  );
	
	$this->store->set('academic_back', FALSE);
	$this->store->set('contact_back', TRUE);
	$this->store->set('contact_bypass', 0);
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
	
	  if (trim($form_state->getValue('phoneno')) == '' ) {
        $form_state->setErrorByName('phoneno', $this->t('Enter your phone number'));
    }
    else if(!preg_match("/^[6-9][0-9]{9}$/", $form_state->getValue('phoneno'))) {
        $form_state->setErrorByName('phoneno', $this->t('Enter your valid phone number')); 
    }
    if (trim($form_state->getValue('altphoneno')) == '' ) {
        $form_state->setErrorByName('altphoneno', $this->t('Enter your Alternate phone number'));
    }
    else if(!preg_match("/^[6-9][0-9]{9}$/", $form_state->getValue('altphoneno'))) {
        $form_state->setErrorByName('altphoneno', $this->t('Enter your valid Alternate phone number')); 
    }
    if (trim($form_state->getValue('emergencyno')) == '' ) {
        $form_state->setErrorByName('emergencyno', $this->t('Enter your Emergency phone number'));
    }
    else if(!preg_match("/^[6-9][0-9]{9}$/", $form_state->getValue('emergencyno'))) {
        $form_state->setErrorByName('emergencyno', $this->t('Enter your valid Emergency phone number')); 
    }
	if (trim($form_state->getValue('phoneno')) == trim($form_state->getValue('altphoneno'))) {
		$form_state->setErrorByName('altphoneno', $this->t('Enter a different number'));
    }
	else if (trim($form_state->getValue('altphoneno')) == trim($form_state->getValue('emergencyno'))) {
		$form_state->setErrorByName('emergencyno', $this->t('Enter a different number'));
	}
	else if (trim($form_state->getValue('phoneno')) == trim($form_state->getValue('emergencyno'))) {
		$form_state->setErrorByName('emergencyno', $this->t('Enter a different number!'));
    }
    if (trim($form_state->getValue('email')) == '' ) {
        $form_state->setErrorByName('email', $this->t('Enter your Email Id'));
    }
    else if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $form_state->getValue('email'))) {
        $form_state->setErrorByName('email', $this->t('Enter your valid Email Id')); 
    }    
    if (trim($form_state->getValue('address1')) == '' ) {
        $form_state->setErrorByName('address1', $this->t('Enter your Address '));
    }
    if (trim($form_state->getValue('state')) == '' ) {
        $form_state->setErrorByName('state', $this->t('Enter your state '));
    }
    if (trim($form_state->getValue('city')) == '' ) {
        $form_state->setErrorByName('city', $this->t('Enter your city '));
    }
    if (trim($form_state->getValue('country')) == '' ) {
        $form_state->setErrorByName('country', $this->t('Enter your country '));
    }
    if (trim($form_state->getValue('pincode')) == '' ) {
        $form_state->setErrorByName('pincode', $this->t('Enter your pincode '));
    }     
    else if(!preg_match("/^[1-9][0-9]{5}$/", $form_state->getValue('pincode'))) {
        $form_state->setErrorByName('pincode', $this->t('Enter your valid pincode')); 
    }
    
    
	
	if(!$form_state->getValue('addresscopy'))
    {
		if (trim($form_state->getValue('permanentaddress1')) == '' ) {
			$form_state->setErrorByName('permanentaddress1', $this->t('Enter your Permanent address '));
		}
		if (trim($form_state->getValue('permanentstate')) == '' ) {
			$form_state->setErrorByName('permanentstate', $this->t('Enter your Permanent state '));
		}
		if (trim($form_state->getValue('permanentcity')) == '' ) {
			$form_state->setErrorByName('permanentcity', $this->t('Enter your permanent city '));
		}
		if (trim($form_state->getValue('permanentcountry')) == '' ) {
			$form_state->setErrorByName('permanentcountry', $this->t('Enter your permanent country '));
		}
		if (trim($form_state->getValue('permanentpincode')) == '' ) {
			$form_state->setErrorByName('permanentpincode', $this->t('Enter your permanent pincode '));
		}     
		else if(!preg_match("/^[1-9][0-9]{5}$/", $form_state->getValue('permanentpincode'))) {
			$form_state->setErrorByName('permanentpincode', $this->t('Enter your valid permanent pincode')); 
		}
    }
  } 

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
	 
	 $field_list = array('phoneno', 'altphoneno', 'emergencyno', 'relationship', 'email',
						'address1', 'address2', 'state', 'city', 'country', 'pincode', 'addresscopy',
						'permanentaddress1', 'permanentaddress2', );
	 
	 foreach($field_list AS $val)
	 {
			$this->store->set($val, $form_state->getValue($val));
	 }
	 
	 if(isset($form_state->getValue('image')[0]))
	 { 
		$this->store->set('image',$form_state->getValue('image')[0]);
	 }
	
	if(!$this->store->get('addresscopy'))
    {
		$this->store->set('permanentaddress1', $form_state->getValue('permanentaddress1'));
		$this->store->set('permanentaddress2', $form_state->getValue('permanentaddress2'));
		
		$this->store->set('permanentstate', $form_state->getValue('permanentstate'));
		$this->store->set('permanentcity', $form_state->getValue('permanentcity'));
		
		$this->store->set('permanentcountry', $form_state->getValue('permanentcountry'));
		$this->store->set('permanentpincode', $form_state->getValue('permanentpincode'));
	}
	else{
		$this->store->set('permanentaddress1', $form_state->getValue('address1'));
		$this->store->set('permanentaddress2', $form_state->getValue('address2'));
		
		$this->store->set('permanentstate', $form_state->getValue('state'));
		$this->store->set('permanentcity', $form_state->getValue('city'));
		
		$this->store->set('permanentcountry', $form_state->getValue('country'));
		$this->store->set('permanentpincode', $form_state->getValue('pincode'));
	}
  	$this->store->set('contact_bypass', TRUE);
	
    $form_state->setRedirect('employee.empaddacademic');
   
  }
  
  public function getCityList(array $form, FormStateInterface $form_state)
  {
	return $form['employee']['city'];
  }
  public function getPermCityList(array $form, FormStateInterface $form_state)
  {
	return $form['employee']['permanentcity'];
  }
  
}
