<?php

namespace Drupal\company\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

class CompanyController extends ControllerBase {

  public function display() {

	global $base_url;
  $compobj = \Drupal::service('company.service');
	$data = $compobj->getCompanyDetailsById(1);
	$encrypt = new \Drupal\library\Controller\Encrypt;
	global $base_url;
	$asset_url = $base_url.'/'.\Drupal::theme()->getActiveTheme()->getPath();
  $comp_details = [];
  if(!empty($data)){
    $comp_details = array(
      'logo' => file_create_url("public://logo.png"),
      'name' => $data->companyname,
      'type' => $data->codevalues,
      'email' => $data->email,
      'phone' => $data->phone,
      'address'=> $data->address1 . ', ' . $data->address2 .', '. $data->cityname .', '. $data->statename .', '. $data->countryname .', '. $data->pincode,
      'id'     => $encrypt->encode($data->companypk)
    );
  }
	 return array(
    '#theme'  => 'companyview',
	  '#prefix' => '<div class="panel panel-info"> <h3 class="box-title  col-md-10">Company Details</h3> 
                  <div class=" col-md-2"> <a id="printit" data-toggle="tooltip" data-original-title="Print"><img src="'.$asset_url.'/assets/images/icon/print.png" /></a>
						      </div></div>',

    '#data' => $comp_details,
    );
  }
}