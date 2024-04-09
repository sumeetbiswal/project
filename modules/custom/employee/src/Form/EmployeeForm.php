<?php
/**
 * @file
 * Contains \Drupal\organisation\Form\OrganisationForm.
 */

namespace Drupal\employee\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\library\Lib\LibController;

class EmployeeForm extends FormBase
{

    public function getFormId()
    {
        return 'organisation_form';

    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {



        $libobj = new \Drupal\library\Lib\LibController;
        $compobj = new \Drupal\organisation\Model\OrganisationModel;

        $mode = $libobj->getActionMode();

        if($mode == 'edit') {
            $pk = $libobj->getIdFromUrl();
            $data = $compobj->getOrganisationDetailsById($pk);
        }

        $form['organisation']['#attributes']['enctype'] = "multipart/form-data";

        $form['organisation']['#prefix'] = '<div class="row"> <div class="panel panel-inverse">
                            <div class="panel-heading"> Organisation details</div><div class="panel-body">';

        $form['organisation']['cname'] = array(
        '#type' => 'textfield',
        '#title' => t('Organisation Name:'),
        '#required' => true,
        '#prefix' => '<div class="row"><div class="col-md-6">',
        '#suffix' => '</div>',
        '#default_value' => isset($data)? $data->organisationname : '',

        );

        $complist = $compobj->getOrganisationTypeList();
        $comp_option[''] = 'Select Type of Organisation';
        foreach($complist AS $item)
        {
            $comp_option[$item->codename]  = $item->codevalues;
        }

        $form['organisation']['ctype'] = array(
        '#type' => 'select',
        '#title' => t('Organisation Type:'),
        '#required' => true,
        '#options' => $comp_option,
        '#prefix' => '<div class="col-md-6">',
        '#suffix' => '</div></div>',
        '#default_value' => isset($data)? $data->organisationtype : '',

        );
        $form['organisation']['cemail'] = array(
        '#type' => 'email',
        '#title' => t('Email:'),
        '#required' => true,
        '#prefix' => '<div class="row"><div class="col-md-6">',
        '#suffix' => '</div>',
        '#default_value' => isset($data)? $data->email : '',

        );

        $form['organisation']['cphone'] = array(
        '#type' => 'tel',
        '#title' => t('Phone number:'),
        //  '#required' => TRUE,
        '#prefix' => '<div class="col-md-6">',
        '#suffix' => '</div></div>',
               '#default_value' => isset($data)? $data->phone : '',

        );
        $form['organisation']['caddress'] = array(
        '#type' => 'textarea',
        '#title' => t('Address:'),
        //  '#required' => TRUE,
        '#prefix' => '<div class="row"><div class="col-md-6">',
        '#suffix' => '</div>',
        '#default_value' => isset($data)? $data->address : '',

        );
        $form['organisation']['clogo'] = array(
        '#type' => 'file',
        '#title' => t('Upload your Organisation logo:'),
        '#upload_location' => 'public://',
        // '#suffix' =>  '</div>',
        '#prefix' => '<div class="col-md-6">',
        '#suffix' => '</div></div>',
        );
        // $form['#suffix'] =  '</div>';


        $form['organisation']['#type'] = 'actions';
        $form['organisation']['submit'] = array(
        '#type' => 'submit',
        '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
        '#button_type' => 'primary',
        '#attributes' => ['class' => ['btn btn-info']],
        '#prefix' => '<div class="row"><div class="col-md-5"></div><div class="col-md-4">',
        '#suffix' => '',
        );

        $form['organisation']['cancel'] = array(
          '#type' => 'submit',
          '#value' => t('Cancel'),
        '#attributes' => ['class' => ['btn btn-default']],
        '#limit_validation_errors' => array(),
        '#prefix' => '',
        '#suffix' => '</div></div>',
          );
        $form['organisation']['cancel']['#submit'][] = '::ActionCancel';

        $form['organisation']['#suffix'] = '</div></div></div></div>';

        return $form;


    }

    public function ActionCancel(array &$form, FormStateInterface $form_state)
    {

        $form_state->setRedirect('organisation.view');
    }


    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        if (strlen($form_state->getValue('cphone')) < 10) {
              $form_state->setErrorByName('cphone', $this->t('Mobile number is too short.'));
            echo "<div>errorrrr</div>";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $libobj = new \Drupal\library\Lib\LibController;
        $compobj = new \Drupal\organisation\Model\OrganisationModel;


        $field = $form_state->getValues();


        $field  = array(
              'organisationname' =>  $field['cname'],
              'organisationcode' =>  $field['cname'],
              'organisationtype' =>  $field['ctype'],
              'email'        =>  $field['cemail'],
              'phone'         =>  $field['cphone'],
              'address'     =>  $field['caddress'],
              'logo'         =>  $field['clogo'],

          );

        $mode = $libobj->getActionMode();

        if($mode == 'add' ) {
            $compobj->setOrganisation($field);
            drupal_set_message("succesfully saved.");
        }
        if($mode == 'edit' ) {
            $compobj->updateOrganisation($field);
            drupal_set_message("succesfully Updated.");
        }

        $form_state->setRedirect('organisation.view');
    }
}
?>
