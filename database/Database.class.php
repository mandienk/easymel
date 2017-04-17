<?php 
class Database {

	private $host;
	private $db;
	private $user;
	private $pass;
	private $dsn;
	private $pdo;

	/**
	 *
	 * @param unknown $db
	 * @param unknown $user
	 * @param unknown $pass
	 * @param string $host
	 */
	public function __construct($db, $user, $pass, $host = "localhost")
	{
		$this->host = $host;
		$this->db   = $db; // 'mcbrains';
		$this->user = $user; // 'mcbrains';
		$this->pass = $pass; // 'mcbrains1';

		$dsn = "mysql:host=$this->host;dbname=$this->db";
		$opt = [
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
				PDO::ATTR_EMULATE_PREPARES   => false,
		];

		// return new PDO($dsn, $this->user, $this->pass, $opt);
		try
		{
			$this->pdo = new PDO($dsn, $this->user, $this->pass, $opt);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
			die("Impossible de se connecter à la base de données");
		}
		// return $this->pdo;
	}

	public function getPdo ()
	{
		return $this->pdo;
	}
}
?>