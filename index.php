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
        $sql = 'something';
        return $sql;
    }
    private function update() {
        $tableName='accounts';
	$sql = 'UPDATE' . $tableName . 'WHERE id=' . $id;
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
    public static function table()
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
    public static function table()
    {
        $this->tableName = 'todos';

    }
}

class tableNew {
    static public function htmlTable($head,$rows) {
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
        return $htmlTable;
    }
}

$records = accounts::findAll();
$records = todos::findAll();
$record = todos::findOne(1);
$record = new todo();
$record->message = 'some task';
$record->isdone = 0;
print_r($record);
$record = todos::create();
print_r($record);
print($htmlTable);
?>
