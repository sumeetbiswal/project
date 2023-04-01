<?php

namespace Drupal\tagging\Model;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Drupal\library\Lib\DataModel;

class TaggingModel extends ControllerBase  {

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


  public function getUnEmployeeList()
  {
    $query = $this->connection->select(DataModel::EMPTAGGING, 't');
    $query -> innerJoin(DataModel::EMPPERSONAL, 'p','t.userpk = p.userpk');
    $query->isNull('t.won');
    $query->isNull('t.ton');
    $query->orderBy('t.createdon', 'DESC');
    $query->fields('t');
    $query->fields('p');
    $result = $query->execute()->fetchAll();
    return $result;

  }

  public function getTaggingDetailsById($pk){

    $query = $this->connection->select(DataModel::EMPTAGGING, 't');
    $query->leftJoin(DataModel::EMPPERSONAL, 'p','t.userpk = p.userpk');
    $query->leftJoin(DataModel::EMPOFFICIAL, 'o','t.userpk = o.userpk');
    $query->fields('t');
    $query->fields('p',['firstname']);
    $query->fields('p',['lastname']);
    $query->fields('o',['empid']);
    $result = $query->execute()->fetch();

    return $result;
  }

}
