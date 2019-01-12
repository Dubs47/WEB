<?php

class BaseController
{

	/** @var ArticleModel $articleModel */
	protected $articleModel;

	/** @var UserModel $userModel */
	protected $userModel;

	/** @var ReviewRequestModel $reviewRequestModel */
	protected $reviewRequestModel;

	/** @var Auth $auth */
	protected $auth;

	/** @var Input $input */
	protected $input;

	/** @var View $view */
	protected $view;

	/** @var Session $session */
	protected $session;

	/** @var Url $url */
	private $url;

	public function __construct()
	{
		$this->articleModel = ArticleModel::getInstance();
		$this->userModel = UserModel::getInstance();
		$this->reviewRequestModel = ReviewRequestModel::getInstance();
		$this->auth = Auth::getInstance();
		$this->input = Input::getInstance();
		$this->view = View::getInstance();
		$this->session = Session::getInstance();
		$this->url = Url::getInstance();
	}

	protected function redirect(string $controller, string $action, array $parameters = [])
	{
		header('Location: ' . $this->url->generate($controller, $action, $parameters), TRUE, 302);
		die();
	}

	protected function redirectToLogin()
	{
		$this->redirect('user', 'login');
	}

	protected function alert(string $type, string $content)
	{
		$this->session->add('alerts', [
			'type' => $type,
			'content' => $content,
		]);
	}

	protected function loggedOnly()
	{
		if (!$this->auth->isLogged()) {
			$this->alert('danger', 'Nejste přihlášeni');
			$this->redirectToLogin();
		}
	}

	protected function normalOnly()
	{
		$this->loggedOnly();
		if (!$this->auth->isNormal()) {
			$this->alert('danger', 'Nejste běžný uživatel');
			$this->redirect('user', 'dashboard');
		}
	}

	protected function reviewerOnly()
	{
		$this->loggedOnly();
		if (!$this->auth->isReviewer()) {
			$this->alert('danger', 'Nejste recenzent');
			$this->redirect('user', 'dashboard');
		}
	}

	protected function adminOnly()
	{
		$this->loggedOnly();
		if (!$this->auth->isAdmin()) {
			$this->alert('danger', 'Nejste administrátor');
			$this->redirect('user', 'dashboard');
		}
	}

}
