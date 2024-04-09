<?php

namespace Drupal\organisation\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;

/**
 * Model file for the Department.
 */
class DepartmentModel extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * Constructor for the DepartmentModel.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Helper function to get department details by ID.
   */
  public function getDepartmentDetailsById($id = 1) {
    $query = $this->connection->select('srch_codevalues', 'n');
    $query->fields('n');
    $query->condition('codetype', 'department', "=");
    $query->condition('codepk', $id, "=");
    $query->condition('status', 1, "=");
    $result = $query->execute()->fetchAll();

    $res = @$result[0];
    return $res;
  }

  /**
   * Helper function to create department.
   */
  public function setDepartment($field) {

    $this->connection->insert('srch_codevalues')
      ->fields($field)
      ->execute();
  }

  /**
   * Helper function to update department details.
   */
  public function updateDepartment($field, $id) {

    $this->connection->update('srch_codevalues')
      ->fields($field)
      ->condition('codepk', $id)
      ->execute();
  }

  /**
   * Helper function to get all department details.
   */
  public function getAllDepartmentDetails() {
    $query = $this->connection->select('srch_codevalues', 'n');
    $query->fields('n');
    $query->orderBy('createdon', 'DESC');
    $query->condition('status', 1, "=");
    $query->condition('codetype', 'department', "=");
    $result = $query->execute()->fetchAll();
    return $result;
  }

  /**
   * Helper function to get department ID.
   */
  public function getDepartmentId($codename) {
    $query = $this->connection->select('srch_codevalues', 'codepk');
    $query->fields('codepk');
    $query->condition('status', 1, "=");
    $query->condition('codename', $codename, "=");
    $result = $query->execute()->fetch();

    $res = $result;
    return $res;
  }

  /**
   * Helper function to get department List.
   */
  public function getDepartmentList() {
    $query = $this->connection->select('srch_codevalues', 'n');
    $query->fields('n');
    $query->condition('status', 1, "=");
    $query->condition('codetype', 'department', "=");
    $result = $query->execute()->fetchAll();
    $res[' '] = 'Select Department';
    foreach ($result as $val) {
      $res[$val->codename] = $val->codevalues;
    }

    return $res;
  }

  /**
   * Helper function to check if department exist.
   */
  public function deptIsExist($dept_name) {
    $query = $this->connection->select('srch_codevalues', 'codepk');
    $query->fields('codepk');
    $query->condition('codevalues', $dept_name, "=");
    $query->condition('codetype', 'department', "=");
    $result = $query->execute()->fetch();

    $res = (empty($result)) ? FALSE : TRUE;
    return $res;
  }

  /**
   * Get department name from department code.
   *
   * @input department code
   * @output department name
   */
  public function getDepartmentNameFromCode($departmentcode) {
    $query = $this->connection->select('srch_codevalues', 'n');
    $query->fields('n');
    $query->condition('codetype', 'department', "=");
    $query->condition('codename', $departmentcode, "=");
    $query->condition('status', 1, "=");
    $result = $query->execute()->fetch();

    return $result;
  }

  /**
   * Helper function to get department name from ID.
   */
  public function getDepartmentNameById($codepk) {
    $query = $this->connection->select('srch_codevalues', 'codepk');
    $query->fields('codepk');
    $query->condition('status', 1, "=");
    $query->condition('codepk', $codepk, "=");
    $result = $query->execute()->fetch();

    $res = $result->codevalues;
    return $res;
  }

}
