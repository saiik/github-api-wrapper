<?php

declare(strict_types=1);

namespace saiik;

use GuzzleHttp\{
	Client,
	Exception\ClientException
};
use saiik\Request\{
	Repository as RequestRepository,
	Misc as MiscRequest,
	User as UserRequest,
	Organizations as OrganizationsRequest
};

/**
 * @package saiik\
 * @author Tobias Fuchs <saikon@hotmail.de>
 * @version 1.0
 */
class Github {

	use RequestRepository, MiscRequest, UserRequest, OrganizationsRequest;

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
	const STATUS_NO_CONTENT = 204;

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
			case self::METHOD_PATCH:
				$json = json_encode($post);
				
				try {
					$request = $this->client->patch(
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
			case self::METHOD_DELETE:
				try {
					$request = $this->client->delete(
						$this->url ?? '' . $url,
						[
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
			return $this->request($url, $post, $method);
		} elseif($statusCode === 0 && $request->getStatusCode() == self::STATUS_CREATED) {
			return true;
		} elseif($statusCode === 0 && $request->getStatusCode() == self::STATUS_NO_CONTENT) {
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