<?php

class Url
{

	private static $INSTANCE;

	public static function getInstance(): Url
	{
		if (self::$INSTANCE === NULL) {
			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}

	public function generate(string $controller, string $action, array $parameters = []): string
	{
		$url = 'index.php?c=' . $controller . '&a=' . $action;
		foreach ($parameters as $paramKey => $paramValue) {
			$url .= '&' . $paramKey . '=' . $paramValue;
		}
		return $url;
	}

}
