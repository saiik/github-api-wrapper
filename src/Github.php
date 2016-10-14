<?php

declare(strict_types=1);

namespace VOLL;

use GuzzleHttp\{
	Client
};

class Github {

	private $client;
	private $token;
	private $uri = 'https://api.github.com/';
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

	public function setUrl(string $url) {
		$this->url = $url;

		return true;
	}

	public function setAuth(string $token) {
		if(is_null($token))
			throw new GithubException('No auth token');

		$this->token = $token;

		return true;
	}

	public function getUser(string $user = null) {
		if(is_null($user))
			return $this->request('user');
		else
			return $this->request(sprintf('users/%s', $user));
	}

	public function getRepo(string $owner, string $repo) {
		$get = $this->request(sprintf('repos/%s/%s', $owner, $repo));
		
		return new Repository($get);
	}

	public function getRepos() {
		$repos = $this->request('user/repos');

		$newRepos = [];

		foreach($repos as $repo) {
			$newRepos[$repo->owner->login][$repo->name] = new Repository($repo);		
		}

		return $newRepos;		
	}

	public function getCommits(Repository $repo) {
		return $this->request(sprintf('repos/%s/%s/commits', $repo->owner, $repo->name));
	}

	public function getReadMe(Repository $repo) {
		$readme = $this->request(sprintf('repos/%s/%s/readme', $repo->owner, $repo->name));
		$content = base64_decode($readme->content);

		return $content ?? false;
	}

	public function getRepoCodeCount(Repository $repo) {
		$freqs = $this->request(sprintf('repos/%s/%s/stats/code_frequency', $repo->owner, $repo->name));
		$total = 0;

		foreach($freqs as $freq) {
			$total += $freq[1] - $freq[2];
		}

		return $total;
	}

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