<?php
/**
 * @file
 * Contains \Drupal\employee\Form\Multistep\EmployeePersonalForm.
 */

namespace Drupal\employee\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Request;


class EmployeePersonalForm extends EmployeeFormBase
{

    /**
     * {@inheritdoc}.
     */
    public function getFormId()
    {
        return 'multistep_form_one';
    }

    /**
     * {@inheritdoc}.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
      
        if(!$this->store->get('contact_back')) {
            parent::deletePersonalStore();
            parent::deleteContactStore();
            parent::deleteAcademicStore();
        }
    
        $form = parent::buildForm($form, $form_state);
    
        $form['actions']['submit'] = array();
        $form['#attached']['library'][] = 'singleportal/datetimepicker';
        $form['#attached']['library'][] = 'singleportal/master-validation';
    
        $form['#attributes']['autocomplete'] = 'off';
        $form['employee']['#prefix'] = '<ul id="eliteregister">
									<li class="active">Personal Details</li>
									<li>Contact Details</li>
									<li>Academic Details</li>
									<li>Official Details</li>
									</ul><div class="row"><div class="panel-body"><h3 class="box-title">Personal</h3>
                                            <hr class="m-t-0 m-b-40">';
        $form['employee']['#suffix'] = '</div></div>';
                                    
        $form['employee']['firstname'] = array(
        '#type'            => 'textfield',
        '#title'            => $this->t('First Name'),
        '#default_value' => $this->store->get('firstname') ? $this->store->get('firstname') : '',
        '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
        '#prefix'        => '<div class="row">',
        '#suffix'        => '',
        );

        $form['employee']['lastname'] = array(
        '#type'             => 'textfield',
        '#title'             => $this->t('Last Name'),
        '#default_value'     => $this->store->get('lastname') ? $this->store->get('lastname') : '',
        '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
        '#suffix'        => '</div>',
        );
    
        $form['employee']['fname'] = array(
        '#type'            => 'textfield',
        '#title'            => $this->t('Father Name'),
        '#default_value' => $this->store->get('fname') ? $this->store->get('fname') : '',
        '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
        '#prefix'        => '<div class="row">',
        '#suffix'        => '',
        );

        $form['employee']['mname'] = array(
        '#type'             => 'textfield',
        '#title'             => $this->t('Mother Name'),
        '#default_value'     => $this->store->get('mname') ? $this->store->get('mname') : '',
        '#attributes'    => ['class' => ['form-control']],
        '#suffix'        => '</div>',
        );
    
        $form['employee']['gender'] = array(
        '#type'          => 'select',
        '#title'         => t('Gender'),
        '#options'       => ['M' => 'Male', 'F' => 'Female', 'O' => 'Other'],
        '#attributes'    => ['class' => ['form-control', 'validate[required]']],
        '#prefix'        => '<div class="row">',
        '#default_value' => $this->store->get('gender') ? $this->store->get('gender') : '',

        );
    
        $form['employee']['dob'] = array(
        '#type'             => 'textfield',
        '#title'             => $this->t('Date of Birth'),
        '#default_value'     => $this->store->get('dob') ? $this->store->get('dob') : '',
        '#attributes'    => ['id' => ['datetimepicker'],'class' => ['form-control' , 'validate[required]'],'readonly' => 'readonly'],
        '#suffix'        => '</div>',
        );
    
        $form['employee']['marital'] = array(
        '#type'          => 'select',
        '#title'         => t('Marital'),
        '#options'       => ['M' => 'Married', 'U' => 'Unmarried'],
        '#attributes'    => ['class' => ['form-control', 'validate[required]']],
        '#prefix'        => '<div class="row">',
        '#default_value' => $this->store->get('marital') ? $this->store->get('marital') : '',

        );
    
        $blood = [
                'O+' => 'O+',
                'O-' => 'O-',
                'A+' => 'A+',
                'A-' => 'A-',
                'B+' => 'B+',
                'B-' => 'B-',
                'AB+' => 'AB+',
                'AB-' => 'AB-',
             ];
    
        $form['employee']['blood'] = array(
        '#type'             => 'select',
        '#title'             => $this->t('Blood Group'),
        '#options'       => $blood,
        '#default_value'     => $this->store->get('blood') ? $this->store->get('blood') : '',
        '#attributes'    => ['class' => ['form-control', 'validate[required]']],
        '#suffix'        => '</div>',
        );
    
        $form['employee']['religion'] = array(
        '#type'            => 'textfield',
        '#title'            => $this->t('Religion'),
        '#default_value' => $this->store->get('religion') ? $this->store->get('religion') : '',
        //'#required'          => TRUE,
        '#attributes'    => ['class' => ['form-control']],
        '#prefix'        => '<div class="row">',
        '#suffix'        => '',
        );

        $form['employee']['nationality'] = array(
        '#type'             => 'textfield',
        '#title'             => $this->t('Nationality'),
        '#default_value'     => $this->store->get('nationality') ? $this->store->get('nationality') : '',
        // '#required'          => TRUE,
        '#attributes'    => ['class' => ['form-control', 'validate[required,custom[onlyLetterSp]]']],
        '#suffix'        => '</div>',
        );
        $form['employee']['cancel'] = [
          '#title' => $this->t('Cancel'),
          '#type' => 'link',
          '#attributes' => ['class' => ['btn btn-default']],
          '#url' => \Drupal\Core\Url::fromRoute('employee.emplist'),
          '#prefix' => '<br/><div class="row"><div class="col-md-5"></div><div class="col-md-4">',
        ];
    
        $form['employee']['#type'] = 'actions';
        $form['employee']['submit'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Next'),
        '#button_type' => 'primary',
        '#attributes' => ['class' => ['btn btn-info']],
        '#prefix' => '',
        '#suffix' => '</div></div>',
          );
         
        $this->store->set('contact_back', false);
        $this->store->set('personal_bypass', 0);
        return $form;
    }
  
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        
        $max_age_dob = date('Y', strtotime('-18 years'));   
        $user_year = explode('/', $form_state->getValue('dob'));
        $dob_year=$user_year[2];    

        if (trim($form_state->getValue('firstname')) == '' ) {
             $form_state->setErrorByName('firstname', $this->t('Enter your firstname'));
        }
        else if(!preg_match("/^[a-zA-Z'-]+$/", $form_state->getValue('firstname'))) {
            $form_state->setErrorByName('firstname', $this->t('Enter your valid Name')); 
        }
        if (trim($form_state->getValue('lastname')) == '' ) {
            $form_state->setErrorByName('lastname', $this->t('Enter your Lastname'));
        }
        else if(!preg_match("/^[A-Za-z]+((\s)?([A-Za-z])+)*$/", $form_state->getValue('lastname'))) {
            $form_state->setErrorByName('lastname', $this->t('Enter a valid Lastname')); 
        }
        if (trim($form_state->getValue('fname')) == '' ) {
            $form_state->setErrorByName('fname', $this->t('Enter your Fathers name'));
        }
        else if(!preg_match("/^[A-Za-z]+((\s)?([A-Za-z])+)*$/", $form_state->getValue('fname'))) {
            $form_state->setErrorByName('fname', $this->t('Enter a valid Fathersname ')); 
        }
        if (trim($form_state->getValue('dob')) == '' ) {
             $form_state->setErrorByName('dob', $this->t('Enter your Date of birth'));
        }
        else if ($dob_year > $max_age_dob ) {
            $form_state->setErrorByName('dob', $this->t('Date of Birth should not less than 18 years.'));
        }
  
        if (trim($form_state->getValue('nationality')) == '' ) {
             $form_state->setErrorByName('nationality', $this->t('Enter your nationality'));
        }
        else if(!preg_match("/^[a-zA-Z'-]+$/", $form_state->getValue('nationality'))) {
            $form_state->setErrorByName('nationality', $this->t('Enter a valid nationality ')); 
        }
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
      
        $field_list = array('firstname', 'lastname', 'fname', 'mname', 'gender', 'dob', 'marital', 'blood', 'religion', 'nationality');
    
        foreach($field_list AS $val)
        {
            $this->store->set($val, $form_state->getValue($val));
        }
    
    
        $this->store->set('personal_bypass', true);
    
        $form_state->setRedirect('employee.empaddcontct');
    }
  
}
