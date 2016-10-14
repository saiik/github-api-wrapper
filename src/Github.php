<?php

declare(strict_types=1);

namespace VOLL;

use GuzzleHttp\{
	Client
};

class Github {

	/**
	 * @var GuzzleHttp\Client $client
	 *
	 */
	private $client;

	/**
	 * @var string $token
	 *
	 */
	private $token;

	/**
	 * @var string $uri
	 *
	 */	
	private $uri = 'https://api.github.com/';

	/**
	 * @var string $url
	 *
	 */	
	private $url;

	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	const METHOD_PATCH = 'PATCH';
	const METHOD_DELETE = 'DELETE';

	const STATUS_OK = 200;
	const STATUS_ACCEPTED = 202;
	const STATUS_AUTH = 401;
	const STATUS_NOTFOUND = 404;

	public function __construct(string $token) {
		if(is_null($token))
			throw new GithubException('No auth token');

		$this->token = $token;

		$this->client = new Client(
			[
				'base_uri' => $this->uri,
			]
		);
	}

	/**
	 * Set API url
	 *
	 * @param string $url
	 * @return boolean
	 */
	public function setUrl(string $url) {
		$this->url = $url;

		return true;
	}

	/**
	 * Set auth token
	 *
	 * @param string $token
	 * @return boolean
 	 * @throws GithubException
	 */
	public function setAuth(string $token) {
		if(is_null($token))
			throw new GithubException('No auth token');

		$this->token = $token;

		return true;
	}

	/**
	 * Get github user
	 *
	 * @param string $user
	 * @return array
	 */
	public function getUser(string $user = null) {
		if(is_null($user))
			return $this->request('user');
		else
			return $this->request(sprintf('users/%s', $user));
	}

	/**
	 * Get a specific repository
	 *
	 * @param string $owner
	 * @param string $repo
	 * @return VOLL\Repository
	 */
	public function getRepo(string $owner, string $repo) {
		$get = $this->request(sprintf('repos/%s/%s', $owner, $repo));
		
		return new Repository($get);
	}

	/**
	 * Get all repositories for user
	 *
	 * @return array<VOLL\Repository>
	 */
	public function getRepos() {
		$repos = $this->request('user/repos');

		$newRepos = [];

		foreach($repos as $repo) {
			$newRepos[$repo->owner->login][$repo->name] = new Repository($repo);		
		}

		return $newRepos;		
	}

	/**
	 * Get last commits
	 *
	 * @param VOLL\Repository $repo
	 * @return array
	 */
	public function getCommits(Repository $repo) {
		return $this->request(sprintf('repos/%s/%s/commits', $repo->owner, $repo->name));
	}

	/**
	 * Get README.md 
	 *
	 * @param VOLL\Repository
	 * @return string
	 */
	public function getReadMe(Repository $repo) {
		$readme = $this->request(sprintf('repos/%s/%s/readme', $repo->owner, $repo->name));
		$content = base64_decode($readme->content);

		return $content ?? false;
	}

	/**
	 * Get amount of code lines for a repo
	 *
	 * @param VOLL\Repository $repo
	 * @return int
	 */
	public function getRepoCodeCount(Repository $repo) {
		$freqs = $this->request(sprintf('repos/%s/%s/stats/code_frequency', $repo->owner, $repo->name));
		$total = 0;

		foreach($freqs as $freq) {
			$total += $freq[1] - $freq[2];
		}

		return $total;
	}

	/**
	 * Send a request to api
	 *
	 * @param string $url
	 * @param string $method
	 * @return array
	 * @throws GithubException
	 */
	protected function request($url, $method = self::METHOD_GET) {
		$request = $this->client->request(
			$method, 
			$this->url ?? '' . $url, 
			[
				'headers' => [
					'Authorization' => sprintf('token %s', $this->token)
				]
			]
		);

		if($request->getStatusCode() === self::STATUS_OK) {
			$body = $request->getBody();
			$content = $body->getContents();

			return json_decode($content);
		} elseif($request->getStatusCode() == self::STATUS_ACCEPTED) {
			return $this->request($method, $url);
		} else {
			throw new GithubException('Status Code: ' . $request->getStatusCode());
		}
	}

}