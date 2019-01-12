<?php

class View
{

	/** @var Auth $auth */
	private $auth;

	/** @var Session $session */
	private $session;

	/** @var Twig_Environment $twig */
	private $twig;

	/** @var Url $url */
	private $url;

	private $args;

	private static $INSTANCE;

	public function __construct()
	{
		$this->auth = Auth::getInstance();
		$this->session = Session::getInstance();
		$this->url = Url::getInstance();
	}

	public static function getInstance(): View
	{
		if (self::$INSTANCE === NULL) {
			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}

	public function render($template, array $args = []): string
	{
		$this->args = $args;
		$this->args['debug'] = true;	// TODO: Remove
		$this->addFrontendData();

		$loader = new Twig_Loader_Filesystem('resources/views');
		$this->twig = new \Twig_Environment($loader, $this->args);
		$this->twig->addExtension(new Twig_Extension_Debug());	// TODO: Remove
		$this->registerMacros();

		return $this->twig->render($template, $this->args);
	}

	private function addFrontendData()
	{
		$this->auth->updateUserData();
		$this->args['user'] = $this->session->has('user') ? $this->session->get('user') : NULL;
		$this->args['alerts'] = $this->session->has('alerts') ? $this->session->getAndRemove('alerts') : NULL;
	}

	private function registerMacros()
	{
		$this->twig->addFunction(new \Twig_Function('url', function (string $controller, string $action, array $parameters = []) {
			return $this->url->generate($controller, $action, $parameters);
		}));

		$this->twig->addFunction(new \Twig_Function('isLogged', function () {
			return $this->auth->isLogged();
		}));

		$this->twig->addFunction(new \Twig_Function('isNormal', function () {
			return $this->auth->isNormal();
		}));

		$this->twig->addFunction(new \Twig_Function('isReviewer', function () {
			return $this->auth->isReviewer();
		}));

		$this->twig->addFunction(new \Twig_Function('isAdmin', function () {
			return $this->auth->isAdmin();
		}));
	}

}
