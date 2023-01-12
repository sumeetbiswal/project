<?php
/**
 * @file
 * Contains \Drupal\employee\Form\Multistep\EmployeeAcademicworkForm.
 */

namespace Drupal\employee\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
class EmployeeAcademicworkForm extends EmployeeFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'multistep_form_three';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

  if(!$this->store->get('contact_bypass'))
	{
		$response = new RedirectResponse(\Drupal::url('employee.empaddcontct'));
		$response->send();
	}
	if(!$this->store->get('official_back'))
	{
		parent::deleteAcademicStore();
	}

    $form = parent::buildForm($form, $form_state);
    $form['actions']['submit'] = array();

    $name_field = $form_state->get('num_names');
    $name_field_qual = $form_state->get('num_qual');
    $form['#tree'] = TRUE;
	$form['#attributes']['autocomplete'] = 'off';

	$form['#attached']['library'][] = 'singleportal/master-validation';
	$form['#attached']['library'][] = 'employee/emp-academic';

	$html = '<ul id="eliteregister">
									<li class="active">Personal Details</li>
									<li class="active">Contact Details</li>
									<li class="active">Academic Details</li>
									<li>Official Details</li>
									</ul><div class="row"><div class="panel-body"><h3 class="box-title">Academic Details</h3><hr class="m-t-0 m-b-40">';


	if (empty($name_field_qual)) {
      $name_field_qual = $form_state->set('num_qual', 1);
    }

	$form['quali']['actions'] = [
        '#type' => 'actions',
      ];
      $form['quali']['actions']['add_name_qual'] = [
        '#type' => 'submit',
		'#name' => 'qualification',
		'#prefix' => $html,
        '#value' => ' ',
		'#limit_validation_errors' => array(),
        '#submit' => array('::addOneQual'),
	    '#attributes' => ['class' => ['addmore']],
        '#ajax' => [
          'callback' => '::addmoreCallbackQual',
          'wrapper' => "qual-id",
        ],
      ];

	$form['qualification']['academics'] = [
      //'#prefix' => $html,
	  '#prefix' => '<div id="qual-id"><div class="panel-body">',
      '#suffix' => '</div></div>',
	  '#attributes' => ['class' => ['']],
	  '#type' => 'table',
	  '#title' => 'Sample Table',
	  '#header' => [ ['data' => 'SLNO', 'class' => 'text-center'],
					 ['data' => 'Class/Standard.', 'class' => 'text-center'],
					 ['data' => 'Stream/Branch', 'class' => 'text-center'],
					 ['data' => 'Board/University', 'class' => 'text-center'],
					 ['data' => 'Year of Passing', 'class' => 'text-center'],
					 ['data' => 'Score in %', 'class' => 'text-center'],
					 ['data' => 'Action', 'class' => 'text-left'],
					]
    ];



	if(!empty($this->store->get('qualification')))
	{
		$temp_stor = $this->store->get('qualification');
	}

	 for ($i = 0; $i <  $form_state->get('num_qual'); $i++) {
		$cntq = $i + 1;

	 $form['qualification']['academics'][$i]['slno'] = [
      '#type' 		   => 'item',
      //'#title' => t('Block title'),
	  '#markup' => $cntq,
      '#title_display' => 'invisible',
	 // '#attributes'    => ['class' => ['form-control']],

    ];

    $option = ['matric' => '10th / Matriculation', 'twelveth' => '12th / +2', 'graduation' => 'Graduation', 'diploma' => 'Diploma', 'postgraduation' => 'Post Graduation'];
	$form['qualification']['academics'][$i]['class'] = [
      '#type' 		   => 'select',
	  '#options'       => $option,
      '#title_display' => 'invisible',
	  '#attributes'    => ['class' => ['form-control']],
      '#default_value' => isset($temp_stor[$i]['class']) ? $temp_stor[$i]['class'] : '',


    ];

    $form['qualification']['academics'][$i]['stream'] = [
      '#type' 			=> 'textfield',
      '#title' 			=> $this->t('stream'),
      '#default_value' 	=> isset($temp_stor[$i]['stream']) ? $temp_stor[$i]['stream'] : '',
      '#title_display' => 'invisible',
	  '#attributes'    => ['class' => ['form-control']],

    ];
	 $form['qualification']['academics'][$i]['university'] = [
      '#type' 			=> 'textfield',
      '#title' 			=> $this->t('University'),
      '#default_value' 	=> isset($temp_stor[$i]['university']) ? $temp_stor[$i]['university'] : '',
     '#title_display' => 'invisible',
	  '#attributes'    => ['class' => ['form-control']],
    ];
	$form['qualification']['academics'][$i]['yearofpassing'] = [
      '#type' 			=> 'date',
      '#title' 			=> $this->t('Year Of passing'),
      '#default_value' 	=> isset($temp_stor[$i]['yearofpassing']) ? $temp_stor[$i]['yearofpassing'] : '',
      '#title_display' => 'invisible',
	  '#attributes'    => ['class' => ['form-control'], 'type' => 'date', 'max' => date('Y-m-d')],
    ];
	$form['qualification']['academics'][$i]['score'] = [
      '#type' 			=> 'textfield',
      '#title' 			=> $this->t('Score'),
      '#default_value' 	=> isset($temp_stor[$i]['score']) ? $temp_stor[$i]['score'] : '',
     '#title_display' => 'invisible',
	  '#attributes'    => ['class' => ['form-control']],
    ];

	if ($i ==  $form_state->get('num_qual') - 1) {
        $form['qualification']['academics'][$i]['actions']['remove_name_qual'] = [
          '#type' => 'submit',
		  '#name' => 'qualification_remove'.$i,
          '#value' => '.',
		  '#attributes' => ['class' => ['removeitem']],
		  '#limit_validation_errors' => array(),
          '#submit' => array('::removeCallbackQual'),
          '#ajax' => [
            'callback' => '::addmoreCallbackQual',
            'wrapper' => "qual-id",
			'progress' => [
					  'type' => 'throbber',
					  'message' => t(''),
					],
          ],
        ];
      }
    }




	/** starting for employement details **/


    if (empty($name_field)) {
      $name_field = $form_state->set('num_names', 1);
    }

	$form['empact']['actions'] = [
        '#type' => 'actions',
      ];
      $form['empact']['actions']['add_name'] = [
        '#type' => 'submit',
		'#name' => 'experience',
		'#prefix' => '<h3 class="box-title">Previous Employement</h3><hr class="m-t-0 m-b-40">',
        '#value' => ' ',
        '#submit' => array('::addOne'),
		'#limit_validation_errors' => array(),
	    '#attributes' => ['class' => ['addmore']],
        '#ajax' => [
          'callback' => '::addmoreCallback',
          'wrapper' => "ajax-id",
        ],
      ];

    $form['employee']['exp'] = [
      '#prefix' => '<div id="ajax-id"><div class="panel-body">',
      '#suffix' => '</div></div></div></div>',
	  '#attributes' => ['class' => ['']],
	  '#type' => 'table',
	  '#title' => 'Sample Table',
	  '#header' => [ ['data' => 'SLNO', 'class' => 'text-center'],
					 ['data' => 'Organisation', 'class' => 'text-center'],
					 ['data' => 'Designation', 'class' => 'text-center'],
					 ['data' => 'From Date', 'class' => 'text-center'],
					 ['data' => 'To Date', 'class' => 'text-center'],
					// ['data' => 'Duration', 'class' => 'text-center'],
					 ['data' => 'Action', 'class' => 'text-left']],
    ];

	 if(!empty($this->store->get('experience')))
	{
		$temp_stor_emp = $this->store->get('experience');
	}

    for ($i = 0; $i < $form_state->get('num_names'); $i++) {
		$cnt = $i + 1;
    $form['employee']['exp'][$i]['slno'] = [
      '#type' 		   => 'item',
      //'#title' => t('Block title'),
	  '#markup' => $cnt,
      '#title_display' => 'invisible',
	  '#attributes'    => ['class' => ['form-control']],

    ];

    $form['employee']['exp'][$i]['organisation'] = [
      '#type' 			=> 'textfield',
      '#title' 			=> $this->t('Organisation'),
      '#default_value' 	=>isset( $temp_stor_emp[$i]['organisation']) ? $temp_stor_emp[$i]['organisation'] : '',
     '#title_display' => 'invisible',
	  '#attributes'    => ['class' => ['form-control']],

    ];

	$form['employee']['exp'][$i]['designation'] = [
      '#type' 			=> 'textfield',
      '#title' 			=> $this->t('Designation'),
      '#default_value' 	=> isset($temp_stor_emp[$i]['designation']) ? $temp_stor_emp[$i]['designation'] : '',
     '#title_display' => 'invisible',
	  '#attributes'    => ['class' => ['form-control']],

    ];
	$form['employee']['exp'][$i]['fromdate'] = [
      '#type' 			=> 'date',
      '#title' 			=> $this->t('From Date'),
      '#default_value' 	=> isset($temp_stor_emp[$i]['fromdate']) ? $temp_stor_emp[$i]['fromdate'] : '',
     '#title_display' => 'invisible',
	  '#attributes'    => ['class' => ['form-control'], 'type' => 'date', 'max' => date('Y-m-d')],

    ];
	$form['employee']['exp'][$i]['todate'] = [
      '#type' 			=> 'date',
      '#title' 			=> $this->t('TO Date'),
      '#default_value' 	=> isset($temp_stor_emp[$i]['todate']) ? $temp_stor_emp[$i]['todate'] : '',
      '#title_display' => 'invisible',
	  '#attributes'    => ['class' => ['form-control'], 'type' => 'date', 'max' => date('Y-m-d') ],

    ];

	// $form['employee']['exp'][$i]['duration'] = [
      // '#type' 		   => 'item',
      //'#title' => t('Block title'),
	  // '#markup' => '1 Year 2 months',
      // '#title_display' => 'invisible',
	  // '#attributes'    => ['class' => ['form-control']],

    // ];

	if ($i == $form_state->get('num_names')-1) {
        $form['employee']['exp'][$i]['actions']['remove_name'] = [
          '#type' => 'submit',
		  '#name' => 'experience_remove'.$i,
          '#value' => '.',
		  '#attributes' => ['class' => ['removeitem']],
		  '#limit_validation_errors' => array(),
          '#submit' => array('::removeCallback'),
          '#ajax' => [
            'callback' => '::addmoreCallback',
            'wrapper' => "ajax-id",
			'progress' => [
					  'type' => 'throbber',
					  'message' => t(''),
					],
          ],
        ];
      }
    }



    $form_state->setCached(FALSE);

    $form['cancel'] = [
		  '#title' => $this->t('Back'),
		  '#type' => 'link',
		  '#attributes' => ['class' => ['btn btn-default']],
		  '#url' => \Drupal\Core\Url::fromRoute('employee.empaddcontct'),
		  '#prefix' => '<br/><div class="row"><div class="col-md-5"></div><div class="col-md-4">',
		];


	$form['#type'] = 'actions';
    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Next'),
      '#button_type' => 'primary',
	  '#attributes' => ['class' => ['btn btn-info']],
	  '#prefix' => '',
	  '#suffix' => '</div></div>',
		  );

    $this->store->set('academic_back', TRUE);
    $this->store->set('academic_bypass', 0);

    return $form;
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_names');
    return $form['employee'];
  }

  public function addmoreCallbackQual(array &$form, FormStateInterface $form_state) {
    $name_field_qual = $form_state->get('num_qual');
    return $form['qualification'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addOne(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_names');
    $add_button = $name_field + 1;
    $form_state->set('num_names', $add_button);
    $form_state->setRebuild();
  }

  public function addOneQual(array &$form, FormStateInterface $form_state) {
    $name_field_qual = $form_state->get('num_qual');
    $add_button_qual = $name_field_qual + 1;
    $form_state->set('num_qual', $add_button_qual);
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove one" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeCallback(array &$form, FormStateInterface $form_state) {
    $name_field = $form_state->get('num_names');

    if ($name_field > 1) {
      $remove_button = $name_field - 1;
      $form_state->set('num_names', $remove_button);

    }
    $form_state->setRebuild();
  }

  public function removeCallbackQual(array &$form, FormStateInterface $form_state) {
    $name_field_qual = $form_state->get('num_qual');

    if ($name_field_qual > 1) {
      $remove_button_qual = $name_field_qual - 1;
      $form_state->set('num_qual', $remove_button_qual);

    }
    $form_state->setRebuild();
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
	   //Validation start for Academics
	   foreach($form_state->getValue(array('qualification'))['academics'] as $key => $val)
	   {
		   if(is_numeric($key))
		   {
			    if (trim($val['class']) == '' ) {
					$form_state->setErrorByName('qualification][academics]['.$key.'][class', $this->t('Academics Details  Line no: '.($key+1).' &nbsp; Enter Class'));
				}
				if (trim($val['stream']) == '' ) {
					$form_state->setErrorByName('qualification][academics]['.$key.'][stream', $this->t('Academics Details  Line no: '.($key+1).' &nbsp; Enter Stream'));
				}
				if (trim($val['university']) == '' ) {
					$form_state->setErrorByName('qualification][academics]['.$key.'][university', $this->t('Academics Details  Line no: '.($key+1).' &nbsp; Enter university'));
				}
				if (trim($val['yearofpassing']) == '' ) {
					$form_state->setErrorByName('qualification][academics]['.$key.'][yearofpassing', $this->t('Academics Details  Line no: '.($key+1).' &nbsp; Enter year of passing'));
				}
				if (trim($val['yearofpassing']) > date('Y-m-d') ) {
					$form_state->setErrorByName('qualification][academics]['.$key.'][yearofpassing', $this->t('Academics Details  Line no: '.($key+1).' &nbsp; Passing year can not be set as future date'));
				}
				if (trim($val['score']) == '' ) {
					$form_state->setErrorByName('qualification][academics]['.$key.'][score', $this->t('Academics Details  Line no: '.($key+1).' &nbsp; Enter score'));
				}
		   }

	   }

	   //Validation start for Employement
	   foreach($form_state->getValue(array('employee'))['exp'] as $key => $val)
	   {
		   if(is_numeric($key))
		   {
			   /* if (trim($val['organisation']) == '' ) {
					$form_state->setErrorByName('employee][exp]['.$key.'][organisation', $this->t('Employement Details  Line no: '.($key+1).' &nbsp; Enter Organisation'));
				}
				if (trim($val['designation']) == '' ) {
					$form_state->setErrorByName('employee][exp]['.$key.'][designation', $this->t('Employement Details  Line no: '.($key+1).' &nbsp; Enter Designation'));
				}
				if (trim($val['fromdate']) == '' ) {
					$form_state->setErrorByName('employee][exp]['.$key.'][fromdate', $this->t('Employement Details  Line no: '.($key+1).' &nbsp; Enter Fromdate'));
				}
				if (trim($val['todate']) == '' ) {
					$form_state->setErrorByName('employee][exp]['.$key.'][todate', $this->t('Employement Details  Line no: '.($key+1).' &nbsp; Enter Todate'));
				}*/
				if (trim($val['fromdate']) >= date('Y-m-d') ) {
					$form_state->setErrorByName('employee][exp]['.$key.'][fromdate', $this->t('Employement Details  Line no: '.($key+1).' &nbsp; From date can not be set as today & future date'));
				}
				if (trim($val['todate']) > date('Y-m-d') ) {
					$form_state->setErrorByName('employee][exp]['.$key.'][todate', $this->t('Employement Details  Line no: '.($key+1).' &nbsp; To date can not be set as future date'));
				}
				if(trim($val['fromdate'])  > trim($val['todate'])){
					$form_state->setErrorByName('employee][exp]['.$key.'][fromdate', $this->t('Employement Details  Line no: '.($key+1).' &nbsp; From date is  greater than To Date'));
				}
		   }

	   }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->store->set('qualification', $form_state->getValues()['qualification']['academics']);

    $this->store->set('experience', $form_state->getValues()['employee']['exp']);


    $this->store->set('academic_bypass', TRUE);
    $form_state->setRedirect('employee.empaddoffcl');
  }
}
