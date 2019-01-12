<?php

class UserController extends BaseController
{

	public function register()
	{
		if ($this->auth->isLogged()) {
			$this->redirect('user', 'dashboard');
		}

		echo $this->view->render('register.html.twig');
	}

	public function registerDo() {
		if (
			!$this->input->hasPost('email')
			|| !$this->input->hasPost('password')
			|| !$this->input->hasPost('name')
			|| !$this->input->hasPost('surname')
		) {
			$this->alert('danger', 'Registrace selhala');
			$this->redirect('user', 'register');
		}

		$email = $this->input->getPost('email');
		$password = $this->input->getPost('password');
		$name = $this->input->getPost('name');
		$surname = $this->input->getPost('surname');

		if ($this->auth->register($email, $password, $name, $surname)) {
			$this->alert('success', 'Registrace úspěšná');
			$this->redirect('user', 'login');
		}

		$this->alert('danger', 'Registrace selhala');
		$this->redirect('user', 'register');
	}

	public function login()
	{
		if ($this->auth->isLogged()) {
			$this->redirect('user', 'dashboard');
		}

		echo $this->view->render('login.html.twig');
	}

	public function loginDo()
	{
		if (!$this->input->hasPost('email') || !$this->input->hasPost('password')) {
			$this->alert('danger', 'Přihlášení selhalo');
			$this->redirectToLogin();
		}

		$email = $this->input->getPost('email');
		$password = $this->input->getPost('password');
		if ($this->auth->login($email, $password)) {
			$this->alert('success', 'Přihlášení úspěšné');
			$this->redirect('user', 'dashboard');
		}

		$this->alert('danger', 'Přihlášení selhalo');
		$this->redirect('user', 'login');
	}

	public function logout()
	{
		$this->loggedOnly();

		$this->alert('success', 'Odhlášení úspěšné');
		$this->auth->logout();
		$this->redirectToLogin();
	}

	public function dashboard()
	{
		$this->loggedOnly();

		echo $this->view->render('dashboard.html.twig');
	}

}