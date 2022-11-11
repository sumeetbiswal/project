<?php

namespace Drupal\company\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\library\Lib\DataModel;

class ConfigurationModel extends ControllerBase {

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

	public function getJobNature()
	{
		$query = $this->connection->select(DataModel::CODEVAL, 'n');
		$query->fields('n');
		$query->condition('status', 1, "=");
		$query->condition('codetype', 'jobnature', "=");
		$query->orderBy('createdon', 'DESC');
		$result = $query->execute();

		$natureofjob[''] = 'Select Nature of job';
		 foreach($result AS $item)
		 {
		  $natureofjob[$item->codename]  = $item->codevalues;
		 }

		return $natureofjob;
	}

	public function getJobType()
	{
		$query = $this->connection->select(DataModel::CODEVAL, 'n');
		$query->fields('n');
		$query->condition('status', 1, "=");
		$query->condition('codetype', 'jobtype', "=");
		$query->orderBy('createdon', 'DESC');
		$result = $query->execute();
		$jobtype[''] = 'Select Type of job';
		foreach($result AS $item)
		{
			$jobtype[$item->codename]  = $item->codevalues;
		}
		return $jobtype;
	}

	public function getJobShift()
	{
		$query = $this->connection->select(DataModel::CODEVAL, 'n');
		$query->fields('n');
		$query->condition('status', 1, "=");
		$query->condition('codetype', 'jobshift', "=");
		$query->orderBy('createdon', 'DESC');
		$result = $query->execute()->fetchAll();
		$jobshift[''] = 'Select Shift type';
		 foreach($result AS $item)
		 {
		   $jobshift[$item->codename]  = $item->codevalues;
		 }
		return $jobshift;
	}

     /*
	 * @param $field is an array of field ON/OFF value
	 * needs to udpate on DB srch_codevalues
	 */
	public function updatAllConfig($field)
	{
		foreach( $field AS $item)
		{
			$query = \Drupal::database();
			$query->update(DataModel::CODEVAL)
              ->fields($item)
              ->condition('codename', $item['codename'], "=")
              ->condition('codetype', $item['codetype'], "=")
              ->execute();

		}
	}

	public function getEmpIdType()
	{
		$query = $this->connection->select(DataModel::CODEVAL, 'n');
		$query->fields('n')
		->condition('codename', 'EMPID', "=")
        ->condition('codetype', 'employeeid', "=");
		$result = $query->execute()->fetch();
		$res = @$result;

		return $res;
	}


	/*
	* This checks the Employee id type configuration
	* setup  in Administrative --> configuration
	*/
	public function getEmployeeIdConfig()
	{
		$query = $this->connection->select(DataModel::CODEVAL, 'n');
		$query->fields('n')
		->condition('codename', 'EMPID', "=")
        ->condition('codetype', 'employeeid', "=");
		$result = $query->execute()->fetch();

		$res = [];
		if($result->codevalues == 'Automatic')
		{
			$res['disabled'] = 'disabled';
			$res['empid'] = $result->description . 'XXXX';
			$res['helpmsg'] = 'Employee ID will be auto generate';
		}
		else
		{
			$res['disabled'] = '';
			$res['empid'] = '';
			$res['helpmsg'] = 'Mention Employee Id of the person';
		}

		return $res;
	}

	/*
	* @return Branch Code Toggle On/Off
	*/
	public function getBranchCodeConfig()
	{
		$query = $this->connection->select(DataModel::CODEVAL, 'n');
		$query->fields('n')
		->condition('codename', 'BRNCD', "=")
        ->condition('codetype', 'branchcode', "=");
		$result = $query->execute()->fetch();

		return $result;
	}

	/*
	* @return Department Code Toggle On/Off
	*/
	public function getDepartmentCodeConfig()
	{
		$query = $this->connection->select(DataModel::CODEVAL, 'n');
		$query->fields('n')
		->condition('codename', 'DPTCD', "=")
        ->condition('codetype', 'departmentcode', "=");
		$result = $query->execute()->fetch();

		return $result;
	}

	/*
	* @return Designation Code Toggle On/Off
	*/
	public function getDesignationCodeConfig()
	{
		$query = $this->connection->select(DataModel::CODEVAL, 'n');
		$query->fields('n')
		->condition('codename', 'DSGCD', "=")
        ->condition('codetype', 'designationcode', "=");
		$result = $query->execute()->fetch();

		return $result;
	}

	/*
	* @return Work order Code Toggle On/Off
	*/
	public function getWorkorderCodeConfig()
	{
		$query = $this->connection->select(DataModel::CODEVAL, 'n');
		$query->fields('n')
		->condition('codename', 'WRKCD', "=")
        ->condition('codetype', 'workordercode', "=");
		$result = $query->execute()->fetch();

		return $result;
	}

	public function setShiftTiming($field)
	{
		$query = \Drupal::database();
		 $result =  $query ->insert(DataModel::CODEVAL)
				   ->fields($field)
				   ->execute();
	}

	public function getShiftTimingList()
	{
		$query = $this->connection->select(DataModel::CODEVAL, 'n');
		$query->fields('n');
		$query->condition('status', 1, "=");
		$query->condition('codetype', 'jobshift', "=");
		$query->orderBy('createdon', 'DESC');
		$result = $query->execute()->fetchAll();

		return $result;
	}

	public function getShiftDetailsById($pk)
	{
		$query = $this->connection->select(DataModel::CODEVAL, 'n');
		$query->fields('n');
		$query->condition('codepk', $pk, "=");
		$query->condition('codetype', 'jobshift', "=");
		$result = $query->execute()->fetch();

		return $result;
	}

	public function updateShiftTiming($field, $pk)
	{
		$query = \Drupal::database();
        $query->update(DataModel::CODEVAL)
              ->fields($field)
              ->condition('codepk', $pk, "=")
              ->condition('codetype', 'jobshift', "=")
              ->execute();
	}

}
