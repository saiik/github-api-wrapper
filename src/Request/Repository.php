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
trait Repository {
	/**
	 * Create a repository
	 *
	 * @param array $data
	 * @return boolean
	 * @throws \saiik\GithubException
	 */
	public function createRepository(array $data) {
		if(!isset($data['name'])) 
			throw new GithubException('Please provide a repository name');

		$post = $this->request('user/repos', $data, self::METHOD_POST);

		return $post;
	}

	/**
	 * Get a specific repository
	 *
	 * @param string $owner
	 * @param string $repo
	 * @return \saiik\Github\Repository
	 */
	public function getRepo(string $owner, string $repo) {
		$get = $this->request(sprintf('repos/%s/%s', $owner, $repo));
		
		return new Repo($get);
	}

	/**
	 * Get all repositories for user
	 *
	 * @return array<\saiik\Github\Repository>
	 */
	public function getRepos() {
		$repos = $this->request('user/repos');

		$newRepos = [];

		foreach($repos as $repo) {
			$newRepos[$repo->owner->login][$repo->name] = new Repo($repo);		
		}

		return $newRepos;		
	}

	/**
	 * Get last commits
	 *
	 * @param \saiik\Github\Repository $repo
	 * @return array<\saiik\Github\Commit>
	 */
	public function getCommits(Repo $repo) {
		$commits = $this->request(sprintf('repos/%s/%s/commits', $repo->owner, $repo->name));

		$commitsOut = [];
		foreach($commits as $commit) {
			$commitsOut[] = new Commit($commit, $repo);
		}

		return $commitsOut;
	}

	/**
	 * get a specific commit
	 *
	 * @param \saiik\Commit $commit
	 * @return \stdClass
	 */
	public function getCommit(Commit $commit) {
		$commit = $this->request(sprintf('repos/%s/%s/git/commits/%s', $commit->rOwner, $commit->rName, $commit->sha));

		return $commit;
	}

	/**
	 * Get README.md 
	 *
	 * @param \saiik\Github\Repository
	 * @return string
	 */
	public function getReadMe(Repo $repo) {
		$readme = $this->request(sprintf('repos/%s/%s/readme', $repo->owner, $repo->name));
		$content = base64_decode($readme->content);

		return $content ?? false;
	}

	/**
	 * Get amount of code lines for a repo
	 *
	 * @param \saiik\Github\Repository $repo
	 * @return int
	 */
	public function getRepoCodeCount(Repo $repo) {
		$freqs = $this->request(sprintf('repos/%s/%s/stats/code_frequency', $repo->owner, $repo->name));
		$total = 0;

		foreach($freqs as $freq) {
			$total += $freq[1] - $freq[2];
		}

		return $total;
	}

	/**
	 * Returns all programming languages used in a repository
	 *
	 * @param \saiik\Github\Repository $repo
	 * @return array | boolean
	 */
	public function getRepoLanguages(Repo $repo) {
		$lang = $this->request(sprintf('repos/%s/%s/languages', $repo->owner, $repo->name));

		if($lang instanceof \stdClass) {
			$return = [];
			$i = 0;
			foreach($lang as $language => $bytes) {
				$return[$i]['lang'] = $language;
				$return[$i]['bytes'] = $bytes;
				$i++;
			}

			return $return;
		} 

		return false;
	}

	/**
	 * List all contributors for a repository
	 *
	 * @param \saiik\Github\Repository $repo
	 * @return array | boolean
	 */
	public function getRepoContributors(Repo $repo) {
		$cont = $this->request(sprintf('repos/%s/%s/contributors', $repo->owner, $repo->name));

		if(is_array($cont) && count($cont) > 0) {
			$users = [];
			$i = 0;
			foreach($cont as $contributor) {
				$users[$i]['name'] = $contributor->login;
				$users[$i]['url'] = $contributor->html_url;
				$users[$i]['contributions'] = $contributor->contributions; 

				$i++;
			}

			return $users;
		}

		return false;
	}

	/** 
	 * List all teams for the repository
	 *
	 * @param \saiik\Github\Repository $repo
	 * @return array
	 */
	public function getRepoTeams(Repo $repo) {
		$teams = $this->request(sprintf('repos/%s/%s/teams', $repo->owner, $repo->name));

		return $teams;
	}	
}