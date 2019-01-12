<?php

class AdminController extends BaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->adminOnly();
	}

	public function requestReview()
	{
		if (
			!$this->input->hasPost('article_id')
			|| !$this->input->hasPost('reviewer_id')
			|| !$this->input->hasPost('review_id')
		) {
			$this->alert('danger', 'Žádost o hodnocení selhala');
			$this->redirect('article', 'adminArticleList');
		}

		$article_id = $this->input->getPost('article_id');
		$reviewer_id = $this->input->getPost('reviewer_id');
		$review_id = $this->input->getPost('review_id');
		if ($this->reviewRequestModel->createRequest($article_id, $reviewer_id, $review_id)) {
			$this->alert('success', 'Žádost o hodnocení odeslána');
			$this->redirect('article', 'adminArticleList');
		}

		$this->alert('danger', 'Žádost o hodnocení selhala');
		$this->redirect('article', 'adminArticleList');
	}

	public function blockDo()
	{
		// TODO: Check for user_id
		$user_id = $this->input->getGet('user_id');
	    if ($this->userModel->block($user_id)) {
			$this->alert('success', 'Uživatel zablokován');
			$this->redirect('admin', 'userList');
		}

		$this->alert('danger', 'Error');
		$this->redirect('admin', 'userList');
	}

	public function promoteDo()
	{
		// TODO: Check for user_id
		$user_id = $this->input->getGet('user_id');
		if ($this->userModel->isUserNormal($user_id)) {
			if ($this->userModel->changeRole($user_id, Auth::USER_ROLE_REVIEWER)) {
				$this->alert('success', 'Uživatel povýšen');
				$this->redirect('admin', 'userList');
			}
		}

		$this->alert('danger', 'Error');
		$this->redirect('admin', 'userList');
	}

	public function demoteDo()
	{
		// TODO: Check for user_id
		$user_id = $this->input->getGet('user_id');
		if ($this->userModel->isUserReviewer($user_id)) {
			if ($this->userModel->changeRole($user_id, Auth::USER_ROLE_NORMAL)) {
				$this->alert('success', 'Uživateli byla odebrána práva');
				$this->redirect('admin', 'userList');
			}
		}

		$this->alert('danger', 'Error');
		$this->redirect('admin', 'userList');
	}

	public function userList()
	{
	    $users = $this->userModel->getAllUsers();
	    echo $this->view->render('admin/userList.html.twig', ['users' => $users]);
	}

	public function publishDo()
	{
	    $article_id = $this->input->getGet('article_id');
	    if ($this->articleModel->publish($article_id)) {
			$this->alert('success', 'Článek publikován');
			$this->redirect('article', 'adminArticleList');
		}

		$this->alert('danger', 'Error');
		$this->redirect('article', 'adminArticleList');
	}

	public function rejectDo()
	{
	    $article_id = $this->input->getGet('article_id');
	    if ($this->articleModel->reject($article_id)) {
			$this->alert('success', 'Článek zamítnut');
			$this->redirect('article', 'adminArticleList');
		}

		$this->alert('danger', 'Error');
		$this->redirect('article', 'adminArticleList');
	}

}