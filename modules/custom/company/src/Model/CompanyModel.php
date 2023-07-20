<?php

namespace Drupal\company\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Link;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

class CompanyModel extends ControllerBase
{

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
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getCompanyDetailsById($id = 1)
    {
        $query = $this->connection->select('srch_companyinfo', 'n');
        $query->leftjoin('srch_codevalues', 'cd', 'cd.codename = n.companytype');
        $query->leftjoin('srch_cities', 'ct', 'ct.id = n.city');
        $query->leftjoin('srch_states', 'st', 'st.id = n.state');
        $query->leftjoin('srch_countries', 'cnt', 'cnt.id = n.country');
        $query->addField('ct', 'name', 'cityname');
        $query->addField('st', 'name', 'statename');
        $query->addField('cnt', 'name', 'countryname');
        $query->fields('n');
        $query->fields('cd', ['codevalues']);
        $query->condition('n.companypk', $id, "=");
        $query->condition('n.status', 1, "=");
        $result = $query->execute()->fetch();

        return $result;
    }

    public function setCompany($field)
    {
        $query = \Drupal::database();
           $query ->insert('srch_companyinfo')
               ->fields($field)
               ->execute();
    }

    public function updateCompany($field)
    {
        $query = \Drupal::database();
          $query->update('srch_companyinfo')
              ->fields($field)
              ->condition('companypk', 1)
              ->execute();
    }

    public function getCompanyTypeList()
    {
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
