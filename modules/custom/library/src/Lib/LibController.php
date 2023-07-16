<?php

namespace Drupal\library\Lib;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\library\Lib\DataModel;

class LibController extends ControllerBase {

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

	public function getActionMode()
	{
		$current_path = \Drupal::request()->getPathInfo();
		$path = explode('/', $current_path);
		return $path[2];
	}

	public function getIdFromUrl()
	{

		$current_path = \Drupal::request()->getPathInfo();
		$path = explode('/', $current_path);
		return $path[3];
	}

	public function getStateList()
	{
		$query = $this->connection->select(DataModel::STATE, 'n');
				$query->fields('n');
				$query->condition('name', "", "!=");
				$query->condition('country_id', "101", "=");
				$query->orderBy('name', "ASC");
				$result = $query->execute()->fetchAll();
		$res[''] = 'Select State';
		foreach($result AS $val)
		{
			$res[$val->id] = $val->name;
		}


		return $res;
	}

	public function getStateNameById($id)
	{
		$query = $this->connection->select(DataModel::STATE, 'n');
				$query->fields('n');
				$query->condition('id', $id ,"=");
        $result = $query->execute()->fetchAll();
        foreach ($result as $row => $content) {
          $state_name = $content->name;
        }

		return $state_name;
	}

	public function getCityListByState($statePk)
	{
		$query = $this->connection->select(DataModel::CITY, 'n');
				$query->fields('n');
				$query->condition('name', "", "!=");
				$query->condition('state_id', $statePk, "=");
				$query->orderBy('name', "ASC");
				$result = $query->execute()->fetchAll();
		$res[''] = 'Select City';
		foreach($result AS $val)
		{
			$res[$val->id] = $val->name;
		}

		return $res;
	}
	public function getCityNameById($id)
	{
		$query = $this->connection->select(DataModel::CITY, 'n');
				$query->fields('n');
				$query->condition('id', $id ,"=");
				$result = $query->execute()->fetchAll();
		      foreach ($result as $row => $content) {
            $city_name = $content->name;
       }

		return $city_name;
	}

	public function getCountryNameById($id)
	{
		$query = $this->connection->select(DataModel::COUNTRY, 'n');
				$query->fields('n');
				$query->condition('id', $id ,"=");
				$result = $query->execute()->fetchAll();
		      foreach ($result as $row => $content) {
            $cntry_name = $content->name;
       }

		return $cntry_name;
	}

	public function getCodeValues($codetype, $codename)
	{
		$query = $this->connection->select(DataModel::CODEVAL, 'n');
				$query->fields('n');
				$query->condition('codename', $codename ,"=");
				$query->condition('codetype', $codetype ,"=");
				$result = $query->execute();
		    foreach ($result as $row => $content) {
            $codevalue = $content->codevalues;
       }

		return $codevalue;
	}

  public function getRoles()
  {
		$roles = \Drupal::entityTypeManager()->getStorage('user_role')->loadMultiple();
		foreach($roles AS $val){
		 $exclude = array('anonymous', 'administrator');
		 if(in_array($val->id(), $exclude)) continue;
		 $roleList[$val->id()] = $val->label();
		}
		 return $roleList;
  }

  /*
  * Generating code for codevalues based upon entity type
  * @1st paramter entity type
  * @2nd paramter string inputed by user whcih to be used in
  * generating code
  */
  public function generateCode($codephrase, $string)
  {
		$codename = '';

		for($i = 3; $i <= 8; $i++ )
		{
			$extract_subcode_from_string = substr($string, 0, $i);
			$codename = strtoupper($codephrase.$extract_subcode_from_string);

			$query = $this->connection->select(DataModel::CODEVAL, 'n');
			$query->fields('n')
			->condition('codename', $codename, "=");
			$result = $query->execute()->fetch();
			if(empty($result))	break;
		}


		return $codename;
  }

   /*
   * Generate default password
   * Parameters required @string , @date of birth
   * @return string@date_of_birth
   */

   public function generateDefaultPassword($str, $dob)
   {
	   $dob_split_arr = explode('/', $dob);
	   $dob_merge_to_string = implode('', $dob_split_arr);

	   $password = $str.'@'.$dob_merge_to_string;

	   return $password;
   }

   /*
	* change the dateformat to Db date format
	* Parameters required UI @date string
	* @return DB date format
   */

   public function getDbDateFormat($dt)
   {
	   $explode_dt = explode('/', $dt);
	   $dbDate = $explode_dt[2].'-'.$explode_dt[1].'-'.$explode_dt[0];
	   return $dbDate;
   }


   /*
    * This function helps to identify whether a path exist ?
    * @param $path
    * @return Boolean
   */
   public function isPathEnable($path, $action){
     $path = $path . '/' . $action;
     $query = $this->connection->select('router', 'r');
     $query->fields('r', ['path']);
     $query->condition('path', '%' . $path . '%' ,'LIKE');
     $results = $query->execute()->fetchAll();

     //get current route name. from that fetch route module name which will use on permission on twig file
     $current_route_permission = \Drupal::service('current_route_match')->getRouteObject()->getRequirements()['_permission'];
     $route_permission_name = explode(' ', $current_route_permission)[0];


     $label = '';

     //Get the route name of the ADD page and fetch title of it. to be used in ADD button Label.
     $route_name = \Drupal::routeMatch()->getRouteName();
     $route_name = explode('.', $route_name)[0];
     $add_route_name = $route_name . '.add';

     if ($route = \Drupal::service('router.route_provider')->getRouteByName($add_route_name)){
       $title = $route->getDefault('_title');
       $label = $title;
     }


     $result = [
       'exist' => FALSE,
       'permission' => FALSE,
       'label' => $label ,
     ];
     if(count($results) > 0){
       $result['exist'] = TRUE;
     }

     if (\Drupal::currentUser()->hasPermission($route_permission_name . ' '. $action)) {
       $result['permission'] = TRUE;
     }

     return $result;
   }


  /*
   * This function helps to set the page title at run time
   * This usually being called when need of dynamic page title
   * such as Edit page
   * @param $title
  */
  public function setPageTitle($newTitle){
    $request = \Drupal::request();
    if ($route = $request->attributes->get(\Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT)) {
      $route->setDefault('_title', $newTitle);
    }
  }

}
