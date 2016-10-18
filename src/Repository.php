<?php

declare(strict_types=1);

namespace saiik;

/**
 * @package saiik\
 * @author Tobias Fuchs <saikon@hotmail.de>
 * @version 1.0
 */
class Repository {

	/**
	 * @var int $id
	 *
	 */
	protected $id;

	/**
	 * @var string $name
	 *
	 */
	protected $name;

	/**
	 * @var string $fullName
	 *
	 */	
	protected $fullName;

	/**
	 * @var boolean $private
	 *
	 */	
	protected $private;

	/**
	 * @var string $description
	 *
	 */	
	protected $description;

	/**
	 * @var string $url
	 *
	 */
	protected $url;

	/**
	 * @var \DateTime $created_at
	 *
	 */
	protected $created_at;

	/**
	 * @var \DateTime $updated_at
	 *
	 */
	protected $updated_at;

	/**
	 * @var int $stars
	 *
	 */
	protected $stars;

	/**
	 * @var int $watchers
	 *
	 */
	protected $watchers;

	/**
	 * @var string $language
	 *
	 */
	protected $language;

	/**
	 * @var int $forks
	 *
	 */
	protected $forks;

	/**
	 * @var int issues
	 *
	 */
	protected $issues;

	public function __construct(\stdClass $repo) {

		$this->id = $repo->id;
		$this->owner = $repo->owner->login;
		$this->name = $repo->name;
		$this->fullName = $repo->full_name;
		$this->private = $repo->private;
		$this->description = $repo->description;
		$this->url = $repo->html_url;
		$this->created_at = new \DateTime($repo->created_at);
		$this->updated_at = new \DateTime($repo->updated_at);
		$this->stars = $repo->stargazers_count;
		$this->watchers = $repo->watchers_count;
		$this->language = $repo->language;
		$this->forks = $repo->forks_count;
		$this->issues = $repo->open_issues_count;
	}

	/**
	 * Check if repo is private or not
	 *
	 * @return boolean
	 */
	public function isPrivate() {

		return $this->private;
	}

	public function __get($name) {
		if(isset($this->$name)) 
			return $this->$name;

		return false;
	}


}