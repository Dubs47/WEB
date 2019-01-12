<?php

class Input
{

	private static $INSTANCE;

	public static function getInstance(): Input
	{
		if (self::$INSTANCE === NULL) {
			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}

	public function hasGet(string $key): bool
	{
		return isset($_GET[$key]);
	}

	public function hasPost(string $key): bool
	{
		return isset($_POST[$key]);
	}

	public function hasFiles(string $key): bool
	{
	    return isset($_FILES[$key]);
	}

	public function getGet(string $key): string
	{
		return $_GET[$key];
	}

	public function getPost(string $key): string
	{
		return $_POST[$key];
	}

	public function getFiles(string $key): array
	{
		return $_FILES[$key];
	}

	public function setGet(string $key, $value)
	{
		$_GET[$key] = $value;
	}

	public function setPost(string $key, $value)
	{
		$_POST[$key] = $value;
	}

}
