<?php

namespace OCA\Proton;

class UserDB extends \OC_User_Backend{
	public function userExists($uid) {
        Util::log("userExists: " . $uid);
        $query = Database::prepare("SELECT idUser FROM user WHERE idUser = ?");
        if (!$query) {
            return false;
        }
        $query->execute(array($uid));
        return ($query->fetch() !== false);
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
