<?php

namespace OCA\Proton;

class User extends \OC_User_Backend{
	private static $userId; //Hooks uses static functions so this should be static
    
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
        $hostingConfig = \OC_Config::getValue( "user_proton_hosting");
        if (!empty($hostingConfig) && $hostingConfig !== $info['hostingname']) {
            Util::log('The user '. $uid .' can not use OwnCloud due to Hosting retrictions');
            return false;
        }
        self::$userId = $info['id'];
		Util::storePassword($password);
		return $uid;
	}

    public static function postLogin($uid, $password = '') {
        if (isset(self::$userId)) {
            Util::log('Post login');
            \OC_User::setUserid(self::$userId);
        }
    }
    
    public function getDisplayName($uid) {
        return false;
    }
    
}
