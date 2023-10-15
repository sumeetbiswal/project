<?php

namespace Drupal\company\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\library\Lib\DataModel;

/**
 * WorkorderModel creates database services.
 */
class WorkorderModel extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * WorkorderModel constructors.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Helper function to get Work Order List.
   */
  public function getWorkorderList() {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('n.codetype', 'workorder', "=");
    $query->condition('n.status', 1, "=");
    $query->orderBy('createdon', 'DESC');
    $result = $query->execute()->fetchAll();

    $res = @$result;

    return $res;
  }

  /**
   * Helper function to get Work Order details by ID.
   */
  public function getWorkorderById($pk) {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('n.codetype', 'workorder', "=");
    $query->condition('n.codepk', $pk, "=");
    $result = $query->execute()->fetch();

    return $result;
  }

  /**
   * Helper function to create workorder.
   *
   * @param string $data
   *   Array which includes work & team details.
   *   which needs to be insert in srch_codevalues table.
   */
  public function setWorkOrder($data) {
    $data['codetype'] = 'workorder';

    $this->connection->insert(DataModel::CODEVAL)
      ->fields($data)
      ->execute();
  }

  /**
   * Helper function to update updateWorkorder.
   */
  public function updateWorkorder($data, $pk) {

    $this->connection->update(DataModel::CODEVAL)
      ->fields($data)
      ->condition('codepk', $pk)
      ->execute();
  }

  /**
   * Helper function to create Team order.
   */
  public function setTeamOrder($data) {
    // Inserting column codetype & parent.
    $data['codetype']   = 'teamorder';

    $this->connection->insert(DataModel::CODEVAL)
      ->fields($data)
      ->execute();

  }

  /**
   * Helper function to update Team order.
   */
  public function updateTeamorder($data, $pk) {
    // Inserting column codetype & parent.
    $data['codetype']   = 'teamorder';

    $this->connection->update(DataModel::CODEVAL)
      ->fields($data)
      ->condition('codepk', $pk)
      ->execute();

  }

  /**
   * Helper function to get Team list from Project won number.
   *
   * @param string $won
   *   Project won number
   *   Static Query
   *   SELECT cd1.codepk, cd1.codetype, cd1.codename, cd1.codevalues,
   *   cd2.codevalues FROM `srch_codevalues` cd1
   *   LEFT JOIN `srch_codevalues` cd2 ON cd1.parent = cd2.codepk
   *   WHERE cd1.codetype='teamorder' AND cd2.codename=<codename>;.
   */
  public function getTeamListByWorkOrder($won = '') {

    $query = $this->connection->select(DataModel::CODEVAL, 'cd1');
    $query->leftJoin(DataModel::CODEVAL, 'cd2', 'cd1.parent = cd2.codepk');
    $query->fields('cd1', ['codepk']);
    $query->fields('cd1', ['codetype']);
    $query->fields('cd1', ['codename']);
    $query->fields('cd1', ['codevalues']);
    $query->fields('cd2', ['codevalues']);

    $query->condition('cd1.codetype', 'teamorder', "=");
    $query->condition('cd2.codename', $won, "=");

    $result = $query->execute()->fetchAll();
    $res = [];
    foreach ($result as $val) {
      $res[$val->codename] = $val->codevalues;
    }

    return $res;
  }

  /**
   * Helper function to get Team Order details by ID.
   */
  public function getTeamorderById($pk) {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('n.codetype', 'teamorder', "=");
    $query->condition('n.codepk', $pk, "=");
    $result = $query->execute()->fetch();

    return $result;
  }

}
