<?php

declare(strict_types=1);

namespace saiik\Request;

/**
 * @package saiik\Request
 * @author Tobias Fuchs <saikon@hotmail.de>
 * @version 1.0
 */
trait User {
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
	 * Update your user profile
	 *
	 * @param array $data
	 * @return array
	 */
	public function updateMe(array $data) {
		if(!isset($data['name']))
			throw new GithubException('Please provide a username');

		$user = $this->request('user', $data, self::METHOD_PATCH);

		return $user;
	}

	/**
	 * List all your emails
	 *
	 * @return array<\stdClass>
	 */
	public function listMyEmails() {
		$emails = $this->request('user/emails');

		return $emails ?? false;
	}

	/**
	 * Add an email address
	 *
	 * @param aray $data
	 * @return boolean
	 */
	public function addEmailAddress(array $data) {
		$post = $this->request('user/emails', $data, self::METHOD_POST);

		return $post;
	}

	/**
	 * Delete an email address
	 *
	 * @param aray $data
	 * @return boolean
	 */
	public function deleteEmailAddress(array $data) {
		$post = $this->request('user/emails', $data, self::METHOD_DELETE);

		return $post;
	}

	/**
	 * List my followers
 	 *
	 * @return array<\stdClass>
	 */
	public function listMyFollowers() {
		$followers = $this->request('user/followers');

		return $followers ?? false;
	}

	/**
	 * List users which i follow
	 *
	 * @return array<\stdClass>
	 */
	public function listMeFollowing() {
		$following = $this->request('user/following');

		return $following ?? false;
	}

	/**
	 * List my followers
 	 *
	 * @return array<\stdClass>
	 */
	public function listUserFollowers(string $user) {
		$followers = $this->request(sprintf('users/%s/followers', $user));

		return $followers ?? false;
	}

	/**
	 * List users which i follow
	 *
	 * @return array<\stdClass>
	 */
	public function listUserFollowing(string $user) {
		$following = $this->request(sprintf('users/%s/following', $user));

		return $following ?? false;
	}

	/**
	 * List all my public ssh keys
	 *
	 * @return array<\stdClass>
	 */
	public function listMyPublicSSHKeys() {
		$ssh = $this->request('user/keys');

		return $ssh ?? false;
	}

	/**
	 * List all user public ssh keys
	 *
	 * @param string $user
	 * @return array<\stdClass>
	 */
	public function listUserPublicSSHKeys(string $user) {
		$ssh = $this->request(sprintf('users/%s/keys', $user));

		return $ssh ?? false;
	}

	/**
	 * Get one public ssh key
	 *
	 * @param int $id
	 * @return \stdClass
	 */
	public function getOnePublicSSHKey(int $id) {
		$ssh = $this->request(sprintf('user/keys/%u', $id));

		return $ssh ?? false;
	}

	/**
	 * Create one public ssh key
	 *
	 * @param string $title
	 * @param string $key
	 * @return boolean
	 */
	public function createSSHKey(string $title, string $key) {
		$post = $this->request('user/keys', ['title' => $title, 'key' => $key], self::METHOD_POST);

		return $post;
	}

	/**
	 * Delete one ssh key
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function deleteSSHKey(int $id) {
		$delete = $this->request(sprintf('user/keys/%u', $id), null, self::METHOD_DELETE);

		return $delete;
	}

	/**
	 * List all your gpg keys
	 *
	 * @return array<\stdClass>
	 */
	public function listMyGPGKeys() {
		$gpg = $this->request('user/gpg_keys');

		return $gpg ?? false;
	}

	/**
	 * Get one GPG Key
	 *
	 * @param int $id
	 * @return \stdCLass
	 */
	public function getOneGPGKey(int $id) {
		$gpg = $this->request(sprintf('user/gpg_keys/%u', $id));

		return $gpg ?? false;
	}

	/**
	 * Create one gpg key
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function createOneGPGKey(string $key) {
		$post = $this->request('user/gpg_keys', ['armored_public_key' => $key], self::METHOD_POST);

		return $post;
	}

	/**
	 * Delete one gpg key
	 *
	 * @param int $id
	 * @return boolean
	 */
	public function deleteOneGPGKey(int $id) {
		$delete = $this->request(sprintf('user/gpg_keys/%u', $id), null, self::METHOD_DELETE);

		return $delete;
	}
}