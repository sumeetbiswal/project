<?php

namespace Drupal\company\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;

/**
 * Model file for the Designation.
 */
class DesignationModel extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * Constructor for the DesignationModel.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Helper function to get Designation details By ID.
   */
  public function getDesignationDetailsById($id = 1) {
    $query = $this->connection->select('srch_codevalues', 'n');
    $query->fields('n');
    $query->condition('codetype', 'designation', "=");
    $query->condition('codepk', $id, "=");
    $query->condition('status', 1, "=");
    $result = $query->execute()->fetchAll();

    $res = @$result[0];
    return $res;
  }

  /**
   * Helper function to create designation.
   */
  public function setDesignation($field) {

    $this->connection->insert('srch_codevalues')
      ->fields($field)
      ->execute();
  }

  /**
   * Helper function to update Designation.
   */
  public function updateDesignation($field, $id) {

    $this->connection->update('srch_codevalues')
      ->fields($field)
      ->condition('codepk', $id)
      ->execute();
  }

  /**
   * Helper function to get all Designation details.
   */
  public function getAllDesignationDetails() {
    $query = $this->connection->select('srch_codevalues', 'n');
    $query->fields('n');
    $query->condition('status', 1, "=");
    $query->orderBy('createdon', 'DESC');
    $query->condition('codetype', 'designation', "=");
    $result = $query->execute()->fetchAll();

    return $result;
  }

  /**
   * Helper function to get Designation list.
   */
  public function getDesignationList($department) {
    if (empty($department)) {
      $res1[''] = 'Select Designation';
      return $res1;
    }

    // Get codepk from dept code.
    $query_dpt = $this->connection->select('srch_codevalues', 'n');
    $query_dpt->fields('n');
    $query_dpt->condition('codetype', 'department', "=");
    $query_dpt->condition('codename', $department, "=");
    $dept_pk = $query_dpt->execute()->fetch();

    // Get designation list.
    $query = $this->connection->select('srch_codevalues', 'n');
    $query->fields('n');
    $query->condition('codetype', 'designation', "=");
    $query->condition('parent', $dept_pk->codepk, "=");
    $result1 = $query->execute()->fetchAll();

    $res1[''] = 'Select Designation';
    foreach ($result1 as $val1) {
      $res1[$val1->codename] = $val1->codevalues;
    }
    return $res1;
  }

  /**
   * Get designation name from designation code.
   *
   * @input designation code
   * @output designation name
   */
  public function getDesignationNameFromCode($designationcode) {
    $query = $this->connection->select('srch_codevalues', 'n');
    $query->fields('n');
    $query->condition('codetype', 'designation', "=");
    $query->condition('codename', $designationcode, "=");
    $query->condition('status', 1, "=");
    $result = $query->execute()->fetch();

    return $result;
  }

}
