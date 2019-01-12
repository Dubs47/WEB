<?php

class UserModel extends BaseModel
{

	private static $INSTANCE;

	public static function getInstance(): UserModel
	{
		if (self::$INSTANCE === NULL) {
			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}

	public function createUser(string $email, string $password, string $name, string $surname): bool
	{
		$query = 'INSERT INTO users (email, password, name, surname) VALUES (:email, :password, :name, :surname)';
		return $this->run($query, [
			'email' => $email,
			'password' => $password,
			'name' => $name,
			'surname' => $surname,
		]);
	}

	public function getUserById(string $id)
	{
		$query = "SELECT * FROM users WHERE id = :id";
		$data = ['id' => $id];
		return $this->fetch($query, $data);
	}

	public function getUserByEmail(string $email)
	{
		$query = "SELECT * FROM users WHERE email = :email";
		$data = ['email' => $email];
		return $this->fetch($query, $data);
	}

	public function isUserNormal($user_id): bool
	{
		return $this->isUserRole($user_id, Auth::USER_ROLE_NORMAL);
	}

	public function isUserReviewer($user_id): bool
	{
		return $this->isUserRole($user_id, Auth::USER_ROLE_REVIEWER);
	}

	public function isUserAdmin($user_id): bool
	{
		return $this->isUserRole($user_id, Auth::USER_ROLE_ADMIN);
	}

	private function isUserRole($user_id, int $role)
	{
		$query = 'SELECT COUNT(id) AS count FROM users WHERE id = :user_id AND role = :role';
		return $this->fetch($query, [
				'user_id' => $user_id,
				'role' => $role,
			])['count'] >= 1;
	}

	public function getAllReviewers()
	{
		$query = 'SELECT * FROM users WHERE role = :role';
		return $this->fetchAll($query, ['role' => Auth::USER_ROLE_REVIEWER]);
	}

	public function changeRole($user_id, $role): bool
	{
		$query = 'UPDATE users SET role = :role WHERE id = :user_id';
		return $this->run($query, [
			'user_id' => $user_id,
			'role' => $role,
		]);
	}

	public function getAllUsers(): array
	{
		$query = 'SELECT * FROM users';
		return $this->fetchAll($query);
	}

	public function block($user_id): bool
	{
		$query = 'UPDATE users SET is_blocked = 1 WHERE id = :user_id';
		return $this->run($query, ['user_id' => $user_id]);
	}

}
