<?php

class BaseModel
{

	/** @var Database $database */
	private $database;

	public function __construct()
	{
		$this->database = Database::getInstance();
	}

	protected function fetch(string $query, array $data = []): array
	{
		$result = $this->database->query($query, $data)->fetch();
		return $result === FALSE ? [] : $result;
	}

	protected function fetchAll(string $query, array $data = []): array
	{
		return $this->database->query($query, $data)->fetchAll();
	}

	protected function run(string $query, array $data = []): bool
	{
		return $this->database->query($query, $data)->result();
	}

	protected function lastInsertId(): string
	{
		return $this->database->lastInsertId();
	}

}
