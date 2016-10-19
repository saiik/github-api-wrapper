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
	const STATUS_ENTITY = 422;
	const STATUS_CREATED = 201;

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
	 * @return \saiik\Repository
	 */
	public function getRepo(string $owner, string $repo) {
		$get = $this->request(sprintf('repos/%s/%s', $owner, $repo));
		
		return new Repository($get);
	}

	/**
	 * Get all repositories for user
	 *
	 * @return array<\saiik\Repository>
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
	 * @param \saiik\Repository $repo
	 * @return array
	 */
	public function getCommits(Repository $repo) {
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
	 * @param \saiik\Repository
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
	 * @param \saiik\Repository $repo
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
	 * Returns all programming languages used in a repository
	 *
	 * @param \saiik\Repoistory $repo
	 * @return array | boolean
	 */
	public function getRepoLanguages(Repository $repo) {
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
	 * @param \saiik\Repository $repo
	 * @return array | boolean
	 */
	public function getRepoContributors(Repository $repo) {
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
	 * @param \saiik\Repository $repo
	 * @return array
	 */
	public function getRepoTeams(Repository $repo) {
		$teams = $this->request(sprintf('repos/%s/%s/teams', $repo->owner, $repo->name));

		return $teams;
	}

	/**
	 * Get your current rate limit
	 *
	 * @return array
	 */
	public function getRateLimit() {
		$limit = $this->request('rate_limit');

		return $limit ?? false;
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
		} elseif($statusCode === 0 && $request->getStatusCode() == self::STATUS_CREATED) {
			return true;
		} else {
			$statusCode = $statusCode = 0 ? $request->getStatusCode() : $statusCode;
			switch((int)$statusCode) {
				case self::STATUS_NOTFOUND:
					throw new GithubException(
						'Requested page not found '.PHP_EOL.' URL: ' . $url
					);
				break;
				case self::STATUS_AUTH:
					throw new GithubException(
						'Invalid access token, please generate your access token here: https://github.com/settings/tokens'
					);
				break;
				case self::STATUS_ENTITY:
					throw new GithubException(
						'Unprocessable Entity'
					);
				break;
				default:
					throw new GithubException(
						'Status Code: ' . $statusCode
					);
				break;
			}
		}
	}

}