<?php

namespace Drupal\company\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

class BranchModel extends ControllerBase {
	
	public function getBranchDetailsById($id = 1)
	{
		$query = db_select('srch_codevalues', 'n');
				$query->fields('n');	
				$query->condition('codetype', 'branch', "=");
				$query->condition('codepk', $id, "=");
				$query->condition('status', 1, "=");
				$result = $query->execute()->fetchAll();
		
		$res = @$result[0];	
		return $res;
	}
  
    public function getAllBranchDetails()
	{
		
		$query = db_select('srch_codevalues', 'n');
		$query->leftJoin('srch_cities', 'ct', 'n.city = ct.id'); 
		$query->leftJoin('srch_states', 'st', 'n.state = st.id'); 
		$query->fields('n');	
		$query->fields('st',['name']);	
		$query->fields('ct',['name']);	
		$query->condition('status', 1, "=");
		$query->condition('codetype', 'branch', "=");
		$query->orderBy('createdon', 'DESC');
		$result = $query->execute()->fetchAll();
		return $result;
	}
	
	public function setBranch($field)
	{
		$query = \Drupal::database();
           $query ->insert('srch_codevalues')
               ->fields($field)
               ->execute();
	}
	
	public function updateBranch($field, $id)
	{
		$query = \Drupal::database();
          $query->update('srch_codevalues')
              ->fields($field)
              ->condition('codepk', $id)
              ->execute();
	}
	
	/*
	* get branch name from branch code
	* @input branch code
	* @output Branch name
	*/
	public function getBranchNameFromCode($branchcode)
	{
		$query = db_select('srch_codevalues', 'n');
				$query->fields('n');	
				$query->condition('codetype', 'branch', "=");
				$query->condition('codename', $branchcode, "=");
				$query->condition('status', 1, "=");
				$result = $query->execute()->fetch();
		
		return $result;
	}
}
