<?php

namespace Drupal\employee\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\library\Controller\Encrypt;
use Drupal\employee\Model\TaggingModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\Element\EntityAutocomplete;

class Employee extends ControllerBase {

 public function emplist() {


  $empobj = \Drupal::service('employee.service');
  $result = $empobj->getEmployeeList();
  $encrypt = new Encrypt;

    global $base_url;
    $asset_url = $base_url.'/'.\Drupal::theme()->getActiveTheme()->getPath();
	$rows = [];
    foreach ($result as $row => $content) {
	  $codepk_encoded = $encrypt->encode($content->userpk);
      $html = ['#markup' => '<a href="'.$base_url.'/employee/edit/'.$codepk_encoded.'" style="text-align:center">
      <i class="icon-note" title="" data-toggle="tooltip" data-original-title="Edit"></i></a>'];
      $rows[] = 	array(
                    'data' =>	  array( $content->empid, $content->firstname.' '.$content->lastname , $content->doj, $content->designation, $content->department, render($html))
      );
    }
    $element['display']['employeelist'] = array(
      '#type' 	    => 'table',
      '#header' 	  =>  array(t('Employee ID.'), t('Name'),t('Date of joining'), t('Designation'),  t('Department'), t('Action')),
      '#rows'		    =>  $rows,
      '#empty'		=>	'No Employee has been added yet.'
    );
    return $element;
  }
  public function exportToExcel()
	 {
		 $xcel = new \Drupal\library\Controller\Excel;
		 $empobj = new TaggingModel;
		 $result = $empobj->getEmployeeList();
		 $user = \Drupal::currentUser();
		 $emp_details = $empobj->getEmployeeDetails();
		 //$headings = "SLNO" . "\t" . "Branch Name" . "\t" . "State" . "\t" . "City" . "\t" . "Location" . "\t" . "Pincode" . "\t";
		 $headings = ['Name', 'LastName', 'FatherName', 'mothername', 'DOB', 'Gender', 'marital', 'Blood Group', 'Religion', 'Nationality','PhoneNumber', 'AlternatePhoneNumber', 'EmergencyPhoneNumber', 'Relationship', 'Email', 'ResidentAddress1', 'ResidentAddress2', 'State', 'City', 'Country', 'Pincode', 'PermenantAddress1', 'PermenantAddress2', 'State', 'City', 'Country', 'Pincode', 'EmployeeID', 'Branch', 'Department', 'Designation', 'JobType', 'JobNature', 'Email', 'DateofJoining','ShiftTime'];
		 $dataRow = array();
		 $dataRow = array($headings);
		 foreach($result AS $item)
		 {

			 $dataRow[] = array(
								'firstname'	=> 	$emp_details->FirstName,
								'lastname'		=> 	$emp_details->LastName,
						        'fathername'	=> 	$emp_details->FatherName,
						        'mothername'	=> 	$emp_details->MotherName,
						        'dob'			=> 	date("j F Y", strtotime($emp_details->DateOfBirth)),
								'gender'		=> 	($emp_details->Gender == 'M') ? 'Male' : 'Female',
						        'marital'		=> 	($emp_details->Marital == 'M') ? 'Married' : 'Unmarried',
						        'bloodgroup'	=> 	$emp_details->BloodGroup,
                                'religion'		=> 	$emp_details->Religion,
						        'nationality'	=> 	$emp_details->Nationality,

						        'phoneno'		=>	$emp_details->PhoneNumber,
						        'altphone'		=>	$emp_details->AlternatePhoneNumber,
						        'emrgphone'		=>	$emp_details->EmergencyPhoneNumber,
						        'relationship'	=>	$emp_details->Relationship,
						        'pers_email'	=>	$emp_details->Email,
						        'res_address1'	=>	$emp_details->ResidentAddress1,
						        'res_address2'	=>	$emp_details->residentAddress2,
						        'res_state'		=>	$emp_details->State,
						        'res_city'		=>	$emp_details->City,
						        'res_country'	=>	$emp_details->Country,
						        'res_pincode'	=>	$emp_details->Pincode,
						        'perm_address1'	=>	$emp_details->PermenantAddress1,
						        'perm_address2'	=>	$emp_details->PermenantAddress2,
						        'perm_state'	=>	$emp_details->s1_name,
						        'perm_city'		=>	$emp_details->ct1_name,
						        'perm_country'	=>	$emp_details->co1_name,
						        'perm_pincode'	=>	$emp_details->c_perm_pincode,

						        'empid'			=>  $emp_details->EmployeeID,
						        'branch'		=>	$emp_details->Branch,
						        'department'	=>	$emp_details->Department,
						        'designation' 	=>  $emp_details->Designation,
						        'jobtype' 		=> 	$emp_details->JobType,
								'jobnature' 	=> 	$emp_details->JobNature,
								'email' 		=> 	$emp_details->o_email,
								'joining' 		=> 	date("j F Y", strtotime($emp_details->DateofJoining)),
								'jobshift' 		=> 	$emp_details->ShiftTime ,

							);


		 }
		$filename = 'employee_details_'.date('ymds');
		$result = $xcel->generateExcel($filename, $dataRow);

	 }

/*
* Display Employee Profile
* @parameter Logged in User
* @output passing data variable to template file
*/

  public function profile() {
	$empobj = \Drupal::service('employee.service');

	$avatar = $empobj->getUserPic();
	$user = \Drupal::currentUser();
	$prsnl_details = $empobj->getPersonalDetailsById($user->id());
	$ofc_details = $empobj->getOfficialDetailsById($user->id());
	$cont_details = $empobj->getContactDetailsById($user->id());
	$academic_details = $empobj->getAcademicDetailsById($user->id());
	$academic = [];
	foreach($academic_details AS $val)
	{
		$academic[$val->class] = $val->board . ' ('. date("Y", strtotime($val->yearofpassing)) . ')';
	}

	$prevEmp_details = $empobj->getPrevEmployeementDetailsById($user->id());

	$expr = [];
	foreach($prevEmp_details AS $item)
	{
		$expr[] = [
					'organisation'	=>	$item->organisation,
					'designation'	=>	$item->designation,
					'fromdate'		=>	date("j F Y", strtotime($item->fromdate)),
					'todate'		=>	date("j F Y", strtotime($item->todate)),
				  ];
	}

	switch($prsnl_details->gender)
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

	return array(
      '#theme' => 'employee-profile',
      '#data' => array(
						'profpic'		=>	$avatar,
						'name'			=> 	$prsnl_details->firstname . ' ' . $prsnl_details->lastname,
						'fathername'	=> 	$prsnl_details->fathername,
						'mothername'	=> 	$prsnl_details->mothername,
						'dob'			=> 	date("j F Y", strtotime($prsnl_details->dob)),
						'marital'		=> 	($prsnl_details->marital == 'M') ? 'Married' : 'Unmarried',
						'bloodgroup'	=> 	$prsnl_details->bloodgroup,
						'religion'		=> 	$prsnl_details->religion,
						'nationality'	=> 	$prsnl_details->nationality,
						'gender'		=> 	$gender,

						'phoneno'		=>	$cont_details->phoneno,
						'altphone'		=>	$cont_details->altphone,
						'emrgphone'		=>	$cont_details->emrgphone,
						'relationship'	=>	$cont_details->relationship,
						'pers_email'	=>	$cont_details->email,
						'res_address1'	=>	$cont_details->res_address1,
						'res_address2'	=>	$cont_details->res_address2,
						'res_state'		=>	$cont_details->res_state,
						'res_city'		=>	$cont_details->res_city,
						'res_country'	=>	$cont_details->res_country,
						'res_pincode'	=>	$cont_details->res_pincode,
						'perm_address1'	=>	$cont_details->perm_address1,
						'perm_address2'	=>	$cont_details->perm_address2,
						'perm_state'	=>	$cont_details->perm_state,
						'perm_city'		=>	$cont_details->perm_city,
						'perm_country'	=>	$cont_details->perm_country,
						'perm_pincode'	=>	$cont_details->perm_pincode,

						'empid'			=> 	$ofc_details->empid,
						'branch'		=>	$ofc_details->branch,
						'department'	=>	$ofc_details->department,
						'designation' 	=> 	$ofc_details->designation,
						'jobtype' 		=> 	$ofc_details->jobtype,
						'jobnature' 	=> 	$ofc_details->jobnature,
						'email' 		=> 	$ofc_details->email,
						'joining' 		=> 	date("j F Y", strtotime($ofc_details->joining)),
						'jobshift' 		=> 	$ofc_details->jobshift ,

						'qual'			=>	$academic,
						'expr'			=>	$expr
				),
    );

  }

  /**
   * Handler for autocomplete request.
   */
  public function employeeAutocomplete(Request $request){

    $results = [];
    $input = $request->query->get('q');

    // Get the typed string from the URL, if it exists.
    if (!$input) {
      return new JsonResponse($results);
    }

    $input = Xss::filter($input);

    $empobj = \Drupal::service('employee.service');
    $result = $empobj->getEmployeeListAutoComplete($input);
  
    foreach($result AS $key => $item){
      $results[] = [
        'value' => $item->userpk,
        'label' => $item->firstname . ' ' . $item->lastname . ' (' . $item->empid. ')',
      ];
    }

    return new JsonResponse($results);
  }
}
