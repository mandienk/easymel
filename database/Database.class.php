<?php
/**
 * Provides action with database
 * @author	Ma'ndien KAKEZ (m.kakez@gmail.com)
 */

/**
 * Interface for database class
 */
interface iDatabase {
	public function get ($tableName, $fieldName, $value);
	public function getPdo ();
	public function getParams ();
}

/**
 * Class for database actions 
 * 
 * Notice : you can implement your own dabatase class according to iDatabase interface
 */
class Database implements iDatabase {

	private $params;

	public function __construct($db, $user, $pass, $host = "localhost")
	{
		$this->params = new stdClass();
		$this->params->host = $host;
		$this->params->db   = $db;
		$this->params->user = $user;
		$this->params->pass = $pass;
				
		$dsn = "mysql:host=".$this->params->host.";dbname=".$this->params->db;
		$opt = [
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Enable exception
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // Return fetched data as array of object
				PDO::ATTR_EMULATE_PREPARES   => false,
		];

		try
		{
			$this->pdo = new PDO($dsn, $this->params->user, $this->params->pass, $opt);
		}
		catch(Exception $e)
		{
			die($e->getMessage()); // TODO : log this message instead of displaying it
			die("Connexion to database failed");
		}
	}
	
	/**
	 * Function to get data from table
	 * @param string	$tableName
	 * @param string	$fieldName
	 * @param integer	$id
	 */
	public function get ($tableName, $fieldName, $value)
	{		
		$request = "SELECT * FROM ".$tableName." WHERE ".$fieldName." = :value";				
		$stmt = $this->getPdo()->prepare($request);
		$stmt->bindParam (':value', $value, PDO::PARAM_INT);		
		$stmt->execute();
		$result = $stmt->fetchAll();
		// We store the data into the values attribute according to the getFormFromTable () definition
		$obj = new stdClass();
		$obj->objValues = new stdClass();
		$obj->objValues = (isset($result[0]))? $result[0] : null;
		return $obj;
	}
	
	/**
	 * Function to get all parameters
	 */
	public function getParams ()
	{
		return $this->params;
	}

	/**
	 * Function to get the PDO connection object
	 */
	public function getPdo ()
	{
		return $this->pdo;
	}
}
?>