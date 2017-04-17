<?php

/**
 * Interface for database class
 */
interface iDatabase {
	public function getPdo ();
}

/**
 * Class for database actions
 * @author Mandien
 */
class Database implements iDatabase {

	/*
	private $host;
	private $db;
	private $user;
	private $pass;
	private $dsn;
	private $pdo;
	*/
	
	private $params;

	/**
	 *
	 * @param string $db
	 * @param string $user
	 * @param string $pass
	 * @param string $host
	 */
	public function __construct($db, $user, $pass, $host = "localhost")
	{
		$this->params = new stdClass();
		$this->params->host = $host;
		$this->params->db   = $db;
		$this->params->user = $user;
		$this->params->pass = $pass;
		
		/*
		$this->host = $host;
		$this->db   = $db;
		$this->user = $user;
		$this->pass = $pass;
		*/

		// echo $this->params->host;
		// die();
		
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
	
	public function getParams ()
	{
		return $this->params;
	}

	public function getPdo ()
	{
		return $this->pdo;
	}
}
?>