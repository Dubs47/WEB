<?php

ini_set('display_errors', 1);
session_start();

require_once __DIR__ . '/vendor/autoload.php';

require_once 'requires.php';

$availableControllers = [
	'admin' => AdminController::class,
	'article' => ArticleController::class,
	'user' => UserController::class,
	'home' => HomeController::class,
];

$action = $_GET['a'] ?? 'index';
$controllerGet = $_GET['c'] ?? 'home';
if (!isset($availableControllers[$controllerGet])) {
	$controllerGet = 'home';
}
$controller = new $availableControllers[$controllerGet]();
if (!method_exists($controller, $action)) {
	// TODO: 404
	die();
}

$controller->$action();
