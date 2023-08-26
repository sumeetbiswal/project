<?php

namespace Drupal\leave\Model;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\library\Lib\DataModel;
use Symfony\Component\DependencyInjection\ContainerInterface;


class LeavesModel extends ControllerBase {

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

	public function setLeaveType($field)
	{
		$query = \Drupal::database();
           $query ->insert(DataModel::CODEVAL)
               ->fields($field)
               ->execute();
	}

  public function getLeaveTypeList(){
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('codetype', 'leavetype', "=");
    $query->orderBy('createdon', 'DESC');
    $result = $query->execute()->fetchAll();
    return $result;
  }

  public function getLeaveTypeDetailsById($pk){
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('codetype', 'leavetype', "=");
    $query->condition('codepk', $pk, "=");
    $result = $query->execute()->fetch();

    return $result;
  }

  public function updateLeaveType($data, $pk){
    $query = \Drupal::database();
    $query->update(DataModel::CODEVAL)
      ->fields($data)
      ->condition('codepk', $pk)
      ->execute();
  }
}
