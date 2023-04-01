<?php
/**
 * @file
 * Contains \Drupal\tagging\Form\TaggingForm.
 */


namespace Drupal\tagging\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;

/**
 * TaggingForm
 */
class TaggingForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tagging_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $libobj = \Drupal::service('library.service');
    $encrypt = \Drupal::service('encrypt.service');
    $tagging = \Drupal::service('tagging.service');
    $workorder = \Drupal::service('workorder.service');

    $mode = $libobj->getActionMode();

    $form_title = 'Add Tagging details';

    if($mode == 'edit'){
      $pk = $libobj->getIdFromUrl();
      $pk = $encrypt->decode($pk);
      $form_title = 'Edit Tagging details';
      $data = $tagging->getTaggingDetailsById($pk);

     // echo "<pre/>";print_r($data);die;
    }

    $form['#attached']['library'][] = 'singleportal/master-validation';
    $form['#attributes']['class'] = 'form-horizontal';
    $form['#attributes']['autocomplete'] = 'off';


    $form['tagging']['empid'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Employee Id:'),
      '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
      '#disabled'      => 'disabled',
      '#prefix'        => '<div class="row">',
      '#default_value' => $data->empid,
    );

    $form['tagging']['empname'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Name:'),
      '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
      '#disabled'      => 'disabled',
      '#suffix'        => '</div>',
      '#default_value' => $data->firstname . ' ' . $data->lastname,
    );

    $project_list = $workorder->getWorkorderList();
    $won_list[''] = 'Select Project';
    foreach($project_list AS $item) {
      $won_list[$item->codename]  = $item->codevalues;
    }

    $form['tagging']['won'] = array(
      '#type'          => 'select',
      '#title'         => t('Project :'),
      '#options'       => $won_list,
      '#attributes'    => ['class' => ['form-control', 'validate[required]']],
      '#prefix'        => '<div class="row">',
      '#default_value' => isset($data)? $data->won : '',
      '#ajax' => [
        'callback' => '::getTeamList',
        'wrapper' => 'teamlist',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => t(''),
        ],
      ],
    );

    if (!empty($form_state->getValue('won'))) {
      $wonNo = $form_state->getValue('won');
    }
    else{
      $wonNo = isset($data)? $data->won : '';
    }

    $team_list = [];
    $team_list = $workorder->getTeamListByWorkOrder($wonNo);


    $form['tagging']['ton'] = array(
      '#type'          => 'select',
      '#title'         => t('Team :'),
      '#options'       => $team_list,
      '#attributes'    => ['class' => ['form-control', 'validate[required]']],
      '#prefix'        => '<div id="teamlist">',
      '#suffix'        => '</div></div>',
      //'#default_value' => isset($data)? $data->won : '',
    );


    $form['tagging']['supervisor'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Supervisor :'),
      '#autocomplete_route_name' => 'employee.autocomplete',
      //'#autocomplete_route_parameters' => ['pwon' => 'hello'],
      '#attributes'    => ['class' => ['form-control', 'validate[required]']],
      '#suffix'        => '</div>',
      //'#default_value' => isset($data)? $data->won : '',
    );












    $form['tagging']['submit'] = array(
      '#type'          => 'submit',
      '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
      '#button_type'   => 'primary',
      '#attributes'    => ['class' => ['btn btn-info']],
      '#prefix'        => '<br/><div class="row"><div class="col-md-2"></div><div class="col-md-6">',
      '#suffix'        => '',
    );

    $form['tagging']['cancel'] = array(
      '#type' => 'link',
      '#title' => t('Cancel'),
      '#attributes' => ['class'   => ['btn btn-default']],
      '#prefix'    => '',
      '#suffix'    => '</div></div>',
      '#url' => \Drupal\Core\Url::fromRoute('company.Designationview'),
    );

  	$form_state->setCached(FALSE);

    return $form;

    }

  public function validateForm(array &$form, FormStateInterface $form_state) {

  }


  public function submitForm(array &$form, FormStateInterface $form_state) {


  }

  public function getTeamList(array $form, FormStateInterface $form_state){
    return $form['tagging']['ton'];
  }

}
?>
