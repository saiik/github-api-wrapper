<?php

declare(strict_types=1);

namespace saiik\Request;

/**
 * @package saiik\Request
 * @author Tobias Fuchs <saikon@hotmail.de>
 * @version 1.0
 */
trait Organizations {
	/**
	 * Get my organizations
	 *
	 * @return array<\stdClass>
	 */
	public function getMyOrganizations() {
		$orgs = $this->request('user/orgs');

		return $orgs ?? false;
	}

	/**
	 * List all user specific organizations
	 *
	 * @param string $user
	 * @return array<\stdClass>
	 */
	public function getUserOrganizations(string $user) {
		$orgs = $this->request(sprintf('users/%s/orgs', $user));

		return $orgs ?? false;
	}

	/**
     * Get a specific organization
	 *
	 * @return \stdClass
	 */
	public function getOrganization(string $org) {
		$org = $this->request(sprintf('orgs/%s', $org));

		return $org ?? false;
	}

	/**
	 * Edit an organization
	 *
	 * @param string $org
	 * @param array $data
	 * @return boolean
	 */
	public function editOrganization(string $org, array $data) {
		$patch = $this->request(sprintf('orgs/%s', $org), $data, self::METHOD_PATCH);

		return $patch;
	}

	/**
	 * List all members of organization (public)
	 *
	 * @param string $org
	 * @return array<\stdClass>
	 */
	public function getOrganizationMembers(string $org) {
		$members = $this->request(sprintf('orgs/%s/members', $org));

		return $members ?? false;
	}

	/**
	 * Delete a member from organization
	 *
	 * @param string $org
	 * @param string $user
	 * @return boolean
	 */
	public function deleteMember(string $org, string $user) {
		$delete = $this->request(sprintf('orgs/%s/members/%s', $org, $user), null, self::METHOD_DELETE);

		return $delete;
	}

	/**
	 * List teams for this organization
	 *
	 * @param string $org
	 * @return array<\stdClass>
	 */
	public function getOrganizationTeams(string $org) {
		$teams = $this->request(sprintf('orgs/%s/teams', $org));

		return $teams ?? false;
	}
}