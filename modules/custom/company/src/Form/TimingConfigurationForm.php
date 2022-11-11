<?php
/**
 * @file
 * Contains \Drupal\company\Form\TimingConfigurationForm.
 */

namespace Drupal\company\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

class TimingConfigurationForm extends FormBase {

  public function getFormId() {
	return 'tming_configuration_form';

  }

public function buildForm(array $form, FormStateInterface $form_state) {
	global $base_url;

	//$libobj = new \Drupal\library\Lib\LibController;
  $libobj = \Drupal::service('library.service');
	//$configobj = new \Drupal\company\Model\ConfigurationModel;
  $configobj = \Drupal::service('configuration.service');

	$result = $configobj->getShiftTimingList();

	$form['company']['#attributes']['enctype'] = "multipart/form-data";
	$form['#attached']['library'][] = 'singleportal/time-picker';
	$form['company']['#prefix'] = ' <div class="row">

				<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" >
					<a href="'.$base_url.'/organisation/config" role="tab"  ><span class="visible-xs"><i class="ti-home"></i></span><span class="hidden-xs"> <b>General</b></span></a>
				</li>
				<li role="presentation" class="active">
					<a href="'.$base_url.'/organisation/config/timing"  role="tab"  ><span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs"><b>Timing</b></span></a>
				</li>
				</ul><br/><br/>';
	$form['company']['#suffix'] = '</div>';


   $mode = $libobj->getActionMode();

   if($mode == 'edit'){
		$pk = $libobj->getIdFromUrl();
		$data = $configobj->getShiftDetailsById($pk);
   }


	$form['company']['shiftname'] = array(
      '#type' => 'textfield',
      '#title' => t('Shift Name:'),
	  '#attributes' => ['class' => ['form-control']],
	  '#prefix' => '<div class="row">',
	  '#suffix' => '</div>',
	  '#default_value' => isset($data)? $data->codevalues : '',
    '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="For EX: Morning Shift / Evenging Shift / Night Shift" data-toggle="tooltip"></i>',


    );

	$form['company']['fromtime'] = array(
      '#type' => 'textfield',
      '#title' => t('Time From:'),
	  '#attributes' => ['class' => ['form-control'], 'id' => 'time1'],
	  '#prefix' => '<div class="row">',
	  '#suffix' => '</div>',
	  '#default_value' => isset($data)? $data->description : '',
    '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Shift Start Timing" data-toggle="tooltip"></i>',
    );

	$form['company']['totime'] = array(
    '#type' => 'textfield',
    '#title' => t('To Time:'),
	  '#attributes' => ['class' => ['form-control'], 'id' => 'time2'],
	  '#prefix' => '<div class="row">',
	  '#suffix' => '</div>',
	  '#default_value' => isset($data)? $data->email : '',
    '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="Shift End Timing" data-toggle="tooltip"></i>',
    );


	$form['company']['#type'] = 'actions';
    $form['company']['submit'] = array(
      '#type' => 'submit',
      '#default_value' => ($mode == 'config') ? $this->t('Submit') : $this->t('Update'),
      '#button_type' => 'primary',
	  '#attributes' => ['class' => ['btn btn-info']],
	  '#prefix' => '<br/><div class="row"><div class="col-md-2"></div><div class="col-md-4">',
	  '#suffix' => '</div></div>',
		  );

	if($mode == 'edit'){
		 $form['company']['submit']['#suffix'] = '';

		$form['company']['cancel'] = [
		  '#title' => $this->t('Cancel'),
		  '#type' => 'link',
		  '#attributes' => ['class' => ['btn btn-default']],
		  '#url' => \Drupal\Core\Url::fromRoute('company.configuration_shift'),
		  '#prefix' => '&nbsp; &nbsp; &nbsp;',
		  '#suffix' => '</div></div>',
		];
	}



		$rows = array();
		$sl = 0;
		foreach ($result as $item) {
		  $sl++;
		  $html = ['#markup' => '<a href="'.$base_url.'/shift/edit/'.$item->codepk.'" style="text-align:center">
		  <i class="icon-note" title="" data-toggle="tooltip" data-original-title="Edit"></i></a>'];
		  $rows[] =   array(
						'data' =>  array( $sl, $item->codevalues, $item->description . ' - ' . $item->email, render($html))
		  );
		}

	$form['company']['shiftlist'] = array(
      '#type' 	    => 'table',
      '#header' 	  =>  array(t('SL'), t('Shift Name'),t('Timing'), t('Action')),
      '#rows'		    =>  $rows,
      '#attributes' => ['class' => ['table text-center table-hover table-striped table-bordered dataTable']],
      '#prefix'     => '<br/><br/><br/>',
    );


    return $form;


  }



  public function validateForm(array &$form, FormStateInterface $form_state) {

	if(trim($form_state->getValue('shiftname')) == ' ' ) {
        $form_state->setErrorByName('shiftname', $this->t('Enter your shift Name'));
    }
    else if(!preg_match("/^[a-zA-Z'-]+$/", $form_state->getValue('shiftname'))) {
        $form_state->setErrorByName('shiftname', $this->t('Enter a valid Shift Name'));
    }
  	if(empty($form_state->getValue('fromtime'))) {
        $form_state->setErrorByName('fromtime', $this->t('Enter your shift time'));
    }
	if(empty($form_state->getValue('totime')) == ' ' ) {
        $form_state->setErrorByName('totime', $this->t('Enter your shift ending time'));
    }

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

	  // $query = \Drupal::database();
	  // $query->query('start transaction');

		// for($i=1; $i<10; $i++)
		// {
			// $query->query('insert into srch_demo set userpk = :val, firstname = :val2', array(':val'=> $i, ':val2'=> 'hello'));

		// }



	 // $query->query('commit');


    //$configobj = new \Drupal\company\Model\ConfigurationModel;
    $configobj = \Drupal::service('configuration.service');
    //$libobj = new \Drupal\library\Lib\LibController;
    $libobj = \Drupal::service('library.service');

    $fieldval = $form_state->getValues();

    $codename = $libobj->generateCode('SHFT', $fieldval['shiftname']);

	 $field  = array(
			  'codetype'	=>	'jobshift',
			  'codename'	=>	$codename,
              'codevalues'  =>  $fieldval['shiftname'],
              'description' =>  $fieldval['fromtime'],
			  'email'		=>	$fieldval['totime'],
          );

		$mode = $libobj->getActionMode();

		if($mode == 'edit' )
		{
			$pk = $libobj->getIdFromUrl();
			$configobj->updateShiftTiming($field, $pk);
      \Drupal::messenger()->addMessage($field['codevalues'] . " has been updated.");
		}
		else
		{
			$configobj->setShiftTiming($field);
      \Drupal::messenger()->addMessage($field['codevalues'] . " has been created.");
		}


		$form_state->setRedirect('company.configuration_shift');
  }
}
?>
