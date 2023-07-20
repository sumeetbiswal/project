<?php
/**
 * @file
 * Contains \Drupal\company\Form\CompanyForm.
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
        return 'company_form';

    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {


        $libobj = new \Drupal\library\Lib\LibController;
        $compobj = new \Drupal\company\Model\CompanyModel;

        $mode = $libobj->getActionMode();

        if($mode == 'edit') {
            $pk = $libobj->getIdFromUrl();
            $data = $compobj->getCompanyDetailsById($pk);
        }

        $form['company']['#attributes']['enctype'] = "multipart/form-data";

        $form['company']['#prefix'] = '<div class="row"> <div class="panel panel-inverse">
                            <div class="panel-heading"> Company details</div><div class="panel-body">';

        $form['company']['cname'] = array(
        '#type' => 'textfield',
        '#title' => t('Company Name:'),
        '#required' => true,
        '#attributes' => ['class' => ['form-control']],
        '#prefix' => '<div class="row"><div class="col-md-6">',
        '#suffix' => '</div>',
        '#default_value' => isset($data)? $data->companyname : '',

        );

        $complist = $compobj->getCompanyTypeList();
        $comp_option[''] = 'Select Type of Organisation';
        foreach($complist AS $item)
        {
            $comp_option[$item->codename]  = $item->codevalues;
        }

        $form['company']['ctype'] = array(
        '#type' => 'select',
        '#title' => t('Company Type:'),
        '#required' => true,
        '#options' => $comp_option,
        '#attributes' => ['class' => ['form-control']],
        '#prefix' => '<div class="col-md-6">',
        '#suffix' => '</div></div>',
        '#default_value' => isset($data)? $data->companytype : '',

        );
        $form['company']['cemail'] = array(
        '#type' => 'email',
        '#title' => t('Email:'),
        '#required' => true,
        '#attributes' => ['class' => ['form-control']],
        '#prefix' => '<div class="row"><div class="col-md-6">',
        '#suffix' => '</div>',
        '#default_value' => isset($data)? $data->email : '',

        );

        $form['company']['cphone'] = array(
        '#type' => 'tel',
        '#title' => t('Phone number:'),
        //  '#required' => TRUE,
        '#attributes' => ['class' => ['form-control']],
        '#prefix' => '<div class="col-md-6">',
        '#suffix' => '</div></div>',
               '#default_value' => isset($data)? $data->phone : '',

        );
        $form['company']['caddress'] = array(
        '#type' => 'textarea',
        '#title' => t('Address:'),
        //  '#required' => TRUE,
        '#attributes' => ['class' => ['form-control']],
        '#prefix' => '<div class="row"><div class="col-md-6">',
        '#suffix' => '</div>',
        '#default_value' => isset($data)? $data->address : '',

        );
        $form['company']['clogo'] = array(
        '#type' => 'file',
        '#title' => t('Upload your Company logo:'),
        '#upload_location' => 'public://',
        '#attributes' => ['class' => ['form-control']],
        // '#suffix' =>  '</div>',
        '#prefix' => '<div class="col-md-6">',
        '#suffix' => '</div></div>',
        );
        // $form['#suffix'] =  '</div>';


        $form['company']['#type'] = 'actions';
        $form['company']['submit'] = array(
        '#type' => 'submit',
        '#default_value' => ($mode == 'add') ? $this->t('Submit') : $this->t('Update'),
        '#button_type' => 'primary',
        '#attributes' => ['class' => ['btn btn-info']],
        '#prefix' => '<div class="row"><div class="col-md-5"></div><div class="col-md-4">',
        '#suffix' => '',
        );

        $form['company']['cancel'] = array(
          '#type' => 'submit',
          '#value' => t('Cancel'),
        '#attributes' => ['class' => ['btn btn-default']],
        '#limit_validation_errors' => array(),
        '#prefix' => '',
        '#suffix' => '</div></div>',
          );
        $form['company']['cancel']['#submit'][] = '::ActionCancel';

        $form['company']['#suffix'] = '</div></div></div></div>';

        return $form;


    }

    public function ActionCancel(array &$form, FormStateInterface $form_state)
    {

        $form_state->setRedirect('company.view');
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
        $compobj = new \Drupal\company\Model\CompanyModel;


        $field = $form_state->getValues();


        $field  = array(
              'companyname' =>  $field['cname'],
              'companycode' =>  $field['cname'],
              'companytype' =>  $field['ctype'],
              'email'        =>  $field['cemail'],
              'phone'         =>  $field['cphone'],
              'address'     =>  $field['caddress'],
              'logo'         =>  $field['clogo'],

          );

        $mode = $libobj->getActionMode();

        if($mode == 'add' ) {
            $compobj->setCompany($field);
            drupal_set_message("succesfully saved.");
        }
        if($mode == 'edit' ) {
            $compobj->updateCompany($field);
            drupal_set_message("succesfully Updated.");
        }

        $form_state->setRedirect('company.view');
    }
}
?>
