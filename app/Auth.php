<?php

class Auth
{

	private static $INSTANCE;

	/** @var ArticleModel $articleModel */
	private $articleModel;

	/** @var UserModel $userModel */
	private $userModel;

	/** @var ReviewRequestModel $reviewRequestModel */
	private $reviewRequestModel;

	/** @var Session $session */
	private $session;

	const USER_ROLE_NORMAL = 0;
	const USER_ROLE_REVIEWER = 1;
	const USER_ROLE_ADMIN = 2;

	public function __construct()
	{
		$this->articleModel = ArticleModel::getInstance();
		$this->userModel = UserModel::getInstance();
		$this->reviewRequestModel = ReviewRequestModel::getInstance();
		$this->session = Session::getInstance();
	}

	public static function getInstance(): Auth
	{
		if (self::$INSTANCE === NULL) {
			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}

	public function register(string $email, string $password, string $name, string $surname): bool
	{
		return $this->userModel->createUser(
			$email,
			password_hash($password, PASSWORD_BCRYPT),
			$name,
			$surname
		);
	}

	public function login(string $email, string $password): bool
	{
		$user = $this->userModel->getUserByEmail($email);
		if (empty($user) || $user['is_blocked'] == '1' ||  !password_verify($password, $user['password'])) {
			return FALSE;
		}

		$this->session->set('user', $user);
		return TRUE;
	}

	public function logout()
	{
		$this->session->remove('user');
	}

	public function isLogged(): bool
	{
		return $this->session->has('user');
	}

	public function isNormal(): bool
	{
		return $this->isLogged() && $this->isRole(self::USER_ROLE_NORMAL);
	}

	public function isReviewer(): bool
	{
		return $this->isLogged() && $this->isRole(self::USER_ROLE_REVIEWER);
	}

	public function isAdmin(): bool
	{
		return $this->isLogged() && $this->isRole(self::USER_ROLE_ADMIN);
	}

	private function isRole(int $role): bool
	{
		return $this->session->getInner('user', 'role') == $role;
	}

	public function isAuthorOf(int $article_id): bool
	{
		return $this->isLogged() && $this->articleModel->isAuthorArticle(
				$this->session->getInner('user', 'id'), $article_id
			);
	}

	public function isReviewerOf(int $request_id)
	{
		return $this->isLogged() && $this->reviewRequestModel->isReviewerArticle(
				$this->session->getInner('user', 'id'), $request_id
			);
	}

	public function updateUserData()
	{
		if (!$this->isLogged()) {
			return;
		}

		$user = $this->userModel->getUserById($this->session->getInner('user', 'id'));
		$this->session->set('user', $user);
	}

}
