<?php

namespace OCA\Proton;

class User extends \OC_User_Backend{
	private static $userId; //Hooks uses static functions so this should be static
    private $userNameCache;
    
    public function __construct() {
        $this->userNameCache = \OC_Cache::getGlobalCache();        
    }     
    
    protected function getKey($uid) {
        return hash( 'sha1', 'PROTON_USER_NAMES_'.$uid );
    }
    
    protected function storeDisplayName($uid, $displayName) {
        $this->userNameCache->set($this->getKey($uid), $displayName, 60*60*24);
    } 
    
    protected function _getDisplayName($uid) {
        return $this->userNameCache->get($this->getKey($uid));
    }
    
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
		Util::storeCompleteName($info['completename']);
        $this->storeDisplayName($info['id'], $info['completename']);
        self::$userId = $info['id'];
		Util::storePassword($password);
		return $uid;
	}

    public static function postLogin($uid, $password = '') {
        if (isset(self::$userId)) {
            \OC_User::setUserid(self::$userId);                   
        }
    }

	public function userExists($uid) {
		return true;
	}
	
	public function getDisplayName($uid) {
		Util::log("getDisplayName: " . $uid);
		if (\OC_User::getUser() === $uid && Util::getCompleteName() != null) {
			return Util::getCompleteName();
		} else {
			$display = $this->_getDisplayName($uid);
            return $display;
		}
	}

	public function getDisplayNames($search = '', $limit = null, $offset = null) {
		Util::log('Searching for users: '.$search.' limit '.$limit.' offset '.$offset);
		try {
			$pest = Util::getPest();
		} catch (\Exception $e) { //Only triggered when there is not auth stored
			Util::log('Exception: '.$e->getMessage());
			return array();
		}
		$thing = $pest->get('/users/?filter='.$search);
		$users = json_decode($thing, true);
		$result = array();
		foreach ( $users as $user) {
		    $this->storeDisplayName($user['id'], $user['completename']);
			$result[$user['id']]  = $user['completename'];
		}
		return $result;
	}
}
