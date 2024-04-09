<?php

namespace Drupal\organisation\Model;

use Drupal\Core\Database\Connection;
use Drupal\Core\Controller\ControllerBase;

/**
 * Model file for the Branch.
 */
class BranchModel extends ControllerBase {
  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * Constructor for the BranchModel.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Helper function to get Branch Details By ID.
   */
  public function getBranchDetailsById($id = 1) {
    $query = $this->connection->select('srch_codevalues', 'n');
    $query->fields('n');
    $query->condition('codetype', 'branch', "=");
    $query->condition('codepk', $id, "=");
    $query->condition('status', 1, "=");
    $result = $query->execute()->fetchAll();

    $res = @$result[0];
    return $res;
  }

  /**
   * Helper function to get all branch list.
   */
  public function getAllBranchDetails() {
    $query = $this->connection->select('srch_codevalues', 'n');
    $query->leftJoin('srch_cities', 'ct', 'n.city = ct.id');
    $query->leftJoin('srch_states', 'st', 'n.state = st.id');
    $query->fields('n');
    $query->fields('st', ['name']);
    $query->fields('ct', ['name']);
    $query->condition('status', 1, "=");
    $query->condition('codetype', 'branch', "=");
    $query->orderBy('createdon', 'DESC');
    $result = $query->execute()->fetchAll();
    return $result;
  }

  /**
   * Helper function to create the Branch.
   */
  public function setBranch($field) {

    $this->connection->insert('srch_codevalues')
      ->fields($field)
      ->execute();
  }

  /**
   * Helper function to update a Branch.
   */
  public function updateBranch($field, $id) {

    $this->connection->update('srch_codevalues')
      ->fields($field)
      ->condition('codepk', $id)
      ->execute();
  }

  /**
   * Get branch name from branch code.
   *
   * @input branch code
   * @output Branch name
   */
  public function getBranchNameFromCode($branchcode) {
    $query = $this->connection->select('srch_codevalues', 'n');
    $query->fields('n');
    $query->condition('codetype', 'branch', "=");
    $query->condition('codename', $branchcode, "=");
    $query->condition('status', 1, "=");
    $result = $query->execute()->fetch();

    return $result;
  }

}
