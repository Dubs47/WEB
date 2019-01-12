<?php

class Database
{

	/** @var \PDO $pdo */
	private $pdo;

	/** @var \PDOStatement $statement */
	private $statement;

	/** @var boolean $result Result of query */
	private $result;

	private static $INSTANCE;

	const DB_HOST = '127.0.0.1';
	const DB_USERNAME = 'root';
	const DB_PASSWORD = '';
	const DB_NAME = 'konference';

	private function __construct()
	{
		$dsn = 'mysql:dbname=' . self::DB_NAME . ';host=' . self::DB_HOST;
		$settings = [
			\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
			\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
			\PDO::ATTR_EMULATE_PREPARES => FALSE,
		];
		$this->pdo = new \PDO($dsn, self::DB_USERNAME, self::DB_PASSWORD, $settings);
	}

	public function query(string $query, array $data = []): self
	{
		$this->statement = $this->pdo->prepare($query);
		$this->result = $this->statement->execute($data);
		return $this;
	}

	public static function getInstance(): Database
	{
		if (self::$INSTANCE === NULL) {
			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}

	public function result(): bool
	{
		return $this->result;
	}

	public function fetch()
	{
		return $this->statement->fetch();
	}

	public function fetchAll(): array
	{
		return $this->statement->fetchAll();
	}

	public function lastInsertId(): string
	{
		return $this->pdo->lastInsertId();
	}

}
