<?php

namespace Drupal\company\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

class DesignationForm extends FormBase {
  public function getFormId() {
    return 'designation_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

     global $base_url;
    $desobj = \Drupal::service('designation.service');
    $depobj = \Drupal::service('department.service');
    $conobj = \Drupal::service('configuration.service');
    $encrypt = \Drupal::service('encrypt.service');
    $libobj = \Drupal::service('library.service');

    $mode = $libobj->getActionMode();
    $form_title = 'Add Designation Details';
    if($mode == 'edit'){
      $pk = $libobj->getIdFromUrl();
      $pk = $encrypt->decode($pk);
      $data = $desobj->getDesignationDetailsById($pk);

      $form_title = 'Edit Designation - ' . $data->codevalues;
      $libobj->setPageTitle($form_title);
    }

	$form['#attached']['library'][] = 'singleportal/master-validation';
	$form['#attributes']['class'] = 'form-horizontal';
	$form['#attributes']['autocomplete'] = 'off';



    $form['designation']['name'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Designation Name:'),
      '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
      '#prefix'        => '<div class="row"><div class="col-md-12">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data)? $data->codevalues : '',
    );

	$descode_config = $conobj->getDesignationCodeConfig();
	$desconf = [];
	$desconf['disabled'] = '';
	$desconf['departmentcode'] = '';
	$desconf['helpmsg'] = 'Mention Designation Code of the person';

	if($descode_config->codevalues == 'off')
	{
		$desconf['disabled'] = 'disabled';
		$desconf['designationcode'] = 'XXXXXXX';
		$desconf['helpmsg'] = 'Designation Code will be auto generate';
	}

    $form['designation']['code'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Designation Code:'),
      '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
      '#prefix'        => '<div class="row"><div class="col-md-12">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data)? $data->codename : $desconf['designationcode'],
      '#disabled'      =>  $desconf['disabled'],
      '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="'.$desconf['helpmsg'].'" data-toggle="tooltip"></i>',

    );

      $deplist = $depobj->getAllDepartmentDetails();
      $dept_option[''] = 'Select Department';
      foreach($deplist AS $item)
      {
      $dept_option[$item->codename]  = $item->codevalues;
      }

      if($mode == 'edit'){
        $codepk = $data->parent;
        $res = $depobj->getDepartmentDetailsById($codepk);
        $dept = $res->codename;
      }

    $form['designation']['department'] = array(
      '#type'          => 'select',
      '#title'         => t('Department :'),
      '#options'       => $dept_option,
      '#attributes'    => ['class' => ['form-control', 'validate[required]']],
      '#prefix'        => '<div class="row"><div class="col-md-12">',
      '#suffix'        => '</div></div>',
      '#default_value' => isset($data)? $dept : '',
      '#field_suffix' => '<a href="'.$base_url.'/department/modal" class="use-ajax button"><i class="fadehide mdi mdi-settings fa-fw"></i></a>',
   // $form['designation']['#type'] = 'actions';
    );
    $form['designation']['submit'] = array(
      '#type'          => 'submit',
      '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
      '#button_type'   => 'primary',
      '#attributes'    => ['class' => ['btn btn-info']],
      '#prefix'        => '<br/><div class="row"><div class="col-md-2"></div><div class="col-md-6">',
      '#suffix'        => '',
    );

    $form['designation']['cancel'] = array(
      '#type' => 'link',
	  '#title' => t('Cancel'),
      '#attributes' => ['class'   => ['btn btn-default']],
      '#prefix'    => '',
      '#suffix'    => '</div></div>',
      '#url' => \Drupal\Core\Url::fromRoute('designation.view'),
    );
    $form['designation']['cancel']['#submit'][] = '::ActionCancel';

    return $form;

    }

  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

	public function ActionCancel(array &$form, FormStateInterface $form_state)
	{
    $form_state->setRedirect('designation.view');
	}

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $libobj = \Drupal::service('library.service');
    $desobj = \Drupal::service('designation.service');
    $depobj = \Drupal::service('department.service');
	  $conobj = \Drupal::service('configuration.service');
    $encrypt = \Drupal::service('encrypt.service');

	$code_config = $conobj->getDesignationCodeConfig();
	$field = $form_state->getValues();

	//check codevalues OFF then auto generate the code values
	$code = ( $code_config->codevalues == 'on' ) ? $field['code'] : $libobj->generateCode('DSG', $field['name']) ;


    $name = $field['name'];
    $parent = $field['department'];

    $parent = $depobj->getDepartmentId($parent);

    $field  = array(
      'codevalues' =>  $name,
      'codename'   =>  $code,
      'parent'   =>  $parent->codepk,
      'codetype'   => 'designation',
     );

    $mode = $libobj->getActionMode();
    if($mode == 'add' )
    {
      $desobj->setDesignation($field);
      \Drupal::messenger()->addMessage($field['codevalues'] . " has been succesfully created.");
    }
    if($mode == 'edit' )
    {
      $pk = $libobj->getIdFromUrl();
      $pk = $encrypt->decode($pk);
      $desobj->updateDesignation($field,$pk);
      \Drupal::messenger()->addMessage($field['codevalues'] . " has succesfully Updated.");
    }

   $form_state->setRedirect('designation.view');

  }
}
?>
