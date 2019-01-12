<?php

class HomeController extends BaseController
{

	/**
	 * Landing page
	 */
	public function index()
	{
		echo $this->view->render('landing.html.twig');
	}

	/**
	 * Shows public articles
	 */
	public function articles()
	{
		$articles = $this->articleModel->getPublicArticles();
		echo $this->view->render('articles.html.twig', ['articles' => $articles]);
	}

	public function articleDetail() {
		if (!$this->input->hasGet('article_id')) {
			$this->alert('danger', 'Článek neexistuje');
			$this->redirect('home', 'articles');
		}
		$article_id = $this->input->getGet('article_id');
	    $article = $this->articleModel->getPublicArticleById($article_id);

	    if (empty($article)) {
			$this->alert('danger', 'Článek neexistuje');
			$this->redirect('home', 'articles');
		}

	    echo $this->view->render('articleDetail.html.twig', ['article' => $article]);
	}

}