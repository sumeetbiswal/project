<?php

namespace Drupal\company\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\library\Lib\DataModel;

class WorkorderModel extends ControllerBase {
	
	public function getWorkorderList()
	{
		$query = db_select( DataModel::CODEVAL, 'n');
				$query->fields('n');			
				$query->condition('n.codetype', 'workorder', "=");
				$query->condition('n.status', 1, "=");
				$query->orderBy('createdon', 'DESC');
				$result = $query->execute()->fetchAll();
		
		$res = @$result;	
		
		return $res;
	}
	
	/*
	* @param $data array which includes work & team details
	* which needs to be insert in srch_codevalues table
	*/
	public function setWorkOrder( $data )
	{
		$data['workorder']['codetype']	=	'workorder';
		
		$query = \Drupal::database();
        $last_workorder_id = $query ->insert( DataModel::CODEVAL )
				   ->fields($data['workorder'])
				   ->execute();
		   
		foreach( $data['teamorder']  AS  $team )
		{
			//inserting column codetype & parent
			$team['codetype']	=	'teamorder';
			$team['parent']	=	$last_workorder_id;
			
			$query ->insert( DataModel::CODEVAL )
               ->fields($team)
               ->execute();
		}
	}
}