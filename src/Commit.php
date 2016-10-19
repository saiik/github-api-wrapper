<?php

declare(strict_types=1);

namespace saiik;

/**
 * @package saiik\
 * @author Tobias Fuchs <saikon@hotmail.de>
 * @version 1.0
 */
class Commit {

	/**
	 * @var string $sha
	 *
	 */
	protected $sha;

	/**
	 * @var \stdClass $commit
	 *
	 */
	protected $commit;

	/**
	 * @var string $rOWner
	 *
	 */
	protected $rOwner;

	/**
	 * @var string $rName
	 *
	 */
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