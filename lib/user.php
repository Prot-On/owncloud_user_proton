<?php
/**
 * Copyright (c) 2012 Robin Appelman <icewind@owncloud.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */
use \OCA\Proton\Util;
 
class OC_USER_PROTON extends OC_User_Backend{
	private $host;
	private $secure;
	private $protocol;

	/**
	 * @brief Check if the password is correct
	 * @param $uid The username
	 * @param $password The password
	 * @returns true/false
	 *
	 * Check if the password is correct without logging in the user
	 */
	public function checkPassword($uid, $password) {
		$pest = Util::getPest(false);
		$pest->setupAuth($uid, $password);
		try {
		$thing = $pest->get('/users/userInfo');
		} catch (Pest_Unauthorized $e) {
			return false;
		} catch (Pest_Forbidden $e) {
			return false;
		}
		$info = json_decode($thing, true);
        if (false) { //TODO Change this to check hosting
            return false;
        }
		Util::storeCompleteName($info['completename']);
		Util::storePassword($password);
        \OC_User::setUserid($info['username']);
		return $uid;
	}

	public function userExists($uid) {
		return true;
	}
	
	public function getDisplayName($uid) {
		Util::log("getDisplayName: " . $uid);
		if ($_SESSION['user_id'] === $uid && Util::getCompleteName() != null) {
			return Util::getCompleteName();
		} else {
			return $uid;
		}
	}
	
	public function getUsers($search = '', $limit = null, $offset = null) {	
		Util::log('Searching for users: '.$search);
		try {
			$pest = Util::getPest();
		} catch (\Exception $e) { //Only triggered when there is not auth stored
			Util::log('Exception: '.$e->getMessage());
			return array();
		}
		$thing = $pest->get('/users/search/?pattern='.$search);
		$users = json_decode($thing, true);
		$result = array();
		foreach ( $users as $user) {
			$result[]  = $user['username'];
		}
		return $result;
	}
}
