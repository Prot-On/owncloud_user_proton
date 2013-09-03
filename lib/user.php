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
            \OC_User::setUserid(self::$userId);                   
        }
    }

	public function userExists($uid) {
		return true;
	}
	
	public function getDisplayName($uid) {
		Util::log("getDisplayName: " . $uid);
        $query = Database::prepare("SELECT completeName FROM user WHERE idUser = ?");
        if (!$query) {
            return null;
        }
        $query->execute(array($uid));
        $row = $query->fetch();
        return $row['completeName'];    
	}

	public function getDisplayNames($search = '', $limit = null, $offset = null) {
		Util::log('Searching for users: '.$search.' limit '.$limit.' offset '.$offset);
        $hostingConfig = \OC_Config::getValue( "user_proton_hosting");
        $query = "SELECT completeName, idUser FROM user WHERE LOWER(username) LIKE LOWER(?) OR LOWER(completeName) LIKE LOWER(?)";
        $params = array('%'.$search.'%', '%'.$search.'%');
        if (!is_null($hostingConfig)) {
            $query = "SELECT completeName, idUser FROM user u, proton_domain p WHERE ".
            " ( LOWER(username) LIKE LOWER(?) OR LOWER(completeName) LIKE LOWER(?) )".
            " AND p.name = ? AND u.ProtOnDomain_idProtOnDomain = p.idProtOnDomain;";
            $params[] =  $hostingConfig;
        }
        $query = Database::prepare($query, $limit, $offset);
        if (!$query) {
            return null;
        }
        $query->execute($params);
        $result = array();
		foreach ( $query as $row) {
			$result[$row['idUser']]  = $row['completeName'];
		}
		return $result;
	}
}
