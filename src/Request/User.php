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
}