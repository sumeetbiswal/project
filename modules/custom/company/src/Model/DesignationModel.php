<?php

namespace Drupal\company\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DesignationModel extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * @param \Drupal\Core\Database\Connection $connection
   *  The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }	
	public function getDesignationDetailsById($id = 1)
	{
		$query = $this->connection->select('srch_codevalues', 'n');
				$query->fields('n');	
				$query->condition('codetype', 'designation', "=");
				$query->condition('codepk', $id, "=");
				$query->condition('status', 1, "=");
				$result = $query->execute()->fetchAll();
		
		$res = @$result[0];	
		return $res;
	}
	
	public function setDesignation($field)
	{
		$query = \Drupal::database();
           $query ->insert('srch_codevalues')
               ->fields($field)
               ->execute();
	}
	
	public function updateDesignation($field, $id)
	{
		$query = \Drupal::database();
          $query->update('srch_codevalues')
              ->fields($field)
              ->condition('codepk', $id)
              ->execute();
	}

 	public function getAllDesignationDetails() {
	$query = $this->connection->select('srch_codevalues', 'n');
    $query->fields('n');    
    $query->condition('status', 1, "=");
    $query->orderBy('createdon', 'DESC');
    $query->condition('codetype', 'designation', "=");
    $result = $query->execute()->fetchAll();
    
    return $result;
  } 
  

	public function getDesignationList($department)
	{
		if(empty($department))
		{
			$res1[''] = 'Select Designation';
			return $res1;
		}
		
		//get codepk from dept code
		$query_dpt = $this->connection->select('srch_codevalues', 'n');
		$query_dpt->fields('n');	
		$query_dpt->condition('codetype', 'department', "=");
		$query_dpt->condition('codename', $department, "=");
		$dept_pk = $query_dpt->execute()->fetch();
		
		
		//get designation list
		$query = $this->connection->select('srch_codevalues', 'n');
		$query->fields('n');	
		$query->condition('codetype', 'designation', "=");
		$query->condition('parent', $dept_pk->codepk, "=");
		$result1 = $query->execute()->fetchAll();
		
		$res1[''] = 'Select Designation';
		foreach($result1 AS $val1)
		{
			$res1[$val1->codename] = $val1->codevalues;
		}
		return $res1;
	}

	/*
	* get designation name from designation code
	* @input designation code
	* @output designation name
	*/
	public function getDesignationNameFromCode($designationcode)
	{
		$query = $this->connection->select('srch_codevalues', 'n');
				$query->fields('n');	
				$query->condition('codetype', 'designation', "=");
				$query->condition('codename', $designationcode, "=");
				$query->condition('status', 1, "=");
				$result = $query->execute()->fetch();
		
		return $result;
	}

}
