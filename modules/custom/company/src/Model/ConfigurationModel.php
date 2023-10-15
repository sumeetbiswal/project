<?php

namespace Drupal\company\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\library\Lib\DataModel;

/**
 * Model file for the configuration.
 */
class ConfigurationModel extends ControllerBase {
  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * Constructor for the ConfigurationModel.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Helper function to get Job Nature.
   */
  public function getJobNature() {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('status', 1, "=");
    $query->condition('codetype', 'jobnature', "=");
    $query->orderBy('createdon', 'DESC');
    $result = $query->execute();

    $natureofjob[''] = 'Select Nature of job';
    foreach ($result as $item) {
      $natureofjob[$item->codename] = $item->codevalues;
    }

    return $natureofjob;
  }

  /**
   * Helper function for to get Job type.
   */
  public function getJobType() {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('status', 1, "=");
    $query->condition('codetype', 'jobtype', "=");
    $query->orderBy('createdon', 'DESC');
    $result = $query->execute();
    $jobtype[''] = 'Select Type of job';
    foreach ($result as $item) {
      $jobtype[$item->codename] = $item->codevalues;
    }
    return $jobtype;
  }

  /**
   * Helper function to get Job Shift.
   */
  public function getJobShift() {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('status', 1, "=");
    $query->condition('codetype', 'jobshift', "=");
    $query->orderBy('createdon', 'DESC');
    $result = $query->execute()->fetchAll();
    $jobshift[''] = 'Select Shift type';
    foreach ($result as $item) {
      $jobshift[$item->codename] = $item->codevalues;
    }
    return $jobshift;
  }

  /**
   * Helper function to update all config.
   */
  public function updatAllConfig($field) {
    foreach ($field as $item) {

      $this->connection->update(DataModel::CODEVAL)
        ->fields($item)
        ->condition('codename', $item['codename'], "=")
        ->condition('codetype', $item['codetype'], "=")
        ->execute();

    }
  }

  /**
   * Helper function to get EMP ID type.
   */
  public function getEmpIdType() {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n')
      ->condition('codename', 'EMPID', "=")
      ->condition('codetype', 'employeeid', "=");
    $result = $query->execute()->fetch();
    $res = @$result;

    return $res;
  }

  /**
   * This checks the Employee id type configuration.
   */
  public function getEmployeeIdConfig() {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n')
      ->condition('codename', 'EMPID', "=")
      ->condition('codetype', 'employeeid', "=");
    $result = $query->execute()->fetch();

    $res = [];
    if ($result->codevalues == 'Automatic') {
      $res['disabled'] = 'disabled';
      $res['empid'] = $result->description . 'XXXX';
      $res['helpmsg'] = 'Employee ID will be auto generate';
    }
    else {
      $res['disabled'] = '';
      $res['empid'] = '';
      $res['helpmsg'] = 'Mention Employee Id of the person';
    }

    return $res;
  }

  /**
   * Helper function to get Branch Code Toggle On/Off.
   */
  public function getBranchCodeConfig() {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n')
      ->condition('codename', 'BRNCD', "=")
      ->condition('codetype', 'branchcode', "=");
    $result = $query->execute()->fetch();

    return $result;
  }

  /**
   * Helper function to get Department Code Toggle On/Off.
   */
  public function getDepartmentCodeConfig() {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n')
      ->condition('codename', 'DPTCD', "=")
      ->condition('codetype', 'departmentcode', "=");
    $result = $query->execute()->fetch();

    return $result;
  }

  /**
   * Helper function to get Designation Code Toggle On/Off.
   */
  public function getDesignationCodeConfig() {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n')
      ->condition('codename', 'DSGCD', "=")
      ->condition('codetype', 'designationcode', "=");
    $result = $query->execute()->fetch();

    return $result;
  }

  /**
   * Helper function to get Work order Code Toggle On/Off.
   */
  public function getWorkorderCodeConfig() {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n')
      ->condition('codename', 'WRKCD', "=")
      ->condition('codetype', 'workordercode', "=");
    $result = $query->execute()->fetch();

    return $result;
  }

  /**
   * Helper function to get shift timing.
   */
  public function setShiftTiming($field) {

    $this->connection->insert(DataModel::CODEVAL)
      ->fields($field)
      ->execute();
  }

  /**
   * Helper function to get shift timing list.
   */
  public function getShiftTimingList() {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('status', 1, "=");
    $query->condition('codetype', 'jobshift', "=");
    $query->orderBy('createdon', 'DESC');
    $result = $query->execute()->fetchAll();

    return $result;
  }

  /**
   * Helper function to shift details form ID.
   */
  public function getShiftDetailsById($pk) {
    $query = $this->connection->select(DataModel::CODEVAL, 'n');
    $query->fields('n');
    $query->condition('codepk', $pk, "=");
    $query->condition('codetype', 'jobshift', "=");
    $result = $query->execute()->fetch();

    return $result;
  }

  /**
   * Helper funciton to update shift timing.
   */
  public function updateShiftTiming($field, $pk) {

    $this->connection->update(DataModel::CODEVAL)
      ->fields($field)
      ->condition('codepk', $pk, "=")
      ->condition('codetype', 'jobshift', "=")
      ->execute();
  }

}
