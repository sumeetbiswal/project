<?php
/**
 * @file
 * Contains \Drupal\employee\Form\Multistep\EmployeeOfficialForm
 */

namespace Drupal\employee\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;


class EmployeeOfficialForm extends EmployeeFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep-form-four';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

	 global $base_url;

      if(!$this->store->get('academic_bypass'))
      {
        $response = new RedirectResponse(\Drupal::url('employee.empaddacademic'));
        $response->send();
      }
      if(!$this->store->get('preview_back'))
	  {
		parent::deleteOfficialStore();
	  }

    $form = parent::buildForm($form, $form_state);
    $form['actions']['submit'] = array();
    $desobj = \Drupal::service('designation.service');
    $depobj = \Drupal::service('department.service');
    $brnobj = \Drupal::service('branch.service');
    $libobj = \Drupal::service('library.service');
    $conobj = \Drupal::service('configuration.service');

    $form['employee']['#prefix'] = '<ul id="eliteregister">
                                    <li class="active">Personal Details</li>
									<li class="active">Contact Details</li>
									<li class="active">Academic Details</li>
									<li class="active">Official Details</li>
                                    </ul><div class="row"><div class="panel-body"><h3 class="box-title">Official</h3>
                                    <hr class="m-t-0 m-b-40">';
	$form['employee']['#suffix'] = '</div></div>';
	$form['#attached']['library'][] = 'singleportal/master-validation';
 	$form['#attached']['library'][] = 'singleportal/datetimepicker';

  $empid_config = $conobj->getEmployeeIdConfig();

  $form['employee']['id'] = array(
    '#type'          => 'textfield',
    '#title'         => $this->t('Employee ID'),
    '#default_value' => $this->store->get('id') ? $this->store->get('id') : '',
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
	'#disabled'      => $empid_config['disabled'],
    '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="'.$empid_config['helpmsg'].'" data-toggle="tooltip"></i>',

  );
  if(!empty($empid_config['id']))
  {
	  $form['employee']['id']['#value'] = $empid_config['empid'];
  }

  $brnlist = $brnobj->getAllBranchDetails();
  $brn_option[''] = 'Select Branch';
  foreach($brnlist AS $item)
   {
    $brn_option[$item->codename]  = $item->codevalues;
   }

  $form['employee']['branch'] = array(
    '#type'          => 'select',
    '#title'         => $this->t('Branch'),
    '#default_value' => $this->store->get('branch') ? $this->store->get('branch') : '',
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
    '#options'       => $brn_option,
  );

  $deplist = $depobj->getDepartmentList();
  $form['employee']['department'] = array(
    '#type'          => 'select',
    '#title'         => $this->t('Department'),
    '#default_value' => $this->store->get('department') ? $this->store->get('department') : '',
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
	'#options' => $deplist,
    '#field_suffix' => '<a href="'.$base_url.'/department/modal" class="use-ajax button"><i class="fadehide mdi mdi-settings fa-fw"></i></a>',
    '#ajax' => [
					'callback' => '::getList',
					'wrapper' => 'desgn_list',
					'event' => 'change',
					'progress' => [
					  'type' => 'throbber',
					  //'message' => t(''),
					],
				  ],
  );

	if (!empty($form_state->getValue('department'))) {
      $department = $form_state->getValue('department');
    }
	else{
		$department = isset($data)? $data->department : '';
	}

	$dsgn = [];
	$dsgn = $desobj->getDesignationList($department);

 $form['employee']['designation'] = array(
    '#type'          => 'select',
    '#title'         => $this->t('Designation'),
    '#default_value' => $this->store->get('designation') ? $this->store->get('designation') : '',
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
    '#options'       => $dsgn,
    '#prefix'        => '<div id="desgn_list">',
    '#suffix'        => '</div>',

  );

  $rolelist = $libobj->getRoles();
  $form['employee']['role'] = array(
    '#type'          => 'select',
    '#title'         => $this->t('Role'),
    '#default_value' => $this->store->get('role') ? $this->store->get('role') : '',
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
    '#options'       => $rolelist,
    '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Provide Role for employee. Default role will be Authenticated if no special role is given." data-toggle="tooltip"></i>',

  );

  $natureofjob = $conobj->getJobNature();

  $form['employee']['jobnature'] = array(
    '#type'          => 'select',
    '#title'         => $this->t('Nature of job'),
    '#default_value' => $this->store->get('jobnature') ? $this->store->get('jobnature') : '',
    '#options'       => $natureofjob,
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
    );
  $form['employee']['officialemail'] = array(
    '#type'          => 'textfield',
    '#title'         => $this->t('Email'),
    '#default_value' => $this->store->get('officialemail') ? $this->store->get('officialemail') : '',
    '#attributes'    => ['class' => ['form-control', 'validate[required,custom[email]]']],
    '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Your Official Email ID" data-toggle="tooltip"></i>',
  );
  $form['employee']['doj'] = array(
    '#type'          => 'textfield',
    '#title'         => $this->t('Date of Joining'),
    '#default_value' => $this->store->get('doj') ? $this->store->get('doj') : '',
	  '#attributes'    => ['id' => ['datetimepicker'], 'class' => ['form-control', 'validate[required]']],

  );

  $jobtype = $conobj->getJobType();

  $form['employee']['jobtype'] = array(
    '#type'          => 'select',
    '#title'         => $this->t('Job type'),
    '#options'       => $jobtype,
    '#default_value' => $this->store->get('jobtype') ? $this->store->get('jobtype') : '',
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
  );

  $jobshift = $conobj->getJobShift();

  $form['employee']['shifttime'] = array(
    '#type'          => 'select',
    '#title'         => $this->t('Shift time'),
    '#options'       => $jobshift,
    '#default_value' => $this->store->get('jobtype') ? $this->store->get('jobtype') : '',
    '#attributes'    => ['class' => ['form-control', 'validate[required]']],
    '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Helps to identify working hours" data-toggle="tooltip"></i>',

  );

  $form['employee']['cancel'] = [
		  '#title' => $this->t('Back'),
		  '#type' => 'link',
		  '#attributes' => ['class' => ['btn btn-default']],
		  '#url' => \Drupal\Core\Url::fromRoute('employee.empaddacademic'),
		  '#prefix' => '<br/><div class="row"><div class="col-md-5"></div><div class="col-md-4">',
		];

	$form['employee']['#type'] = 'actions';
    $form['employee']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#attributes' => ['class' => ['btn btn-info']],
      '#prefix' => '',
      '#suffix' => '</div></div>',
		);
	$this->store->set('official_back', TRUE);
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

	  $empObj = \Drupal::service('employee.service');

	  $checkEmpIdDuplicacy = $empObj->checkUserIdExist(trim($form_state->getValue('id')));
	  $checkEmailDuplicacy = $empObj->checkEMailIdExist(trim($form_state->getValue('officialemail')));

	if (trim($form_state->getValue('id')) == '' ) {
        $form_state->setErrorByName('id', $this->t('Enter your Employee id'));
    }
	else if(!empty($checkEmpIdDuplicacy))
	{
		$form_state->setErrorByName('id', $this->t('Employee id already Exist'));
	}
    if (trim($form_state->getValue('department')) == '' ) {
        $form_state->setErrorByName('department', $this->t('Enter your Department'));
    }
    if (trim($form_state->getValue('branch')) == '' ) {
        $form_state->setErrorByName('branch', $this->t('Enter your Branch'));
    }
    if (trim($form_state->getValue('designation')) == '' ) {
        $form_state->setErrorByName('designation', $this->t('Enter your Designation'));
    }
    if (trim($form_state->getValue('role')) == '' ) {
        $form_state->setErrorByName('role', $this->t('Enter your Role'));
    }
    if (trim($form_state->getValue('jobnature')) == '' ) {
        $form_state->setErrorByName('jobnature', $this->t('Enter your Nature of job'));
    }
    if (trim($form_state->getValue('officialemail')) == '' ) {
        $form_state->setErrorByName('officialemail', $this->t('Enter your Email Id'));
    }
    else if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $form_state->getValue('officialemail'))) {
        $form_state->setErrorByName('officialemail', $this->t('Enter your valid Email Id'));
    }
	else if(!empty($checkEmailDuplicacy))
	{
		$form_state->setErrorByName('officialemail', $this->t('Email id is already Exist'));
	}
    if (trim($form_state->getValue('doj')) == '' ) {
        $form_state->setErrorByName('doj', $this->t('Enter your Date of joining'));
    }
    if (trim($form_state->getValue('jobtype')) == '' ) {
        $form_state->setErrorByName('jobtype', $this->t('Enter your job type'));
    }
    if (trim($form_state->getValue('shifttime')) == '' ) {
        $form_state->setErrorByName('shifttime', $this->t('Enter your shift time'));
    }


  }
  /**
   * {@inheritdoc}
   */

  public function submitForm(array &$form, FormStateInterface $form_state) {

	 $field_list = array('id', 'department', 'branch', 'designation', 'role', 'jobnature',
                         'officialemail', 'doj', 'jobtype', 'shifttime');

    foreach($field_list AS $val)
     {
			$this->store->set($val, $form_state->getValue($val));
     }


    $form_state->setRedirect('employee.preview');
  }

  public function getList(array $form, FormStateInterface $form_state){
	  return $form['employee']['designation'];
  }
}
