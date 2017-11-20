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
        $recordsSet =  $statement->fetchAll();
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
        $recordsSet =  $statement->fetchAll();
        return $recordsSet[0];
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
    public function save()
    {
        if ($this->id = '') {
            $sql = $this->insert();
        } else {
            $sql = $this->update();
        }
        $db = dbConn::getConnection();
        $statement = $db->prepare($sql);
        $statement->execute();
        $tableName = get_called_class();
        $array = get_object_vars($this);
        $columnString = implode(',', $array);
        $valueString = ":".implode(',:', $array);
	echo 'Record saved' . $this->id;
    }
    private function insert() {
        $db= dbConn::getConnection();
	$statement = $db->prepare($sql);
	$statement->execute();
	$tableName= $this->tableName();
	$array = get_object_vars($this);
        $columnString = implode(',', $array);
	$valueString = ':'.implode(',:', $array);
	$sql = 'INSERT INTO' . $tableName. '('.$columnString.') VALUES ('.$valueString.')' ;
        return $sql;
    }
    private function update() {
        $db = dbConn::getConnection();
	$tableName= $this->tableName;
	$array = get_object_vars($this);
	array_pop($array);
	$space= ' ';
	$arr= '';
	foreach($array as $key=>$value){
	   $array1.=$temp.$key.'="'.$value.'"';
	   $space=", ";
        }
	$sql = 'UPDATE' . $tableName . 'SET' . $arr .  'WHERE id=' . $id;
	$statement = $db->prepare($sql);
	$statement->execute();
	return $sql;
        echo 'Record updated' . $this->id;
    }
    public function delete() {
        $db = dbConn::getConnection();
        $sql = 'DELETE FROM' . $tableName . 'WHERE id='. $id;
        $statement = $db->prepare($sql);
        $statement->execute();
	echo 'Deleted record' . $this->id;
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

class tableNew {
    static public function htmlTable($rows) {
        $db=dbConn::getConnection();
        //$tableName = get_called_class();
        $sql = 'show columns FROM accounts' ;
        $statement = $db->prepare($sql);
        $statement->execute();
        $head= $statement->fetchAll(PDO::FETCH_COLUMN);
        //return $head;
        echo "<table border = 2>";
        foreach ($head as $head1) {
            echo "<th>$head1</th>";
        }
        foreach ($rows as $row) {
            echo "<tr>";
            foreach ($row as $column) {
                echo "<td>$column</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
}

$records = accounts::findAll();

//print_r($records);
echo tableNew::htmlTable($records);
$records = todos::findAll();
$record = todos::findOne(1);
$record = new todo();
$record->message = 'some task';
$record->isdone = 0;
print_r($record);
$record = todos::create();
print_r($record);

?>
