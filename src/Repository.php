<?php

declare(strict_types=1);

namespace VOLL;

class Repository {

	/**
	 * @var array $repo
	 *
	 */
	protected $repo;

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

	public function __construct(\stdClass $repo) {
		$this->repo = $repo;

		$this->id = $this->repo->id;
		$this->owner = $this->repo->owner->login;
		$this->name = $this->repo->name;
		$this->fullName = $this->repo->full_name;
		$this->private = $this->repo->private;
		$this->description = $this->repo->description;
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