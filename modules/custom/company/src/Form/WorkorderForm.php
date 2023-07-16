<?php

namespace Drupal\company\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

class WorkorderForm extends FormBase {
  public function getFormId() {
    return 'workorder_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

   // $libobj = new \Drupal\library\Lib\LibController;
   // $brnobj = new \Drupal\company\Model\DepartmentModel;
	//$conobj = new \Drupal\company\Model\ConfigurationModel;


    $brnobj = \Drupal::service('branch.service');
    $conobj = \Drupal::service('configuration.service');
    $libobj = \Drupal::service('library.service');

    $mode = $libobj->getActionMode();

	$form['#attached']['library'][] = 'singleportal/master-validation';
	$form['#attached']['library'][] = 'company/workorder-lib';
	$form['#attributes']['class'] = 'form-horizontal';
	$form['#attributes']['autocomplete'] = 'off';
    $form['department']['#prefix'] = '<div class="row"> <div class="panel panel-inverse"><h3 class="box-title">Work Order</h3>
                                      <hr/><div class="panel-body">';
    $form['workorder']['workname'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Work order Name:'),
      '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
      '#prefix'        => '<div class="row">',
      '#default_value' => isset($data)? $data->codevalues : '',
    );

	$workcode_config = $conobj->getWorkorderCodeConfig();
	$work_config = [];
	$work_config['disabled'] = '';
	$work_config['workordercode'] = '';
	$work_config['helpmsg'] = 'Mention Workorder Code of the person';

	if($workcode_config->codevalues == 'off')
	{
		$work_config['disabled'] = 'disabled';
		$work_config['branchcode'] = 'XXXXXXX';
		$work_config['helpmsg'] = 'Workorder Code will be auto generate';
	}

	 $form['workorder']['workcode'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Work order No:'),
      '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
	  '#suffix'        => '</div>',
	  '#default_value' => isset($data)? $data->codename : $work_config['workordercode'],
      '#disabled'      =>  $work_config['disabled'],
      '#default_value' => isset($data)? $data->codevalues : '',
	  '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="'.$work_config['helpmsg'].'" data-toggle="tooltip"></i>',
    );


	// repeater Field fro Team Form

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
		'#limit_validation_errors' => array(),
        '#submit' => array('::addOneTeam'),
	    '#attributes' => ['class' => ['addmore']],
        '#ajax' => [
          'callback' => '::addmoreCallbackTeam',
          'wrapper' => "team-id",
        ],
      ];

	$form['team']['teamorder'] = [
      //'#prefix' => $html,
	  '#prefix' => '<div id="team-id"><div class="panel-body">',
      '#suffix' => '</div></div>',
	  '#attributes' => ['class' => ['']],
	  '#type' => 'table',
	  '#title' => 'Sample Table',
	  '#header' => [ ['data' => 'SLNO', 'class' => 'text-center', 'width' => '1%'],
					 ['data' => 'Team Name', 'class' => 'text-center', 'width' => '13%'],
					 ['data' => 'Team Order No', 'class' => 'text-center', 'width' => '13%'],
					 ['data' => 'Action', 'class' => 'text-left', 'width' => '13%'],
					]
    ];




	 for ($i = 0; $i <  $form_state->get('num_team'); $i++) {
		$cntq = $i + 1;

	 $form['team']['teamorder'][$i]['slno'] = [
      '#type' 		   => 'item',
	  '#markup' => $cntq,
      '#title_display' => 'invisible',
    ];

    $form['team']['teamorder'][$i]['name'] = [
      '#type' 			=> 'textfield',
      '#title' 			=> $this->t('Team Name'),
      '#default_value' 	=> '',
      '#title_display' => 'invisible',
	  '#attributes'    => ['class' => ['form-control']],
	  '#prefix'	=> '',
    ];
	 $form['team']['teamorder'][$i]['order'] = [
      '#type' 			=> 'textfield',
      '#title' 			=> $this->t('Team Order No'),
      '#default_value' 	=> '',
     '#title_display' => 'invisible',
	  '#attributes'    => ['class' => ['form-control']],
    ];

	if ($i ==  $form_state->get('num_team') - 1) {
        $form['team']['teamorder'][$i]['actions']['remove_name_team'] = [
          '#type' => 'submit',
		  '#name' => 'qualification_remove'.$i,
          '#value' => '.',
		  '#attributes' => ['class' => ['removeitem']],
		  '#limit_validation_errors' => array(),
          '#submit' => array('::removeCallbackTeam'),
          '#ajax' => [
            'callback' => '::addmoreCallbackTeam',
            'wrapper' => "team-id",
			'progress' => [
					  'type' => 'throbber',
					  'message' => t(''),
					],
          ],
        ];
      }
    }


	// End of Repeater Field

	$form['save']['submit'] = array(
      '#type'          => 'submit',
      '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
      '#button_type'   => 'primary',
      '#attributes'    => ['class' => ['btn btn-info']],
      '#prefix'        => '<br/><div class="row"><div class="col-md-2"></div><div class="col-md-4">',
      '#suffix'        => '',
    );

    $form['save']['cancel'] = array(
      '#type' => 'link',
	  '#title' => t('Cancel'),
      '#attributes'               => ['class'   => ['btn btn-default']],
      //'#limit_validation_errors'  => array(),
      '#suffix'                   => '</div></div>',
      '#url' => \Drupal\Core\Url::fromRoute('company.projectlist'),
    );
	$form_state->setCached(FALSE);
    return $form;

    }

  public function validateForm(array &$form, FormStateInterface $form_state) {

  }


  public function submitForm(array &$form, FormStateInterface $form_state) {

		//$worobj = new \Drupal\company\Model\WorkorderModel;
    //    $conobj = new \Drupal\company\Model\ConfigurationModel;
		//$libobj = new \Drupal\library\Lib\LibController;

    $worobj = \Drupal::service('workorder.service');
    $conobj = \Drupal::service('configuration.service');
    $libobj = \Drupal::service('library.service');


		$field = $form_state->getValues();
		$code_config = $conobj->getWorkorderCodeConfig();

		//check codevalues OFF then auto generate the code values
	    $code = ( $code_config->codevalues == 'on' ) ? $field['workcode'] : $libobj->generateCode('WR', $field['workname']) ;

		$data = array(
						'workorder' => array(
												'codename'	=>	$code,
												'codevalues'=>	$field['workname']
											),
						'teamorder'	=>	array()
					);

		//looping team repeater array and collecting data
		foreach( $field['teamorder'] AS $team )
		{
			$data['teamorder'][]	=	array(
												'codename' 	=>	$team['order'],
												'codevalues'=>	$team['name']
											);
		}

		$worobj->setWorkOrder( $data );

    \Drupal::messenger()->addMessage("Word order has been created.");

		$form_state->setRedirect('company.projectlist');
	}


 public function addmoreCallbackTeam(array &$form, FormStateInterface $form_state) {
    $team_field_count = $form_state->get('num_team');
    return $form['team'];
  }

  public function addOneTeam(array &$form, FormStateInterface $form_state) {
    $team_field_count = $form_state->get('num_team');
    $add_button_team = $team_field_count + 1;
    $form_state->set('num_team', $add_button_team);
    $form_state->setRebuild();
  }

   public function removeCallbackTeam(array &$form, FormStateInterface $form_state) {
    $team_field_count = $form_state->get('num_team');

    if ($team_field_count > 1) {
      $remove_button_team = $team_field_count - 1;
      $form_state->set('num_team', $remove_button_team);

    }
    $form_state->setRebuild();
  }



}
?>
