<?php
/**
 * @file
 * Contains \Drupal\company\Form\ConfigurationForm.
 */

namespace Drupal\company\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\library\Lib\LibController;

class ConfigurationForm extends FormBase {

  public function getFormId() {
	return 'configuration_form';

  }
  
public function buildForm(array $form, FormStateInterface $form_state) {
	global $base_url;
	$user = \Drupal::currentUser();
	
	$configobj = new \Drupal\company\Model\ConfigurationModel;
	
	$form['company']['#attributes']['enctype'] = "multipart/form-data";
	$form['#attached']['library'][] = 'singleportal/bootstrap-toggle';
	$form['company']['#prefix'] = ' <div class="row">
									
				<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active">
					<a href="'.$base_url.'/organisation/config" role="tab"  ><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> <b>General</b></span></a>
				</li>
				<li role="presentation" class="">
					<a href="'.$base_url.'/organisation/config/shift"  role="tab"  ><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs"><b>Timing</b></span></a>
				</li>
				</ul><br/><br/>';
	$form['company']['#suffix'] = '</div>';
	
	
	$data = $configobj->getEmpIdType();
	
	$form['company']['empidtype'] = array(
      '#type' => 'checkbox',
      '#title' => t('Auto EmpID'),
      //'#required' => TRUE,
 	  '#attributes' => ['class' => ['form-control'], 'data-toggle' => 'toggle', 
								'data-on' => 'ON', 'data-off' => 'OFF', 
								'data-onstyle' => 'info'],
	 '#prefix' => '<div class="row">',
	 '#default_value' => !empty($data)? ($data->codevalues == 'Automatic')? 1 : 0 : '',
	 '#disabled' => ($user->hasPermission('admin configuration')) ? false : true,
     '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="The code which is being used for employee ID generation. For EX:- If your code is ABC then Employee ID will be ABC001, ABC021, ABC0156" data-toggle="tooltip"></i>',
    );
	
	$form['company']['codeformat'] = array(
      '#type' => 'textfield',
      '#title' => t('Code Format:'),
	  '#attributes' => ['class' => ['form-control']],
	  '#states' => [
        'visible' => [
          ':input[name="empidtype"]' => [ 'checked' => TRUE,],
        ],
      ],
	 //'#prefix' => '<div class="row">',
	 '#suffix' => '</div>',
	 '#default_value' => !empty($data)? $data->description : '',
	 '#disabled' => ($user->hasPermission('admin configuration')) ? false : true,
    );
	
	$brch = $configobj->getBranchCodeConfig();
	$form['company']['Branchcode'] = array(
      '#type' => 'checkbox',
      '#title' => t('Branch Code'),
      //'#required' => TRUE,
 	  '#attributes' => ['class' => ['form-control'], 'data-toggle' => 'toggle', 
								'data-on' => 'ON', 'data-off' => 'OFF', 
								'data-onstyle' => 'info'],
	 '#prefix' => '<div class="row">',
	 '#default_value' => !empty($brch)? ($brch->codevalues == 'on')? 1 : 0 : '',
	 '#disabled' => ($user->hasPermission('admin configuration')) ? false : true,
     '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Make it ON If you need to enter your branch code" data-toggle="tooltip"></i>',
    );
	
	$dept = $configobj->getDepartmentCodeConfig();
	$form['company']['Departmentcode'] = array(
      '#type' => 'checkbox',
      '#title' => t('Department Code'),
      //'#required' => TRUE,
 	  '#attributes' => ['class' => ['form-control'], 'data-toggle' => 'toggle', 
								'data-on' => 'ON', 'data-off' => 'OFF', 
								'data-onstyle' => 'info'],
	 '#default_value' => !empty($dept)? ($dept->codevalues == 'on')? 1 : 0 : '',
	 '#disabled' => ($user->hasPermission('admin configuration')) ? false : true,
     '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Make it ON If you need to enter your Department code" data-toggle="tooltip"></i>',
     '#suffix' => '</div>'
    );
	
	$desg = $configobj->getDesignationCodeConfig();

	$form['company']['Designationcode'] = array(
      '#type' => 'checkbox',
      '#title' => t('Designation Code'),
      //'#required' => TRUE,
 	  '#attributes' => ['class' => ['form-control'], 'data-toggle' => 'toggle', 
								'data-on' => 'ON', 'data-off' => 'OFF', 
								'data-onstyle' => 'info'],
	 '#prefix' => '<div class="row">',
	 '#default_value' => !empty($desg)? ($desg->codevalues == 'on')? 1 : 0 : '',
	 '#disabled' => ($user->hasPermission('admin configuration')) ? false : true,
     '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Make it ON If you need to enter your Designation code" data-toggle="tooltip"></i>',
    );
	
	$wrkord_conf = $configobj->getWorkorderCodeConfig();
	
	$form['company']['Workordercode'] = array(
      '#type' => 'checkbox',
      '#title' => t('Workorder code'),
      //'#required' => TRUE,
 	  '#attributes' => ['class' => ['form-control'], 'data-toggle' => 'toggle', 
								'data-on' => 'ON', 'data-off' => 'OFF', 
								'data-onstyle' => 'info'],
	 '#default_value' => !empty($wrkord_conf)? ($wrkord_conf->codevalues == 'on')? 1 : 0 : '',
	 '#disabled' => ($user->hasPermission('admin configuration')) ? false : true,
     '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Make it ON If you need to enter your Designation code" data-toggle="tooltip"></i>',
	 '#suffix' => '</div>'
    );
	
	$form['company']['#type'] = 'actions';
    $form['company']['submit'] = array(
      '#type' => 'submit',
      '#default_value' => $this->t('Submit'),
      '#button_type' => 'primary',
	  '#attributes' => ['class' => ['btn btn-info']],
	  '#prefix' => '<br/><div class="row"><div class="col-md-2"></div><div class="col-md-4">',
	  '#suffix' => '',
	 '#disabled' => ($user->hasPermission('admin configuration')) ? false : true,
		  );
    return $form;

	  
  }
  
  
  public function validateForm(array &$form, FormStateInterface $form_state) {
	
  }

  
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
	$configobj = new \Drupal\company\Model\ConfigurationModel;	
	
    $field = $form_state->getValues();
	$employeeIdType = ($field['empidtype']) ? 'Automatic' : 'Manual';
    $branchCodeType = ($field['Branchcode']) ? 'on' : 'off';
    $designationCodeType = ($field['Designationcode']) ? 'on' : 'off';
    $departmentCodeType = ($field['Departmentcode']) ? 'on' : 'off';    
    $workorderCodeType = ($field['Workordercode']) ? 'on' : 'off';
	
	 $field  = array(
				array(
						'codetype'  	=>  'employeeid',
						'codename' 	    =>	'EMPID',
						'codevalues'	=>	$employeeIdType
					),
				array(
						'codetype'  	=>  'branchcode',
						'codename' 	    =>	'BRNCD',
						'codevalues'	=>	$branchCodeType
					), 
             	array(
						'codetype'  	=>  'designationcode',
						'codename' 	    =>	'DSGCD',
						'codevalues'	=>	$designationCodeType
					),
				array(
						'codetype'  	=>  'departmentcode',
						'codename' 	    =>	'DPTCD',
						'codevalues'	=>	$departmentCodeType
					),
                array(
						'codetype'  	=>  'workordercode',
						'codename' 	    =>	'WRKCD',
						'codevalues'	=>	$workorderCodeType
					) 					
          );
		 
		 $configobj->updatAllConfig($field);
	 drupal_set_message("All Configuration has been updated.");
	 
  }
}
?>
