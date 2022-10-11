<?php

namespace Drupal\library\Lib;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\library\Lib\DataModel;

class LibController extends ControllerBase {
	
	public function getActionMode()
	{
		$current_path = \Drupal::request()->getPathInfo();
		$path = explode('/', $current_path);
		return $path[2];		
	}
	
	public function getIdFromUrl()
	{
		
		$current_path = \Drupal::request()->getPathInfo();
		$path = explode('/', $current_path);
		return $path[3];		
	}
	
	public function getStateList()
	{
		$query = db_select(DataModel::STATE, 'n');
				$query->fields('n');	
				$query->condition('name', "", "!=");
				$query->condition('country_id', "101", "=");
				$query->orderBy('name', "ASC");
				$result = $query->execute()->fetchAll();
		$res[''] = 'Select State';
		foreach($result AS $val)
		{
			$res[$val->id] = $val->name;
		}
			
		
		return $res;
	}
	
	public function getStateNameById($id)
	{
		$query = db_select(DataModel::STATE, 'n');
				$query->fields('n');	
				$query->condition('id', $id ,"=");
        $result = $query->execute()->fetchAll();
        foreach ($result as $row => $content) {
          $state_name = $content->name;
        }
			
		return $state_name;
	}	
  
	public function getCityListByState($statePk)
	{
		$query = db_select(DataModel::CITY, 'n');
				$query->fields('n');	
				$query->condition('name', "", "!=");
				$query->condition('state_id', $statePk, "=");
				$query->orderBy('name', "ASC");
				$result = $query->execute()->fetchAll();
		$res[''] = 'Select City';
		foreach($result AS $val)
		{
			$res[$val->id] = $val->name;
		}
			
		return $res;
	}
	public function getCityNameById($id)
	{
		$query = db_select(DataModel::CITY, 'n');
				$query->fields('n');	
				$query->condition('id', $id ,"=");
				$result = $query->execute()->fetchAll();
		      foreach ($result as $row => $content) {
            $city_name = $content->name;
       }
			
		return $city_name;
	}
	
	public function getCountryNameById($id)
	{
		$query = db_select(DataModel::COUNTRY, 'n');
				$query->fields('n');	
				$query->condition('id', $id ,"=");
				$result = $query->execute()->fetchAll();
		      foreach ($result as $row => $content) {
            $cntry_name = $content->name;
       }
			
		return $cntry_name;
	}
	
	public function getCodeValues($codetype, $codename)
	{
		$query = db_select(DataModel::CODEVAL, 'n');
				$query->fields('n');	
				$query->condition('codename', $codename ,"=");
				$query->condition('codetype', $codetype ,"=");
				$result = $query->execute();
		    foreach ($result as $row => $content) {
            $codevalue = $content->codevalues;
       }
			
		return $codevalue;
	}
	
  public function getRoles()
  {
		$roles = \Drupal::entityTypeManager()->getStorage('user_role')->loadMultiple();
		foreach($roles AS $val){
		 $exclude = array('anonymous', 'administrator');
		 if(in_array($val->id(), $exclude)) continue;
		 $roleList[$val->id()] = $val->label();
		}
		 return $roleList;
  }
  
  /*
  * Generating code for codevalues based upon entity type
  * @1st paramter entity type
  * @2nd paramter string inputed by user whcih to be used in
  * generating code
  */
  public function generateCode($codephrase, $string)
  {
		$codename = '';
		
		for($i = 3; $i <= 8; $i++ )
		{
			$extract_subcode_from_string = substr($string, 0, $i);
			$codename = strtoupper($codephrase.$extract_subcode_from_string);
			
			$query = db_select(DataModel::CODEVAL, 'n'); 
			$query->fields('n')		
			->condition('codename', $codename, "=");
			$result = $query->execute()->fetch();
			if(empty($result))	break;
		}
		
		
		return $codename;
  }
		
   /*
	* Generate default password
	* Parameters required @string , @date of birth
	* @return string@date_of_birth
   */
   
   public function generateDefaultPassword($str, $dob)
   {
	   $dob_split_arr = explode('/', $dob);
	   $dob_merge_to_string = implode('', $dob_split_arr);
	   
	   $password = $str.'@'.$dob_merge_to_string;
	   
	   return $password;
   }
   
   /*
	* change the dateformat to Db date format
	* Parameters required UI @date string
	* @return DB date format
   */
   
   public function getDbDateFormat($dt)
   {
	   $explode_dt = explode('/', $dt);
	   $dbDate = $explode_dt[2].'-'.$explode_dt[1].'-'.$explode_dt[0];
	   return $dbDate;
   }

}
