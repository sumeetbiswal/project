<?php

namespace Drupal\leave\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\library\Lib\DataModel;

/**
 * Model file for the Leave.
 */
class LeavesModel extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * Constructor for the LeavesModel.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Helper function to create the leaveType.
   */
  public function setLeaveType($field) {
    $this->connection->insert(DataModel::CODEVAL)
      ->fields($field)
      ->execute();
  }

  /**
   * Helper function to get the leave type list.
   */
  public function getLeaveTypeList() {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('codetype', 'leavetype', "=");
    $query->orderBy('createdon', 'DESC');
    $result = $query->execute()->fetchAll();
    return $result;
  }

  /**
   * Helper function to get the leave type details from ID.
   */
  public function getLeaveTypeDetailsById($pk) {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('codetype', 'leavetype', "=");
    $query->condition('codepk', $pk, "=");
    $result = $query->execute()->fetch();

    return $result;
  }

  /**
   * Helper function to update the leave type.
   */
  public function updateLeaveType($data, $pk) {
    $this->connection->update(DataModel::CODEVAL)
      ->fields($data)
      ->condition('codepk', $pk)
      ->execute();
  }

}
