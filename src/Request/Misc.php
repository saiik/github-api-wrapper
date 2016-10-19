<?php

declare(strict_types=1);

namespace saiik\Request;

/**
 * @package saiik\Request
 * @author Tobias Fuchs <saikon@hotmail.de>
 * @version 1.0
 */
trait Misc {
	/**
	 * Get your current rate limit
	 *
	 * @return array
	 */
	public function getRateLimit() {
		$limit = $this->request('rate_limit');

		return $limit ?? false;
	}

	/**
	 * Get all available .gitignore templates
	 *
	 * @return array
	 */
	public function getGitIgnoreTemplates() {
		$ignores = $this->request(sprintf('gitignore/templates'));

		return $ignores ?? false;
	}

	/**
	 * Get a .gitignore template
	 *
	 * @param string $template
	 * @return \stdObject
	 */
	public function getGitIgnoreTemplate(string $template) {
		$ignore = $this->request(sprintf('gitignore/templates/%s', $template));

		return $ignore ?? false;
	}

	/** 
	 * Parse a README.md file
	 *
	 * @param string $readMe
	 * @return string
	 */
	public function parseReadMe(string $readMe) {
		$post = $this->request('markdown', ['text' => $readMe], self::METHOD_POST);
		
		return $post;
	}

	/**
	 * Get github meta information
	 *
	 * @return \stdClass
	 */
	public function getMeta() {
		$meta = $this->request('meta');

		return $meta ?? false;
	}

	/**
	 * Get github emojis
	 *
	 * @return \stdClass
	 */
	public function getEmojis() {
		$emojis = $this->request('emojis');

		return $emojis ?? false;
	}
}