<?php
/**
 * @file
 * Contains \Drupal\employee\Form\Multistep\EmployeePreview
 */

namespace Drupal\employee\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EmployeePreview extends EmployeeFormBase
{

    /**
     * {@inheritdoc}.
     */
    public function getFormId()
    {
        return 'multistep-preview';
    }

    /**
     * {@inheritdoc}.
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['actions']['submit'] = array();

        $libobj = \Drupal::service('library.service');
        $brnobj = \Drupal::service('branch.service');
        $desgnobj = \Drupal::service('designation.service');
        $deptobj = \Drupal::service('department.service');




        global $base_url;
        $form['employee']['#prefix'] = '<div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-info">
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                <form class="form-horizontal" role="form">
                                  <div class="form-body">';

        $form['employee']['#suffix'] = '</form></div></div></div></div></div></div>';


        $form['employee']['firstname'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('First Name:'),
        '#markup'      => $this->store->get('firstname'),
        '#prefix'      => '<h3 class="box-title">Personal Info <div class="pull-right">
                          <a href="'.$base_url.'/employee/add/personal"><i class="mdi mdi-pencil-circle" title="" style="font-size: x-large;" data-toggle="tooltip" data-original-title="Edit"></i></a>
                          </div> </h3><hr class="m-t-0 m-b-40"><div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['lastname'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Last Name:'),
        '#markup'      => $this->store->get('lastname'),
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );

        $form['employee']['fname'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Fathers Name:'),
        '#markup'      => $this->store->get('fname'),
        '#prefix'      => '<div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['mname'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Mothers Name:'),
        '#markup'      => $this->store->get('mname'),
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );

        switch($this->store->get('gender'))
        {
        CASE 'M':
            $gender = 'Male';
            break;
        CASE 'F':
            $gender = 'Female';
            break;
        default:
            $gender = 'Other';
            break;
        }

        $form['employee']['gender'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Gender:'),
        '#markup'      => $gender,
        '#prefix'      => '<div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['dob'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Date of birth::'),
        '#markup'      => date("j F Y", strtotime($this->store->get('dob'))),
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );
        $form['employee']['marital'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Marital:'),
        '#markup'      => ($this->store->get('marital') == 'M') ? 'Married' : 'Unmarried',
        '#prefix'      => '<div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['blood'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Blood group:'),
        '#markup'      => $this->store->get('blood'),
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );
        $form['employee']['religion'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Religion:'),
        '#markup'      => $this->store->get('religion'),
        '#prefix'      => '<div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['nationality'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Nationality:'),
        '#markup'      => $this->store->get('nationality'),
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );

        //***************************************************************

        $form['employee']['phoneno'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Phone no.:'),
        '#markup'      => $this->store->get('phoneno'),
        '#prefix'      => '</br> </br> <h3 class="box-title">Contact Info <div class="pull-right">
                          <a href="'.$base_url.'/employee/add/contact"><i class="mdi mdi-pencil-circle" title="" style="font-size: x-large;"data-toggle="tooltip" data-original-title="Edit"></i></a>
                          </div> </h3><hr class="m-t-0 m-b-40"><div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['altphoneno'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Alt phone number:'),
        '#markup'      => $this->store->get('altphoneno'),
        '#prefix'      => '',
        '#suffix'      => '</div>',
        );

        $form['employee']['emergencyno'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Emergency Contact:'),
        '#markup'      => $this->store->get('emergencyno'),
        '#prefix'      => '<div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['relationship'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Relationship:'),
        '#markup'      => $this->store->get('relationship'),
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );
        $form['employee']['email'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Email:'),
        '#markup'      => $this->store->get('email'),
        '#prefix'      => '<div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['image'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Image:'),
        '#markup'      => $this->store->get('image'),
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );
        $form['employee']['address1'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Address Line 1:'),
        '#markup'      => $this->store->get('address1'),
        '#prefix'      => '</br> </br>
                         <h3 class="box-title">Present address</h3>
                          <hr class="m-t-0 m-b-40"><div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['address2'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Address Line 2:'),
        '#markup'      => $this->store->get('address2'),
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );

        $state_name = $libobj ->getStateNameById($this->store->get('state'));
        $city_name = $libobj ->getCityNameById($this->store->get('city'));
        $country_name = $libobj ->getCountryNameById($this->store->get('country'));

        $form['employee']['state'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('State:'),
        '#markup'      => $state_name,
        '#prefix'      => '<div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['city'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('City:'),
        '#markup'      => $city_name,
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );
        $form['employee']['pincode'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Pincode:'),
        '#markup'      => $this->store->get('pincode'),
        '#prefix'      => '<div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['country'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Country:'),
        '#markup'      => $country_name,
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );
        if(!$this->store->get('addresscopy')) {
            $form['employee']['permanentaddress1'] = array(
            '#type'            => 'item',
            '#title'            => $this->t('Address Line 1:'),
            '#markup'      => $this->store->get('permanentaddress1'),
            '#prefix'      => '</br> </br><h3 class="box-title">Permanent address</h3>
                        <hr class="m-t-0 m-b-40"><div class="row">',
            '#suffix'      => '',
            );
            $form['employee']['permanentaddress2'] = array(
            '#type'            => 'item',
            '#title'            => $this->t('Address Line 2:'),
            '#markup'      => $this->store->get('permanentaddress2'),
            '#prefix'      => ' ',
            '#suffix'      => '</div>',
            );
            $permanentstate = $libobj ->getStateNameById($this->store->get('permanentstate'));
            $permanentcity = $libobj ->getCityNameById($this->store->get('permanentcity'));

            $form['employee']['permanentstate'] = array(
            '#type'            => 'item',
            '#title'            => $this->t('State:'),
            '#markup'      => $permanentstate,
            '#prefix'      => '<div class="row">',
            '#suffix'      => '',
            );
            $form['employee']['permanentcity'] = array(
            '#type'            => 'item',
            '#title'            => $this->t('City:'),
            '#markup'      => $permanentcity,
            '#prefix'      => ' ',
            '#suffix'      => '</div>',
            );
            $form['employee']['permanentpincode'] = array(
            '#type'            => 'item',
            '#title'            => $this->t('Pincode:'),
            '#markup'      => $this->store->get('permanentpincode'),
            '#prefix'      => '<div class="row">',
            '#suffix'      => '',
            );
            $form['employee']['permanentcountry'] = array(
            '#type'            => 'item',
            '#title'            => $this->t('Country:'),
            '#markup'      => $this->store->get('permanentcountry'),
            '#prefix'      => ' ',
            '#suffix'      => '</div>',
            );

        }
        //***************************************************************

        $rows = [];
        foreach($this->store->get('qualification') AS $item)
        {
            $rows[] = [
            $item['class'], $item['stream'], $item['university'], $item['yearofpassing'], $item['score'] . ' %'
                  ];
        }

        $form['employee']['qual'] = array(
        '#type'         => 'table',
        '#header'     =>  array(t('Class'), t('Stream'),t('University'), t('Passing Year'), t('Score')),
        '#rows'        =>  $rows,
        '#attributes' => ['class' => ['table text-center table-hover table-striped table-bordered dataTable'], 'border' => '1', 'rules' => 'all', 'style'=>['text-align-last: center;']],
        '#prefix'      => '</br> </br> <h3 class="box-title">Academic Info <div class="pull-right">
                          <a href="'.$base_url.'/employee/add/academics"><i class="mdi mdi-pencil-circle" title="" style="font-size: x-large;"data-toggle="tooltip" data-original-title="Edit"></i></a>
                          </div> </h3><hr class="m-t-0 m-b-40"><div class="row">',
        '#empty'        =>    'No Qualification details are available'
        );


        $rows = [];
        foreach($this->store->get('experience') AS $item)
        {
            $rows[] = [
            $item['organisation'], $item['designation'], $item['fromdate'], $item['todate']
                  ];
        }

        $form['employee']['expr'] = array(
        '#type'         => 'table',
        '#header'     =>  array(t('Organisation'), t('Designation'),t('From Date'), t('To Date')),
        '#rows'        =>  $rows,
        '#attributes' => ['class' => ['table text-center table-hover table-striped table-bordered dataTable'], 'border' => '1', 'rules' => 'all', 'style'=>['text-align-last: center;']],
        '#prefix'      => '</br> </br>
                        <h3 class="box-title">Previous Employement info</h3>
                        <hr class="m-t-0 m-b-40"><div class="row">',
        '#empty'        =>    'No Employment Details are Available'
        );

        //***************************************************************

        $form['employee']['id'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Employee ID:'),
        '#markup'      => $this->store->get('id'),
        '#prefix'      => '<br/><br/><h3 class="box-title">Official Info <div class="pull-right">
                        <a href="'.$base_url.'/employee/add/official"><i class="mdi mdi-pencil-circle" title="" style="font-size: x-large;"data-toggle="tooltip" data-original-title="Edit"></i></a>
                        </div> </h3><hr class="m-t-0 m-b-40"><div class="row">',
        '#suffix'      => '',
        );

        $result = $brnobj->getBranchNameFromCode($this->store->get('branch'));
        $branch_name = $result->codevalues;

        $form['employee']['branch'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Branch:'),
        '#markup'      => $branch_name,
        '#prefix'      => '',
        '#suffix'      => '</div>',
        );

        $result = $desgnobj->getDesignationNameFromCode($this->store->get('designation'));
        $designation_name = $result->codevalues;

        $form['employee']['designation'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Designation:'),
        '#markup'      => $designation_name,
        '#prefix'      => '<div class="row">',
        '#suffix'      => '',
        );

        $result = $deptobj->getDepartmentNameFromCode($this->store->get('department'));
        $department_name = $result->codevalues;

        $form['employee']['department'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Department:'),
        '#markup'      => $department_name,
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );
        $form['employee']['role'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Role:'),
        '#markup'      => $this->store->get('role'),
        '#prefix'      => '<div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['officialemail'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Email:'),
        '#markup'      => $this->store->get('officialemail'),
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );
        $jobnature = $libobj->getCodeValues('jobnature', $this->store->get('jobnature'));

        $form['employee']['jobnature'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Nature of job:'),
        '#markup'      => $jobnature,
        '#prefix'      => '<div class="row">',
        '#suffix'      => '',
        );
        $form['employee']['doj'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Date of joining:'),
        '#markup'      => date("j F Y", strtotime($this->store->get('doj'))),
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );
        $jobtype = $libobj->getCodeValues('jobtype', $this->store->get('jobtype'));

        $form['employee']['jobtype'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Job Type:'),
        '#markup'      => $jobtype,
        '#prefix'      => '<div class="row">',
        '#suffix'      => '',
        );
        $jobshift = $libobj->getCodeValues('jobshift', $this->store->get('shifttime'));

        $form['employee']['jobshift'] = array(
        '#type'            => 'item',
        '#title'            => $this->t('Job Shift:'),
        '#markup'      => $jobshift,
        '#prefix'      => ' ',
        '#suffix'      => '</div>',
        );

        //***************************************************************


        $form['employee']['cancel'] = [
          '#title' => $this->t('Cancel'),
          '#type' => 'link',
          '#attributes' => ['class' => ['btn btn-default'],'style' => ['margin-left: 35%']],
          '#url' => \Drupal\Core\Url::fromRoute('employee.emplist'),
          '#prefix' => '<div class="form-actions"><div class="row"><hr class="m-t-0 m-b-40">
                    <div class="col-md-9"><div class="row"><div class="col-md-offset-3 col-md-9">',
        ];

        $form['employee']['#type'] = 'actions';
        $form['employee']['submit'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
        '#button_type' => 'primary',
        '#attributes' => ['class' => ['btn btn-success'],'style' => ['margin-left: 5%']],
        '#suffix' => '</div></div><div class="col-md-6"></div></div></div></div>',
        );

        return $form;
    }
    public function validateForm(array &$form, FormStateInterface $form_state)
    {


    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

        parent::saveData();
    }



}
