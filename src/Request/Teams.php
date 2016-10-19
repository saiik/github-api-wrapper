<?php

declare(strict_types=1);

namespace saiik\Request;

use saiik\Github\{
	Repository as Repo
};

/**
 * @package saiik\Request
 * @author Tobias Fuchs <saikon@hotmail.de>
 * @version 1.0
 */
trait Teams {
	/**
	 * Get a specific team
	 *
	 * @param int $team
	 * @return \stdClass
	 */
	public function getTeam(int $team) {
		$team = $this->request(sprintf('teams/%u', $team));

		return $team ?? false;
	}

	/**
	 * Create a team 
	 *
	 * @param string $org
	 * @param array $data
	 * @return boolean
	 * @throws GithubException
	 */
	public function createTeam(string $org, array $data) {
		if(!isset($data['name']))
			throw new GithubException('Please provide a team name');

		$team = $this->request(sprintf('orgs/%s/teams', $org), $data, self::METHOD_POST);

		return $team;
	}

	/**
	 * Edit a team 
	 *
	 * @param int $team
	 * @param array $data
	 * @return array
	 * @throws GithubException
	 */
	public function editTeam(int $team, array $data) {
		if(!isset($data['name']))
			throw new GithubException('Please provide a team name');

		$team = $this->request(sprintf('teams/%u', $org), $data, self::METHOD_PATCH);

		return $team ?? false;
	}

	/**
	 * Delete a team
	 *
	 * @param int $team
	 * @return boolean
	 */
	public function deleteTeam(int $team) {
		$team = $this->request(sprintf('teams/%u', $team), null, self::METHOD_DELETE);

		return $team;
	}

	/**
	 * List all team members
	 *
	 * @param int $team
	 * @return array<\stdClass>
	 */
	public function getTeamMembers(int $team) {
		$members = $this->request(sprintf('teams/%u/members', $team));

		return $members ?? false;
	}

	/**
	 * Get team repositories
	 *
	 * @param int $team
	 * @return array<\saiik\Github\Repository>
	 */
	public function getTeamRepos(int $team) {
		$repos = $this->request(sprintf('teams/%u/repos', $team));

		$newRepos = [];

		foreach($repos as $repo) {
			$newRepos[$repo->owner->login][$repo->name] = new Repo($repo);		
		}

		return $newRepos;		
	}

	/**
	 * Delete a repository
	 *
	 * @param int $team
	 * @param \saiik\Github\Repository $repo
	 * @return boolean
	 */
	public function deleteTeamRepo(int $team, Repo $repo) {
		$repo = $this->request(sprintf('teams/%u/repos/%s/%s', $team, $repo->owner, $repo->name));

		return $repo;
	}
}