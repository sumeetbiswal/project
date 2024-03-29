<?php

namespace Drupal\employee\Model;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Drupal\library\Lib\DataModel;

class EmployeeModel extends ControllerBase
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

    public function createUser($userdata)
    {

        $user = \Drupal\user\Entity\User::create();

        $user->setUsername($userdata['username']);
        $user->setPassword($userdata['password']);
        $user->setEmail($userdata['email']);

        /* Drupal wont allow to set authenticated role manually as it is bydefault */
        if($userdata['role'] != 'authenticated') {
            $user->addRole($userdata['role']);
        }
        //optional
        $language = 'en';
        $user->set("init", $userdata['email']);
        $user->set("langcode", $language);
        $user->set("preferred_langcode", $language);
        $user->set("preferred_admin_langcode", $language);
        $user->activate();

        if(isset($userdata['image'])) {
            $user->set('user_picture', $userdata['image']);
        }


        //Save user account.
        $user->save();

        return $user;
    }

    public static function setPersonalInfo($user, $data, $opt, &$context )
    {
        $query = \Drupal::database();

        $query->query('start transaction');

        //personal info
        $query->query(
            'insert into '.DataModel::EMPPERSONAL.' set userpk = :userpk, firstname = :fname, lastname= :lname, fathername= :fthname, mothername= :mthname, gender= :gender, dob = :dob,marital= :marital, bloodgroup= :blood, religion= :religion, nationality= :nationality',
            array(':userpk'=>$user->Id(), ':fname'=>$data['personal']['firstname'], ':lname'=>$data['personal']['lastname'], ':fthname'=>$data['personal']['fname'], ':mthname'=>$data['personal']['mname'], ':gender'=>$data['personal']['gender'], ':dob'=>$data['personal']['dob'], ':marital'=>$data['personal']['marital'], ':blood'=>$data['personal']['blood'],':religion'=>$data['personal']['religion'],':nationality'=>$data['personal']['nationality'])
        );

        //contact info
        $query->query(
            'insert into '.DataModel::EMPCONTACT.' set userpk = :userpk, phoneno = :phoneno, altphoneno= :altphoneno, emrgphoneno= :emrgphoneno, relationship= :relationship, email= :email, res_address1= :res_address1, res_address2= :res_address2, res_state= :res_state, res_city= :res_city, res_country= :res_country, res_pincode= :res_pincode, perm_address1= :perm_address1, perm_address2= :perm_address2, perm_state= :perm_state, perm_city= :perm_city, perm_country= :perm_country, perm_pincode= :perm_pincode, status= :status',
            array(':userpk'=>$user->Id(), ':phoneno'=>$data['contact']['phoneno'], ':altphoneno'=>$data['contact']['altphoneno'], ':emrgphoneno'=>$data['contact']['emergencyno'], ':relationship'=>$data['contact']['relationship'], ':email'=>$data['contact']['email'], ':res_address1'=>$data['contact']['address1'],
            ':res_address2'=>$data['contact']['address2'],':res_state'=>$data['contact']['state'], ':res_city'=>$data['contact']['city'], ':res_country'=>$data['contact']['country'], ':res_pincode'=>$data['contact']['pincode'], ':perm_address1'=>$data['contact']['permanentaddress1'], ':perm_address2'=>$data['contact']['permanentaddress2'], ':perm_state'=>$data['contact']['permanentstate'], ':perm_city'=>$data['contact']['permanentcity'], ':perm_country'=>$data['contact']['permanentcountry'], ':perm_pincode'=>$data['contact']['permanentpincode'], ':status'=> 1)
        );

        //academic info
        foreach($data['qualification'] AS $qual)
        {
            $query->query(
                'insert into '.DataModel::EMPACADEMIC.' set userpk = :userpk, class = :class, stream= :stream, board= :board, yearofpassing= :yearofpassing, score = :score',
                array(':userpk'=>$user->Id(), ':class'=>$qual['class'], ':stream'=>$qual['stream'], ':board'=>$qual['university'], ':yearofpassing'=>$qual['yearofpassing'], ':score'=>$qual['score'])
            );

        }

        //employeement info
        if(!empty($data['experience'])) {
            foreach ($data['experience'] as $qual) {
                $query->query(
                    'insert into ' . DataModel::EMPEXPRNC . ' set userpk = :userpk, organisation = :organisation, designation= :designation, fromdate= :fromdate, todate= :todate',
                    array(':userpk' => $user->Id(), ':organisation' => $qual['organisation'], ':designation' => $qual['designation'], ':fromdate' => $qual['fromdate'], ':todate' => $qual['todate'])
                );

            }
        }
        //official info
        $query->query(
            'insert into '.DataModel::EMPOFFICIAL.' set userpk = :userpk, empid = :empid, department= :department, branch= :branch, designation= :designation, jobnature = :jobnature, email= :email, doj= :doj, jobtype= :jobtype, shifttime= :shifttime',
            array(':userpk'=>$user->Id(), ':empid'=>$data['official']['id'], ':department'=>$data['official']['department'], ':branch'=>$data['official']['branch'], ':designation'=>$data['official']['designation'], ':jobnature'=>$data['official']['jobnature'], ':email'=>$data['official']['officialemail'], ':doj'=>$data['official']['doj'], ':jobtype'=>$data['official']['jobtype'],':shifttime'=>$data['official']['shifttime'])
        );


        //tagging info
        $query->query(
            'insert into '.DataModel::EMPTAGGING.' set userpk = :userpk',
            array(':userpk'=>$user->Id())
        );

        $query->query('commit');

    }

    public static function finishOperation()
    {
        $response = new RedirectResponse(\Drupal\Core\Url::fromRoute('employee.emplist')->toString());
        $response->send();
        return;
    }

    public function checkUserIdExist($username)
    {
        $query = $this->connection->select(DataModel::USERDATA, 'name');
        $query->fields('name');
        $query->condition('status', 1, "=");
        $query->condition('name', $username, "=");
        $result = $query->execute()->fetch();

        $res = $result;
        return $res;
    }

    public function checkEMailIdExist($email)
    {
        $query = $this->connection->select(DataModel::USERDATA, 'mail');
        $query->fields('mail');
        $query->condition('status', 1, "=");
        $query->condition('mail', $email, "=");
        $result = $query->execute()->fetch();

        $res = $result;
        return $res;
    }

    public function getPersonalDetailsById($id)
    {
        $query = $this->connection->select(DataModel::EMPPERSONAL, 'n');
        $query->fields('n');
        $query->condition('userpk', $id, "=");
        $result = $query->execute()->fetch();
        return $result;
    }
    /**
     * @parameter user id
     * get official details
     */
    public function getOfficialDetailsById($id)
    {

        $query = $this->connection->select(DataModel::EMPOFFICIAL, 'oi');
        $query->leftjoin(DataModel::CODEVAL, 'cv1', 'cv1.codename = oi.branch AND cv1.codetype = :brnc', array('brnc'=>'branch'));
        $query->leftjoin(DataModel::CODEVAL, 'cv2', 'cv2.codename = oi.department AND cv2.codetype = :dept', array('dept'=>'department'));
        $query->leftjoin(DataModel::CODEVAL, 'cv3', 'cv3.codename = oi.designation AND cv3.codetype = :desig', array('desig'=>'designation'));
        $query->leftjoin(DataModel::CODEVAL, 'cv4', 'cv4.codename = oi.jobtype AND cv4.codetype = :jbtpy', array('jbtpy'=>'jobtype'));
        $query->leftjoin(DataModel::CODEVAL, 'cv5', 'cv5.codename = oi.jobnature AND cv5.codetype = :jbntr', array('jbntr'=>'jobnature'));
        $query->leftjoin(DataModel::CODEVAL, 'cv6', 'cv6.codename = oi.shifttime AND cv6.codetype = :jbshft', array('jbshft'=>'jobshift'));
        $query->fields('oi', array('empid'));
        $query->addField('cv1', 'codevalues', 'branch');
        $query->addField('cv2', 'codevalues', 'department');
        $query->addField('cv3', 'codevalues', 'designation');
        $query->addField('cv4', 'codevalues', 'jobtype');
        $query->addField('cv5', 'codevalues', 'jobnature');
        $query->addField('oi', 'email', 'email');
        $query->addField('oi', 'doj', 'joining');
        $query->addField('cv6', 'codevalues', 'jobshift');
        $query->condition('oi.userpk', $id, "=");

        $result = $query->execute()->fetch();
        return $result;
    }

    /**
     * @parameter user id
     * get contact details
     */
    public function getContactDetailsById($id)
    {
    
        $query = $this->connection->select(DataModel::EMPCONTACT, 'cnt');
        $query->leftjoin(DataModel::STATE, 'st', 'st.id = cnt.res_state');
        $query->leftjoin(DataModel::CITY, 'ct', 'ct.id = cnt.res_city');
        $query->leftjoin(DataModel::COUNTRY, 'cntry', 'cntry.id = cnt.res_country');
        $query->leftjoin(DataModel::STATE, 'pst', 'pst.id = cnt.perm_state');
        $query->leftjoin(DataModel::CITY, 'pct', 'pct.id = cnt.perm_city');
        $query->leftjoin(DataModel::COUNTRY, 'pcntry', 'pcntry.id = cnt.perm_country');

        $query->fields('cnt', array('phoneno'));
        $query->addField('cnt', 'altphoneno', 'altphone');
        $query->addField('cnt', 'emrgphoneno', 'emrgphone');
        $query->addField('cnt', 'relationship', 'relationship');
        $query->addField('cnt', 'email', 'email');
        $query->addField('cnt', 'res_address1', 'res_address1');
        $query->addField('cnt', 'res_address2', 'res_address2');
        $query->addField('st', 'name', 'res_state');
        $query->addField('ct', 'name', 'res_city');
        $query->addField('cntry', 'name', 'res_country');
        $query->addField('cnt', 'res_pincode', 'res_pincode');
        $query->addField('cnt', 'perm_address1', 'perm_address1');
        $query->addField('cnt', 'perm_address2', 'perm_address2');
        $query->addField('pst', 'name', 'perm_state');
        $query->addField('pct', 'name', 'perm_city');
        $query->addField('pcntry', 'name', 'perm_country');
        $query->addField('cnt', 'perm_pincode', 'perm_pincode');

        $query->condition('cnt.userpk', $id, "=");
        $result = $query->execute()->fetch();
        return $result;

    }

    public function getAcademicDetailsById($id)
    {
        $query = $this->connection->select(DataModel::EMPACADEMIC, 'n');
        $query->fields('n');
        $query->condition('userpk', $id, "=");
        $result = $query->execute()->fetchAll();
        return $result;
    }

    public function getPrevEmployeementDetailsById($id)
    {
        $query = $this->connection->select(DataModel::EMPEXPRNC, 'n');
        $query->fields('n');
        $query->condition('userpk', $id, "=");
        $result = $query->execute()->fetchAll();
        return $result;
    }

    public function getEmployeeList()
    {
        $query = $this->connection->select(DataModel::EMPPERSONAL, 'n');
        $query -> innerJoin(DataModel::EMPOFFICIAL, 'nf', 'n.userpk = nf.userpk');
        $query->orderBy('n.createdon', 'DESC');
        $query->fields('n');
        $query->fields('nf');
        $result = $query->execute()->fetchAll();
        return $result;

    }

    /**
     * Helper function to fetch employee based on
     * key word search Autocomplete
     *
     * @param $input
     */
    public function getEmployeeListAutoComplete($input)
    {
        $query = $this->connection->select(DataModel::EMPPERSONAL, 'n');
        $query -> innerJoin(DataModel::EMPOFFICIAL, 'nf', 'n.userpk = nf.userpk');
        $query->orderBy('n.createdon', 'DESC');
        $query->fields('n');
        $query->fields('nf');

        $orGroup = $query->orConditionGroup();
        $orGroup
            ->condition('n.firstname', "%" . $input . "%", 'LIKE')
            ->condition('n.lastname', "%" . $input . "%", 'LIKE')
            ->condition('nf.empid', "%" . $input . "%", 'LIKE');
        $query->condition($orGroup);

        $result = $query->execute()->fetchAll();
        return $result;

    }

    /**
     * Helper function to fetch employee based on
     * key word search Autocomplete
     *
     * @param $input
     */
    public function getEmployeeAutoCompleteValueById($uid)
    {
        $query = $this->connection->select(DataModel::EMPPERSONAL, 'n');
        $query -> innerJoin(DataModel::EMPOFFICIAL, 'nf', 'n.userpk = nf.userpk');
        $query->orderBy('n.createdon', 'DESC');
        $query->fields('n');
        $query->fields('nf');

        $data = $query->execute()->fetch();

        $results = $data->firstname . ' ' . $data->lastname . ' (' . $data->empid. ')';

        return $results;

    }

    public function getEmployeeCount()
    {
        $query = $this->connection->select(DataModel::EMPPERSONAL, 'n');
        $query->condition('status', 1, "=");
        $query->fields('n');
        $result = $query->execute()->fetchAll();
        return count($result);
    }

    /*
    * get user pic from Drupal user object
    * Set default Pic if user has not uploaded pic
    */
    public function getUserPic()
    {
        $user = \Drupal::currentUser();
        $personal_details = $this->getPersonalDetailsById($user->id());

        $userobj = \Drupal::service('entity_type.manager')->getStorage('user')->load($user->id());
        $avatar = 'male.jpg';
        if($userobj->user_picture->entity != null) {
            $avatar = $userobj->user_picture->entity->getFileName();
        }
        else
        {
            $avatar = ( $personal_details->gender == 'M' ) ? 'male.jpg' : 'female.jpg';
        }

        return  $avatar;
    }
    /*
    * joining personalinfo, Contactinfo and Officialinfo
    * Fetching all Employee details except Academic Info
    */
    public function getEmployeeDetails()
    {
        
        $query = $this->connection->select(DataModel::EMPPERSONAL, 'p');
        $query->leftjoin(DataModel::EMPCONTACT, 'c', 'p.userpk = c.userpk');
        $query->leftjoin(DataModel::STATE, 's', 'c.res_state = s.id');
        $query->leftjoin(DataModel::CITY, 'ct', 'c.res_city = ct.id');
        $query->leftjoin(DataModel::COUNTRY, 'co', 'c.res_country = co.id');
        $query->leftjoin(DataModel::STATE, 's1', 'c.res_state = s1.id');
        $query->leftjoin(DataModel::CITY, 'ct1', 'c.res_city = ct1.id');
        $query->leftjoin(DataModel::COUNTRY, 'co1', 'c.res_country = co1.id');
        $query->leftjoin(DataModel::EMPOFFICIAL, 'o', 'o.userpk = c.userpk');


        $query->addField('p', 'firstname', 'FirstName');
        $query->addField('p', 'lastname', 'LastName');
        $query->addField('p', 'fathername', 'FatherName');
        $query->addField('p', 'mothername', 'MotherName');
        $query->addField('p', 'gender', 'Gender');
        $query->addField('p', 'dob', 'DateOfBirth');
        $query->addField('p', 'marital', 'Marital');
        $query->addField('p', 'bloodgroup', 'BloodGroup');
        $query->addField('p', 'religion', 'Religion');
        $query->addField('p', 'nationality', 'Nationality');
        $query->addField('c', 'phoneno', 'PhoneNumber');
        $query->addField('c', 'altphoneno', 'AlternatePhoneNumber');
        $query->addField('c', 'emrgphoneno', 'EmergencyPhoneNumber');
        $query->addField('c', 'relationship', 'Relationship');
        $query->addField('c', 'email', 'Email');
        $query->addField('c', 'res_address1', 'ResidentAddress1');
        $query->addField('c', 'res_address2', 'ResidentAddress2');
        $query->addField('s', 'name', 'State');
        $query->addField('ct', 'name', 'City');
        $query->addField('co', 'name', 'Country');
        $query->addField('c', 'res_pincode', 'Pincode');
        $query->addField('c', 'perm_address1', 'PermenantAddress1');
        $query->addField('c', 'perm_address2', 'PermenantAddress2');
        $query->addField('s1', 'name', 'State');
        $query->addField('ct1', 'name', 'City');
        $query->addField('co1', 'name', 'Country');
        $query->addField('c', 'perm_pincode', 'Pincode');
        $query->addField('o', 'empid', 'EmployeeID');
        $query->addField('o', 'branch', 'Branch');
        $query->addField('o', 'department', 'Department');
        $query->addField('o', 'designation', 'Designation');
        $query->addField('o', 'jobtype', 'JobType');
        $query->addField('o', 'jobnature', 'JobNature');
        $query->addField('o', 'email', 'Email');
        $query->addField('o', 'doj', 'DateofJoining');
        $query->addField('o', 'shifttime', 'ShiftTime');
        $result = $query->execute()->fetch();
        return $result;

    }
}
