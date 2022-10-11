<?php

namespace Drupal\company\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

class DepartmentForm extends FormBase {
  public function getFormId() {
    return 'department_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {  
  
    $libobj = new \Drupal\library\Lib\LibController;
    $brnobj = new \Drupal\company\Model\DepartmentModel;
	$conobj = new \Drupal\company\Model\ConfigurationModel;
    $mode = $libobj->getActionMode();
    
    if($mode == 'edit'){
      $pk = $libobj->getIdFromUrl();	
      $data = $brnobj->getDepartmentDetailsById($pk);
    }

	
	
	$form['#attached']['library'][] = 'singleportal/master-validation';
	$form['#attributes']['class'] = 'form-horizontal';
	$form['#attributes']['autocomplete'] = 'off';
    $form['department']['#prefix'] = '<div class="row"> <div class="panel panel-inverse">
                                      <div class="panel-heading">Add Department Details</div><div class="panel-body">';
    $form['department']['name'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Department Name:'),
      '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
      '#prefix'        => '<div class="row">',
      '#suffix'        => '</div>',
      '#default_value' => isset($data)? $data->codevalues : '',
    );
	
	$dptcode_config = $conobj->getDepartmentcodeConfig();
	$dpt_conf = [];
	$dpt_conf['disabled'] = '';
	$dpt_conf['departmentcode'] = '';
	$dpt_conf['helpmsg'] = 'Mention Department Code of the person';
	if($dptcode_config->codevalues == 'off')
	{
		$dpt_conf['disabled'] = 'disabled';
		$dpt_conf['departmentcode'] = 'XXXXXXX';
		$dpt_conf['helpmsg'] = 'Department Code will be auto generate';			
	}
	
    $form['department']['code'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Department Code:'),
      '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
      '#prefix'        => '<div class="row">',
      '#suffix'        => '</div>',
      '#default_value' => isset($data)? $data->codename : $dpt_conf['departmentcode'],
      '#disabled'      =>  $dpt_conf['disabled'],
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="'.$dpt_conf['helpmsg'].'" data-toggle="tooltip"></i>',

    );
    
    $form['department']['submit'] = array(
      '#type'          => 'submit',
      '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
      '#button_type'   => 'primary',
      '#attributes'    => ['class' => ['btn btn-info']],
      '#prefix'        => '<br/><div class="row"><div class="col-md-2"></div><div class="col-md-4">',
      '#suffix'        => '',
    );

    $form['department']['cancel'] = array(
      '#type' => 'link',
	  '#title' => t('Cancel'),
      '#attributes'               => ['class'   => ['btn btn-default']],
      //'#limit_validation_errors'  => array(),
      '#prefix'                   => '',
      '#suffix'                   => '</div></div>',
      '#url' => \Drupal\Core\Url::fromRoute('company.departmentview'),
    );
	//$form['department']['#type'] = 'actions';
    //$form['department']['cancel']['#submit'][] = '::ActionCancel';
    $form['company']['#suffix'] = '</div></div>';
    return $form;

    }
 
  public function validateForm(array &$form, FormStateInterface $form_state) { 
    $brnobj = new \Drupal\company\Model\DepartmentModel;
	$deptname = trim($form_state->getValue('name'));
	$dept_exist = $brnobj->deptIsExist($deptname);
	
	if($dept_exist)
	{
		$form_state->setErrorByName('name', $this->t('Department has already Exist. Duplicate is not allowed.'));
	}
  }
  
	public function ActionCancel(array &$form, FormStateInterface $form_state)
	{	  
    $form_state->setRedirect('company.departmentview');
	}

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $libobj = new \Drupal\library\Lib\LibController;
    $brnobj = new \Drupal\company\Model\DepartmentModel;
    $conobj = new \Drupal\company\Model\ConfigurationModel;
	
    $field = $form_state->getValues();
	$code_config = $conobj->getDepartmentCodeConfig();
	
	//check codevalues OFF then auto generate the code values 
	$code = ( $code_config->codevalues == 'on' ) ? $field['code'] : $libobj->generateCode('DPT', $field['name']) ;
	
    $name = $field['name'];
   
    $field  = array(
      'codevalues' =>  $name,
      'codename'   =>  $code,
      'codetype'   => 'department',             
     );

    $mode = $libobj->getActionMode();
    if($mode == 'add' )
    { 
      $brnobj->setDepartment($field);
      drupal_set_message("succesfully saved.");
    }
    if($mode == 'edit' )
    {
      $pk = $libobj->getIdFromUrl();
      $brnobj->updateDepartment($field,$pk);
      drupal_set_message("succesfully Updated.");
    }
   
   $form_state->setRedirect('company.departmentview');

  }
}
?>
