<?php

class ArticleModel extends BaseModel
{

	const ARTICLE_STATE_NEW = 0;
	const ARTICLE_STATE_REJECTED = 1;
	const ARTICLE_STATE_ACCEPTED = 2;

	private static $INSTANCE;

	public static function getInstance(): ArticleModel
	{
		if (self::$INSTANCE === NULL) {
			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}

	public function getPublicArticles(): array
	{
		$query = '
			SELECT articles.*, author.name AS authorName, author.surname AS authorSurname
			FROM articles
			INNER JOIN users AS author ON author.id = articles.author_id
			WHERE state = :state
			ORDER BY articles.rating;
		';
		return $this->fetchAll($query, ['state' => self::ARTICLE_STATE_ACCEPTED]);
	}

	public function getArticleById(string $article_id)
	{
		$query = 'SELECT * FROM articles WHERE id = :article_id';
		return $this->fetch($query, ['article_id' => $article_id]);
	}

	public function getPublicArticleById(string $article_id): array
	{
		$query = '
			SELECT articles.*, author.name AS authorName, author.surname AS authorSurname
			FROM articles
			INNER JOIN users AS author ON author.id = articles.author_id
			WHERE state = :state AND articles.id = :article_id
		';
		return $this->fetch($query, [
			'article_id' => $article_id,
			'state' => self::ARTICLE_STATE_ACCEPTED,
		]);
	}

	public function isAuthorArticle(string $author_id, string $article_id): bool
	{
		$query = 'SELECT COUNT(id) AS count FROM articles WHERE id = :article_id AND author_id = :author_id';
		return $this->fetch($query, [
				'author_id' => $author_id,
				'article_id' => $article_id,
			])['count'] >= 1;
	}

	public function getAuthorArticles($user_id): array
	{
		$query = 'SELECT * FROM articles WHERE author_id = :user_id';
		return $this->fetchAll($query, ['user_id' => $user_id]);
	}

	public function getAllArticles(): array
	{
		$query = '
			SELECT articles.*, author.name AS authorName, author.surname AS authorSurname,
				review1.criteria1 AS r1c1, review1.criteria2 AS r1c2, review1.criteria3 AS r1c3,
				review2.criteria1 AS r2c1, review2.criteria2 AS r2c2, review2.criteria3 AS r2c3,
				review3.criteria1 AS r3c1, review3.criteria2 AS r3c2, review3.criteria3 AS r3c3
			FROM articles
			INNER JOIN users AS author ON articles.author_id = author.id
			LEFT JOIN article_reviews AS review1 ON articles.review1_id = review1.id
			LEFT JOIN article_reviews AS review2 ON articles.review2_id = review2.id
			LEFT JOIN article_reviews AS review3 ON articles.review3_id = review3.id
		';
		return $this->fetchAll($query);
	}

	public function createArticle($articleName, $abstract, $file, $author_id): bool
	{
		$extension = pathinfo($file['name'],PATHINFO_EXTENSION);
		$targetFileName = substr(md5(mt_rand()), 0, 20) . '.' . $extension;
		$targetFile = __DIR__ . '/../upload/' . $targetFileName;

		if (file_exists($targetFile)) {
			return FALSE;
		}

		if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
			return FALSE;
		}

		$query = '
			INSERT INTO articles (name, abstract, file, state, rating, author_id)
			VALUES (:name, :abstract, :file, 0, 0, :author_id)
		';
		return $this->run($query, [
			'name' => $articleName,
			'abstract' => $abstract,
			'file' => $targetFileName,
			'author_id' => $author_id,
		]);
	}

	public function removeArticle($article_id): bool
	{
		$query = 'DELETE FROM articles WHERE id = :article_id';
		return $this->run($query, [
			'article_id' => $article_id,
		]);
	}

	public function editArticle($article_id, $name, $abstract): bool
	{
		$query = '
			UPDATE articles
			SET name = :name, abstract = :abstract
			WHERE id = :article_id
		';
		return $this->run($query, [
			'name' => $name,
			'abstract' => $abstract,
			'article_id' => $article_id,
		]);
	}

	public function isArticleInReview($article_id): bool
	{
	    $query = 'SELECT COUNT(id) AS count FROM articles WHERE id = :article_id AND state = :state';
	    return $this->fetch($query, [
	    	'article_id' => $article_id,
			'state' => self::ARTICLE_STATE_NEW,
		])['count'] >= 1;
	}

	public function publish($article_id): bool
	{
		$query = 'SELECT
				review1.criteria1 AS r1c1, review1.criteria2 AS r1c2, review1.criteria3 AS r1c3,
				review2.criteria1 AS r2c1, review2.criteria2 AS r2c2, review2.criteria3 AS r2c3,
				review3.criteria1 AS r3c1, review3.criteria2 AS r3c2, review3.criteria3 AS r3c3
			FROM articles
			INNER JOIN users AS author ON articles.author_id = author.id
			LEFT JOIN article_reviews AS review1 ON articles.review1_id = review1.id
			LEFT JOIN article_reviews AS review2 ON articles.review2_id = review2.id
			LEFT JOIN article_reviews AS review3 ON articles.review3_id = review3.id
			WHERE articles.id = :article_id;
		';
		$ratings = $this->fetch($query, ['article_id' => $article_id]);
		$rating = $ratings['r1c1'] + $ratings['r1c2'] + $ratings['r1c3']
			+ $ratings['r2c1'] + $ratings['r2c2'] + $ratings['r2c3']
			+ $ratings['r3c1'] + $ratings['r3c2'] + $ratings['r3c3'];
		$rating = round($rating / 9.0, 2);

	    $query = 'UPDATE articles SET state = :state, rating = :rating WHERE id = :article_id';
	    return $this->run($query, [
	    	'article_id' => $article_id,
			'state' => self::ARTICLE_STATE_ACCEPTED,
			'rating' => $rating,
		]);
	}

	public function reject($article_id): bool
	{
		$query = 'UPDATE articles SET state = :state WHERE id = :article_id';
		return $this->run($query, [
			'article_id' => $article_id,
			'state' => self::ARTICLE_STATE_REJECTED,
		]);
	}

}
