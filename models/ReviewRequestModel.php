<?php

class ReviewRequestModel extends BaseModel
{

	private static $INSTANCE;

	public static function getInstance(): ReviewRequestModel
	{
		if (self::$INSTANCE === NULL) {
			self::$INSTANCE = new self();
		}

		return self::$INSTANCE;
	}

	public function getRequestById($request_id): array
	{
		$query = '
			SELECT review_requests.*, article.name AS articleName, article.file AS articleFile, article.abstract AS articleAbstract
			FROM review_requests
			INNER JOIN articles AS article ON article.id = review_requests.article_id
			WHERE review_requests.id = :request_id
		';
		return $this->fetch($query, ['request_id' => $request_id]);
	}

	public function getReviewerRequests($reviewer_id): array
	{
		$query = '
			SELECT review_requests.*, article.name AS articleName, article.rating AS articleRating
			FROM review_requests
			INNER JOIN articles AS article ON article.id = review_requests.article_id
			WHERE reviewer_id = :reviewer_id
		';
		return $this->fetchAll($query, ['reviewer_id' => $reviewer_id]);
	}

	public function isReviewerArticle($reviewer_id, $request_id): bool
	{
		$query = 'SELECT COUNT(id) AS count FROM review_requests WHERE reviewer_id = :reviewer_id AND id = :request_id';
		return $this->fetch($query, [
				'reviewer_id' => $reviewer_id,
				'request_id' => $request_id,
			])['count'] >= 1;
	}

	public function createRequest($article_id, $reviewer_id, $review_id): bool
	{
		$query = 'INSERT INTO review_requests (article_id, reviewer_id, review_id) VALUES (:article_id, :reviewer_id, :review_id)';
		return $this->run($query, [
			'article_id' => $article_id,
			'reviewer_id' => $reviewer_id,
			'review_id' => $review_id,
		]);
	}

	public function createReview($request_id, $originality, $quality, $recommendation): bool
	{
		$request = $this->getRequest($request_id);
		$article_id = $request['article_id'];
		$reviewer_id = $request['reviewer_id'];
		$review_id = $request['review_id'];
		$query = '
			INSERT INTO article_reviews (article_id, reviewer_id, criteria1, criteria2, criteria3)
			VALUES (:article_id, :reviewer_id, :criteria1, :criteria2, :criteria3)
		';
		$result = $this->run($query, [
			'article_id' => $article_id,
			'reviewer_id' => $reviewer_id,
			'criteria1' => $originality,
			'criteria2' => $quality,
			'criteria3' => $recommendation,
		]);
		if (!$result) {
			return FALSE;
		}

		$insertId = $this->lastInsertId();
		$query = '
			UPDATE articles SET review' . $review_id . '_id = :insert_id
			WHERE articles.id = :article_id;
		';
		$result = $this->run($query, [
			'insert_id' => $insertId,
			'article_id' => $article_id,
		]);
		if (!$result) {
			return FALSE;
		}

		$query = 'DELETE FROM review_requests WHERE id = :request_id';
		return $this->run($query, ['request_id' => $request_id]);
	}

	private function getRequest($request_id) {
		$query = '
			SELECT article_id, reviewer_id, review_id
			FROM review_requests
			WHERE review_requests.id = :request_id
		';
		return $this->fetch($query, ['request_id' => $request_id]);
	}

}