<?php

namespace Drupal\company\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

class DepartmentModel extends ControllerBase {
	
	public function getDepartmentDetailsById($id = 1)
	{
		$query = db_select('srch_codevalues', 'n');
				$query->fields('n');	
				$query->condition('codetype', 'department', "=");
				$query->condition('codepk', $id, "=");
				$query->condition('status', 1, "=");
				$result = $query->execute()->fetchAll();
		
		$res = @$result[0];	
		return $res;
	}
	
	public function setDepartment($field)
	{
		$query = \Drupal::database();
           $query ->insert('srch_codevalues')
               ->fields($field)
               ->execute();
	}
	
	public function updateDepartment($field, $id)
	{
		$query = \Drupal::database();
          $query->update('srch_codevalues')
              ->fields($field)
              ->condition('codepk', $id)
              ->execute();
	}

 	public function getAllDepartmentDetails() {
		$query = db_select('srch_codevalues', 'n');
		$query->fields('n');    
		$query->orderBy('createdon', 'DESC');
		$query->condition('status', 1, "=");
		$query->condition('codetype', 'department', "=");
		$result = $query->execute()->fetchAll();
		return $result;
	  }
	public function getDepartmentId($codename)
	{
		$query = db_select('srch_codevalues', 'codepk');
				$query->fields('codepk');	
				$query->condition('status', 1, "=");
				$query->condition('codename', $codename , "=");
				$result = $query->execute()->fetch();
		
		$res = $result;	
		return $res;
	}  
	public function getDepartmentList()
	{
		$query = db_select('srch_codevalues', 'n');
		$query->fields('n');	
		$query->condition('status', 1, "=");
		$query->condition('codetype', 'department', "=");
		$result = $query->execute()->fetchAll();
		$res[' '] = 'Select Department';
		foreach($result AS $val)
		{
			$res[$val->codename] = $val->codevalues;
		}
		
		return $res;
	}
	
	public function deptIsExist($dept_name)
	{
		$query = db_select('srch_codevalues', 'codepk');
				$query->fields('codepk');	
				$query->condition('codevalues', $dept_name, "=");
				$query->condition('codetype', 'department' , "=");
				$result = $query->execute()->fetch();
		
		$res = (empty($result)) ? FALSE : TRUE;
		return $res;
	}
	/*
	* get department name from department code
	* @input department code
	* @output department name
	*/
	public function getDepartmentNameFromCode($departmentcode)
	{
		$query = db_select('srch_codevalues', 'n');
				$query->fields('n');	
				$query->condition('codetype', 'department', "=");
				$query->condition('codename', $departmentcode, "=");
				$query->condition('status', 1, "=");
				$result = $query->execute()->fetch();
		
		return $result;
	}
  
}
