<?php
/**
 * @file
 * Contains \Drupal\company\Form\BranchForm.
 */

namespace Drupal\company\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\library\Lib\LibController;

/**
 * BranchForm
 */
class BranchForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'branch_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {


        $libobj = \Drupal::service('library.service');
        $brnobj = \Drupal::service('branch.service');
        $encrypt = \Drupal::service('encrypt.service');
        $conobj = \Drupal::service('configuration.service');

        $mode = $libobj->getActionMode();
        $form_state->setCached(false);
        $form_title = 'Add Branch details';
        if($mode == 'edit') {
            $pk = $libobj->getIdFromUrl();
            $pk = $encrypt->decode($pk);

            $data = $brnobj->getBranchDetailsById($pk);

              $form_title = 'Edit Branch - ' . $data->codevalues;
              $libobj->setPageTitle($form_title);
        }
        $form['#attached']['library'][] = 'singleportal/master-validation';
        $form['#attributes']['class'] = 'form-horizontal';
        $form['#attributes']['autocomplete'] = 'off';
        $form['branch']['name'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Branch Name:'),
        '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
        '#prefix'        => '<div class="row">',
        '#default_value' => isset($data)? $data->codevalues : '',
        '#field_suffix'  => '<i class="fadehide mdi mdi-help-circle" title="Branch name of your company " data-toggle="tooltip"></i>',
        );

        $branchcode_config = $conobj->getBranchCodeConfig();
        $brnch_config = [];
        $brnch_config['disabled'] = '';
        $brnch_config['branchcode'] = '';
        $brnch_config['helpmsg'] = 'Mention Branch Code of the person';

        if($branchcode_config->codevalues == 'off') {
            $brnch_config['disabled'] = 'disabled';
            $brnch_config['branchcode'] = 'XXXXXXX';
            $brnch_config['helpmsg'] = 'Branch Code will be auto generate';
        }
        $form['branch']['code'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Branch Code:'),
        '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
        '#suffix'        => '</div>',
        '#default_value' => isset($data)? $data->codename : $brnch_config['branchcode'],
        '#disabled'      =>  $brnch_config['disabled'],
        '#field_suffix' => '<i class="fadehide mdi mdi-help-circle" title="'.$brnch_config['helpmsg'].'" data-toggle="tooltip"></i>',
        );

        $statelist = $libobj->getStateList();

        $form['branch']['state'] = array(
        '#type'    => 'select',
        '#title'   => t('State:'),
        '#options' => $statelist,
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
        else {
            $statePk = isset($data)? $data->state : '';
        }

        $cityLst = [];
        $cityLst = $libobj->getCityListByState($statePk);

        $form['branch']['city'] = array(
        '#type'          => 'select',
        '#title'         => t('City:'),
        '#options'       => $cityLst,
        '#attributes'    => ['class' => ['form-control', 'validate[required]']],
        '#prefix'        => '<div id="citylist">',
        '#suffix'        => '</div></div>',
        '#default_value' => isset($data)? $data->city : $form_state->getValue('city'),
        );

        $form['branch']['location'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Location:'),
        '#attributes'    => ['class' => ['form-control', 'validate[required]']],
        '#prefix'        => '<div class="row">',


        '#default_value' => isset($data)? $data->location : '',
        );
        $form['branch']['pincode'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Pincode'),
        '#attributes'    => ['class' => ['form-control', 'validate[required]'], 'id' => ['pincode']],
        '#default_value' => isset($data)? $data->pincode : '',
            '#suffix'        => '</div>',

        );

        $form['branch']['#type'] = 'actions';
        $form['branch']['submit'] = array(
        '#type'          => 'submit',
        '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
        '#button_type'   => 'primary',
        '#attributes'    => ['class' => ['btn btn-info']],
        '#prefix'        => '<div class="row"><div class="col-md-5"></div><div class="col-md-4">',
        '#suffix'        => '',
          );

        $form['branch']['cancel'] = array(
        '#type' => 'link',
        '#title' => t('Cancel'),
        '#attributes' => ['class' => ['btn btn-default']],
        '#prefix' => '',
        '#suffix' => '</div></div>',
        '#url' => \Drupal\Core\Url::fromRoute('branch.view'),
          );

        return $form;
    }


    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $field = $form_state->getValues();
    }
    public function ActionCancel(array &$form, FormStateInterface $form_state)
    {
        $form_state->setRedirect('branch.view');
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

        $libobj = \Drupal::service('library.service');
        $brnobj = \Drupal::service('branch.service');
        $encrypt = \Drupal::service('encrypt.service');
        $conobj = \Drupal::service('configuration.service');

        $field = $form_state->getValues();
        $code_config = $conobj->getBranchCodeConfig();

        //check codevalues OFF then auto generate the code values
        $code = ( $code_config->codevalues == 'on' ) ? $field['code'] : $libobj->generateCode('BR', $field['name']);


        $name = $field['name'];
        $location = $field['location'];
        $city = $field['city'];
        $state = $field['state'];
        $pincode = $field['pincode'];

        $data  = array(
              'codevalues' =>  $name,
              'codename' => $code,
              'codetype' => 'branch',
              'location' =>  $location,
              'city' =>  $city,
              'state' =>  $state,
              'pincode' =>  $pincode,
          );

        $mode = $libobj->getActionMode();

        if($mode == 'add') {
            $brnobj->setBranch($data);
            \Drupal::messenger()->addMessage($data['codevalues'] . " has been created.");
        }
        if($mode == 'edit') {
            $pk = $libobj->getIdFromUrl();
            $pk = $encrypt->decode($pk);
            $brnobj->updateBranch($data, $pk);
            \Drupal::messenger()->addMessage($data['codevalues'] . " has succesfully Updated.");
        }
        $form_state->setRedirect('branch.view');

    }
    public function getCityList(array $form, FormStateInterface $form_state)
    {
        return $form['branch']['city'];
    }
}
?>
