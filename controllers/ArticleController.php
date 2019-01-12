<?php

class ArticleController extends BaseController
{

	public function normalArticleList()
	{
		$this->normalOnly();

		$articles = $this->articleModel->getAuthorArticles($this->session->getInner('user', 'id'));
		echo $this->view->render('normal/articleList.html.twig', ['articles' => $articles]);
	}

	public function normalArticleDetail()
	{
		$this->normalOnly();

		if (!$this->input->hasGet('article_id')) {
			$this->alert('danger', 'Článek neexistuje');
			$this->redirect('article', 'normalArticleList');
		}

		$article_id = $this->input->getGet('article_id');
		if (!$this->auth->isAuthorOf($article_id)) {
			$this->alert('danger', 'Nejste autor článku');
			$this->redirect('user', 'dashboard');
		}

		$article = $this->articleModel->getArticleById($article_id);
		if (empty($article)) {
			$this->alert('danger', 'Článek neexistuje');
			$this->redirect('article', 'normalArticleList');
		}

		echo $this->view->render('normal/articleDetail.html.twig', ['article' => $article]);
	}

	public function reviewerArticleList()
	{
		$this->reviewerOnly();

		$requests = $this->reviewRequestModel->getReviewerRequests($this->session->getInner('user', 'id'));
		echo $this->view->render('reviewer/articleList.html.twig', ['requests' => $requests]);
	}

	public function adminArticleList()
	{
		$this->adminOnly();

		$articles = $this->articleModel->getAllArticles();
		$reviewers = $this->userModel->getAllReviewers();
		echo $this->view->render('admin/articleList.html.twig', [
			'articles' => $articles,
			'reviewers' => $reviewers,
		]);
	}

	public function adminArticleDetail()
	{
		$this->adminOnly();

		if (!$this->input->hasGet('article_id')) {
			$this->alert('danger', 'Článek neexistuje');
			$this->redirect('article', 'adminArticleList');
		}

		$article_id = $this->input->getGet('article_id');
		$article = $this->articleModel->getArticleById($article_id);
		if (empty($article)) {
			$this->alert('danger', 'Článek neexistuje');
			$this->redirect('article', 'adminArticleList');
		}

		echo $this->view->render('admin/articleDetail.html.twig', ['article' => $article]);
	}

	public function normalArticleCreate()
	{
		$this->normalOnly();

		echo $this->view->render('normal/articleCreate.html.twig');
	}

	public function normalArticleCreateDo()
	{
		$this->normalOnly();

		if (
			!$this->input->hasPost('abstract')
			|| !$this->input->hasPost('name')
			|| !$this->input->hasFiles('pdf')
		) {
			$this->alert('danger', 'Přidávání článku selhalo');
			$this->redirect('article', 'normalArticleList');
		}

		$abstract = $this->input->getPost('abstract');
		$name = $this->input->getPost('name');
		$file = $this->input->getFiles('pdf');

		if (!$this->articleModel->createArticle($name, $abstract, $file, $this->session->getInner('user', 'id'))) {
			$this->alert('danger', 'Přidávání článku selhalo');
			$this->redirect('article', 'normalArticleList');
		}

		$this->alert('success', 'Článek vytvořen');
		$this->redirect('article', 'normalArticleList');
	}

	public function normalArticleEdit()
	{
		$this->normalOnly();
		if (!$this->input->hasGet('article_id')) {
			$this->alert('danger', 'Článek neexistuje');
			$this->redirect('article', 'normalArticleList');
		}

		$article_id = $this->input->getGet('article_id');
		if (!$this->auth->isAuthorOf($article_id)) {
			$this->alert('danger', 'Nejste autorem tohoto článku');
			$this->redirect('article', 'normalArticleList');
		}

		$article = $this->articleModel->getArticleById($article_id);
		if (empty($article)) {
			$this->alert('danger', 'Článek neexistuje');
			$this->redirect('article', 'normalArticleList');
		}

		echo $this->view->render('normal/articleEdit.html.twig', ['article' => $article]);
	}

	public function normalArticleEditDo()
	{
		$this->normalOnly();

		if (
			!$this->input->hasPost('abstract')
			|| !$this->input->hasPost('name')
		) {
			$this->alert('danger', 'Upravování článku selhalo');
			$this->redirect('article', 'normalArticleList');
		}

		if (!$this->input->hasGet('article_id')) {
			$this->alert('danger', 'Článek neexistuje');
			$this->redirect('article', 'normalArticleList');
		}

		$article_id = $this->input->getGet('article_id');
		if (!$this->auth->isAuthorOf($article_id)) {
			$this->alert('danger', 'Nejste autorem tohoto článku');
			$this->redirect('article', 'normalArticleList');
		}

		if (!$this->articleModel->isArticleInReview($article_id)) {
			$this->alert('danger', 'Článek už nemůžete upravovat');
			$this->redirect('article', 'normalArticleList');
		}

		$abstract = $this->input->getPost('abstract');
		$name = $this->input->getPost('name');

		if (!$this->articleModel->editArticle($article_id, $name, $abstract)) {
			$this->alert('danger', 'Upravování článku selhalo');
			$this->redirect('article', 'normalArticleList');
		}

		$this->alert('success', 'Článek upraven');
		$this->redirect('article', 'normalArticleList');
	}

	public function normalArticleRemoveDo()
	{
		$this->normalOnly();
		if (!$this->input->hasGet('article_id')) {
			$this->alert('danger', 'Článek neexistuje');
			$this->redirect('article', 'normalArticleList');
		}

		$article_id = $this->input->getGet('article_id');
		if (!$this->auth->isAuthorOf($article_id)) {
			$this->alert('danger', 'Nejste autorem tohoto článku');
			$this->redirect('article', 'normalArticleList');
		}

		if (!$this->articleModel->removeArticle($article_id)) {
			$this->alert('danger', 'Mazání článku selhalo');
			$this->redirect('article', 'normalArticleList');
		}

		$this->alert('success', 'Článek odstraněn');
		$this->redirect('article', 'normalArticleList');
	}

	public function review()
	{
	    $this->reviewerOnly();

		if (!$this->input->hasGet('request_id')) {
			$this->alert('danger', 'Žádost neexistuje');
			$this->redirect('article', 'reviewerArticleList');
		}

		$request_id = $this->input->getGet('request_id');

		if (!$this->auth->isReviewerOf($request_id)) {
			$this->alert('danger', 'Tento článek vám nebyl přidělen k recenzi');
			$this->redirect('article', 'reviewerArticleList');
		}

		$request = $this->reviewRequestModel->getRequestById($request_id);
		if (empty($request)) {
			$this->alert('danger', 'Žádost neexistuje');
			$this->redirect('article', 'reviewerArticleList');
		}

		echo $this->view->render('reviewer/articleReview.html.twig', ['request' => $request]);
	}

	public function reviewDo()
	{
	    $this->reviewerOnly();

		if (
			!$this->input->hasPost('request_id')
			|| !$this->input->hasPost('originality')
			|| !$this->input->hasPost('quality')
			|| !$this->input->hasPost('recommendation')
		) {
			$this->alert('danger', 'Recenze článku selhala');
			$this->redirect('article', 'reviewerArticleList');
		}

		$request_id = $this->input->getPost('request_id');
		$originality = $this->input->getPost('originality');
		$quality = $this->input->getPost('quality');
		$recommendation = $this->input->getPost('recommendation');

		if ($this->reviewRequestModel->createReview($request_id, $originality, $quality, $recommendation)) {
			$this->alert('success', 'Recenze článku odeslána');
			$this->redirect('article', 'reviewerArticleList');
		}

		$this->alert('danger', 'Recenze článku selhala');
		$this->redirect('article', 'reviewerArticleList');
	}

}