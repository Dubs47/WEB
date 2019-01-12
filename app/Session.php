<?php

class Session
{

	private static $INSTANCE;

	public static function getInstance(): Session
	{
		if (self::$INSTANCE === NULL) {
			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}

	public function has(string $key): bool
	{
		return isset($_SESSION[$key]);
	}

	public function get(string $key)
	{
		return $_SESSION[$key];
	}

	public function getAndRemove(string $key)
	{
		$out = $this->get($key);
		$this->remove($key);
		return $out;
	}

	public function set(string $key, $value)
	{
		$_SESSION[$key] = $value;
	}

	public function add(string $key, $value)
	{
		$_SESSION[$key][] = $value;
	}

	public function getInner(string $key, string $innerKey)
	{
		return $_SESSION[$key][$innerKey];
	}

	public function remove(string $key)
	{
		unset($_SESSION[$key]);
	}

}
