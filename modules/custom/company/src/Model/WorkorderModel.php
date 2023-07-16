<?php

namespace Drupal\company\Model;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\library\Lib\DataModel;

class WorkorderModel extends ControllerBase
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


    public function getWorkorderList()
    {
        $query = $this->connection->select(DataModel::CODEVAL, 'n');
        $query->fields('n');
        $query->condition('n.codetype', 'workorder', "=");
        $query->condition('n.status', 1, "=");
        $query->orderBy('createdon', 'DESC');
        $result = $query->execute()->fetchAll();

        $res = @$result;

        return $res;
    }

    /*
    * @param $data array which includes work & team details
    * which needs to be insert in srch_codevalues table
    */
    public function setWorkOrder( $data )
    {
        $data['workorder']['codetype']    =    'workorder';

        $query = \Drupal::database();
        $last_workorder_id = $query ->insert(DataModel::CODEVAL)
            ->fields($data['workorder'])
            ->execute();

        foreach( $data['teamorder']  AS  $team )
        {
            //inserting column codetype & parent
            $team['codetype']    =    'teamorder';
            $team['parent']    =    $last_workorder_id;

            $query ->insert(DataModel::CODEVAL)
                ->fields($team)
                ->execute();
        }
    }

    /*
    * Helper function to get Team list from Project won number
    * @param $won project won number
    * Static Query
    * SELECT cd1.codepk, cd1.codetype, cd1.codename, cd1.codevalues, cd2.codevalues FROM `srch_codevalues` cd1
    * LEFT JOIN `srch_codevalues` cd2 ON cd1.parent = cd2.codepk
    * WHERE cd1.codetype='teamorder' AND cd2.codename=<codename>;
    */
    public function getTeamListByWorkOrder($won = '')
    {

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
        foreach($result AS $val) {
            $res[$val->codename] = $val->codevalues;
        }

        return $res;
    }
}
