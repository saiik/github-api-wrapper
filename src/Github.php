<?php

declare(strict_types=1);

namespace saiik;

use GuzzleHttp\{
	Client,
	Exception\ClientException
};

/**
 * @package saiik\
 * @author Tobias Fuchs <saikon@hotmail.de>
 * @version 1.0
 */
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
	 * Parse a README.md file
	 *
	 * @param string $readMe
	 * @return string
	 */
	public function parseReadMe(string $readMe) {
		$post = $this->request('markdown', ['text' => $readMe], self::METHOD_POST);
		
		return $post;
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
	protected function request($url, $post = null, $method = self::METHOD_GET) {
		$statusCode = 0;

		switch($method) {
			case self::METHOD_GET:
				try {
					$request = $this->client->request(
						$method, 
						$this->url ?? '' . $url, 
						[
							'headers' => [
								'Authorization' => sprintf('token %s', $this->token)
							]
						]
					);
				} catch(ClientException $e) {
					$statusCode = $e->getResponse()->getStatusCode();
				}
			break;
			case self::METHOD_POST:
				$json = json_encode($post);
				
				try {
					$request = $this->client->post(
						$this->url ?? '' . $url,
						[
							'body' => $json,
							'headers' => [
								'Authorization' => sprintf('token %s', $this->token),
							]
						]
					);
				} catch(ClientException $e) {
					$statusCode = $e->getResponse()->getStatusCode();
				}

			break;
			default:
				return;
			break;
		}

		if($statusCode === 0 && $request->getStatusCode() === self::STATUS_OK) {
			$body = $request->getBody();
			$content = $body->getContents();

			$header = $request->getHeaders();
			$type = explode(";", $header['Content-Type'][0]);
			$type = $type[0];

			switch($type) {
				case 'application/json':
					return json_decode($content);
				break;
				case 'text/html':
					return $content;
				break;
				default:
					return;
				break;
			}
		} elseif($statusCode === 0 && $request->getStatusCode() == self::STATUS_ACCEPTED) {
			return $this->request($method, $url);
		} else {
			$statusCode = $statusCode = 0 ? $request->getStatusCode() : $statusCode;
			switch((int)$statusCode) {
				case self::STATUS_NOTFOUND:
					throw new GithubException(
						'Requested page not found '.PHP_EOL.' URL: ' . $url
					);
				break;
				case self::STATUS_AUTH:
					throw new GithubException('Invalid access token, please generate your access token here: https://github.com/settings/tokens');
				break;
				default:
					throw new GithubException('Status Code: ' . $statusCode);
				break;
			}
		}
	}

}