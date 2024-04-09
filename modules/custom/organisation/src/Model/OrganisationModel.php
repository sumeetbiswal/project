<?php

namespace Drupal\organisation\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;

/**
 * Model file for Organisation.
 */
class OrganisationModel extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * Constructor for OrganisationModel.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Helper function to get organisation details by ID.
   */
  public function getOrganisationDetailsById($id = 1) {
    $query = $this->connection->select('srch_organisationinfo', 'n');
    $query->leftjoin('srch_codevalues', 'cd', 'cd.codename = n.organisationtype');
    $query->leftjoin('srch_cities', 'ct', 'ct.id = n.city');
    $query->leftjoin('srch_states', 'st', 'st.id = n.state');
    $query->leftjoin('srch_countries', 'cnt', 'cnt.id = n.country');
    $query->addField('ct', 'name', 'cityname');
    $query->addField('st', 'name', 'statename');
    $query->addField('cnt', 'name', 'countryname');
    $query->fields('n');
    $query->fields('cd', ['codevalues']);
    $query->condition('n.organisationpk', $id, "=");
    $query->condition('n.status', 1, "=");
    $result = $query->execute()->fetch();

    return $result;
  }

  /**
   * Helper function to create organisation.
   */
  public function setOrganisation($field) {

    $this->connection->insert('srch_organisationinfo')
      ->fields($field)
      ->execute();
  }

  /**
   * Helper function to update the organisation details.
   */
  public function updateOrganisation($field) {

    $this->connection->update('srch_organisationinfo')
      ->fields($field)
      ->condition('organisationpk', 1)
      ->execute();
  }

  /**
   * Helper function to get organisation type list.
   */
  public function getOrganisationTypeList() {
    $query = $this->connection->select('srch_codevalues', 'n');
    $query->fields('n');
    $query->condition('status', 1, "=");
    $query->condition('codetype', 'organisation', "=");
    $query->orderBy('weight', "ASC");
    $result = $query->execute()->fetchAll();

    $res = $result;

    return $res;
  }

}
