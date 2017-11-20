<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

define('DATABASE', 'bv98');
define('USERNAME', 'bv98');
define('PASSWORD', 'dvJjpozdZ');
define('CONNECTION', 'sql1.njit.edu');
class dbConn{
    protected static $db;
    private function __construct() {
        try {
            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	    echo "Connected successfully to db <br><br>";
        }
        catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
    }
    public static function getConnection() {
        if (!self::$db) {
            new dbConn();
        }
        return self::$db;
    }
}
class collection {
    static public function create() {
        $model = new static::$modelName;
        return $model;
    }
    static public function findAll() {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet = $statement->fetchAll();
        return $recordsSet;
    }
    static public function findOne($id) {
        $db = dbConn::getConnection();
        $tableName = get_called_class();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
        $statement = $db->prepare($sql);
        $statement->execute();
        $class = static::$modelName;
        $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        $recordsSet = $statement->fetchAll();
        return $recordsSet;
    }
}
class accounts extends collection {
    public static $modelName = 'account';
}
class todos extends collection {
    public static $modelName = 'todo';
}

class model {
    protected $tableName;
    public function save() {
        if ($this->id == '') {
            $this->insert();
        } else {
            $this->update($this->id);
        }
    }
    public function insert() {
        $db= dbConn::getConnection();
	$this->id=33;
	$tableName= $this->tableName;
	$array = get_object_vars($this);
        array_pop($array);
	$head = array_keys($array);
        $columnString = implode(',',$head);
	$valueString = "'".implode("',' ",$array)."'";
	$sql = 'INSERT INTO ' . $tableName. ' (' . $columnString . ') VALUES (' . $valueString . ')' ;
        $statement = $db->prepare($sql);
	$statement->execute();
    }
    public function update($id) {
        $db = dbConn::getConnection();
	$tableName= $this->tableName;
	$array = get_object_vars($this);
        array_pop($array);
	$space=' ';
	$arr1='';
        foreach($array as $key=>$value){
	         $arr1.=$space.$key.'="'.$value.'"';
           $space=", ";
	}
        $sql= 'UPDATE '. $tableName .' SET'. $arr1 .' WHERE id='. $id;
        $statement = $db->prepare($sql);
        $statement->execute();
    }
    public function delete($id){
        $tableName= $this->tableName;
	$sql = ' DELETE FROM ' . $tableName . ' WHERE id= '. $id;
        $db = dbConn::getConnection();
	$statement = $db->prepare($sql);
        $statement->execute();
    }
    public function header() {
        $db= dbConn::getConnection();
        $tableName = $this->tableName;
        $sql = 'SHOW COLUMNS FROM ' . $tableName;
        $statement = $db->prepare($sql);
        $statement->execute();
        $head = $statement->fetchAll(PDO::FETCH_COLUMN);
        return $head;
    }
}
class account extends model {
    public $id;
    public $email;
    public $fname;
    public $lname;
    public $phone;
    public $birthday;
    public $gender;
    public $password;
    public function __construct()
    {
        $this->tableName = 'accounts';
    }
}
class todo extends model {
    public $id;
    public $owneremail;
    public $ownerid;
    public $createddate;
    public $duedate;
    public $message;
    public $isdone;
    public function __construct()
    {
        $this->tableName = 'todos';

    }
}

class htmlTable {
    static public function tableNew($head,$rows)
    {
        $htmlTable = NULL;
        $htmlTable .= "<table border = 2>";
        foreach ($head as $head1) {
            $htmlTable .= "<th>$head1</th>";
        }
        foreach ($rows as $row) {
            $htmlTable .= "<tr>";
            foreach ($row as $column) {
                $htmlTable .= "<td>$column</td>";
            }
            $htmlTable .= "</tr>";
        }
        $htmlTable .= "</table>";
        echo $htmlTable;
    }
}

accounts::create();
$records = accounts::findAll();
$record = accounts::findOne(1);
$acc= new account();
$head= $acc->header();
echo '<h1>accounts table find all </h1>';
echo htmlTable::tableNew($head,$records);
echo '<hr>';

echo '<h1>accounts table find one </h1>';
echo htmlTable::tableNew($head,$record);
echo '<hr>';  

$acc->fname='tony';
$acc->lname='martial';
$acc->gender='male';
$acc->insert();
$a= accounts::findOne(33);
echo '<h1>accounts table insert </h1>';
echo htmlTable::tableNew($head,$a);
echo '<hr>';

$acc->phone= '848';
$acc->id=33;
$acc->fname='harry';
$acc->lname='winks';
$acc->email='winks@fmail.com';
$acc->birthday='1111-11-11';
$acc->gender='male';
$acc->password='winks';
$acc->update(33);
$a= accounts::findOne(33);
echo '<h1>accounts table update </h1>';
echo htmlTable::tableNew($head,$a);
echo '<hr>';

$acc->delete(33);
$a= accounts::findAll();
echo '<h1>accounts table delete </h1>';
echo htmlTable::tableNew($head,$a);
echo '<hr>';

todos::create();
$records = todos::findAll();
$td= new todo();
$head= $td->header();
echo '<h1>todos table find all </h1>';
echo htmlTable::tableNew($head,$records);
echo '<hr>';

$record = todos::findOne(4);
echo '<h1>todos table find one </h1>';
echo htmlTable::tableNew($head,$record);
echo '<hr>';

$td->delete(33);
$a= todos::findAll();
echo '<h1>todos table delete </h1>';
echo htmlTable::tableNew($head,$a);
echo '<hr>';

$td->owneremail='marcusford@njit.com';
$td->message='new todo';
$td->save();
$a1= todos::findAll();
echo '<h1>todos table insert </h1>';
echo htmlTable::tableNew($head,$a1);
echo '<hr>';

$td->owneremail='marcusford@njit.com';
$td->message='update todo';
$td->save();
$a1= todos::findAll();
echo '<h1>todos table update </h1>';
echo htmlTable::tableNew($head,$a1);
echo '<hr>';

?>
