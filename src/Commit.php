<?php

namespace saiik;

class Commit {

	protected $sha;
	protected $commit;
	protected $rOwner;
	protected $rName;

	public function __construct(\stdClass $commit, Repository $repo) {

		$this->sha = $commit->sha;
		$this->commit = $commit->commit;
		$this->rOwner = $repo->owner;
		$this->rName = $repo->name;
	}

	public function __get($name) {
		if(isset($this->$name)) 
			return $this->$name;

		return false;
	}

}