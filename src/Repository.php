<?php

declare(strict_types=1);

namespace VOLL;

class Repository {

	protected $repo;

	protected $id;
	protected $name;
	protected $fullName;
	protected $private;
	protected $description;

	public function __construct(\stdClass $repo) {
		$this->repo = $repo;

		$this->parse();
	}

	private function parse() {
		$this->id = $this->repo->id;
		$this->owner = $this->repo->owner->login;
		$this->name = $this->repo->name;
		$this->fullName = $this->repo->full_name;
		$this->private = $this->repo->private;
		$this->description = $this->repo->description;
		/** .. **/
	}

	public function isPrivate() {

		return $this->private;
	}

	public function __get($name) {
		if(isset($this->$name)) 
			return $this->$name;

		return false;
	}


}