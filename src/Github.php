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
			return json_decode($this->request(self::METHOD_GET, 'user'));
		else
			return json_decode($this->request(self::METHOD_GET, 'users/' . $user));
	}

	public function getRepo(string $owner, string $repo) {
		$get = json_decode($this->request(self::METHOD_GET, 'repos/' . $owner . '/' . $repo));
		
		return new Repository($get);
	}

	public function getRepos() {
		$repos = json_decode($this->request(self::METHOD_GET, 'user/repos'));

		$newRepos = [];

		foreach($repos as $repo) {
			$newRepos[$repo->owner->login][$repo->name] = new Repository($repo);		
		}

		return $newRepos;		
	}

	public function getRepoCodeCount(Repository $repo) {
		$freqs = json_decode($this->request(self::METHOD_GET, 'repos/' . $repo->owner . '/' . $repo->name . '/stats/code_frequency'));
		$total = 0;

		foreach($freqs as $freq) {
			$total += $freq[1] - $freq[2];
		}

		return $total;
	}

	protected function request($method = self::METHOD_GET, $url) {
		$request = $this->client->request(
			$method, 
			$this->url ?? '' . $url, 
			[
				'headers' => [
					'Authorization' => 'token ' . $this->token
				]
			]
		);

		if($request->getStatusCode() === self::STATUS_OK) {
			$body = $request->getBody();
			$content = $body->getContents();

			return $content;
		} elseif($request->getStatusCode() == self::STATUS_ACCEPTED) {
			return $this->request($method, $url);
		} else {
			throw new GithubException('Status Code: ' . $request->getStatusCode());
		}
	}

}