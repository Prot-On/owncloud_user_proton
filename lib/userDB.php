<?php

/**
 * ownCloud - ProtOn user plugin
 *
 * @author Ramiro Aparicio
 * @copyright 2013 Protección Online, S.L. info@prot-on.com
 *
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 *
 */
 
namespace OCA\Proton;

class UserDB extends \OC_User_Backend{
    private $cache;
    private $usersChecked = array();
    
    public function __construct() {
        $this->cache = \OC_Cache::getGlobalCache();
    }
    
    protected function getKey($method, $params) {
        $elegible_args_values = '';
 
        foreach( $params as $arg_value ){
            if( is_scalar( $arg_value ) ){
                $elegible_args_values .= $arg_value . '_';
            } else {
                $elegible_args_values .= sizeof( $arg_value ) . '_';
            }
        }
 
        $hash_parts = 'PROTON_USERS_' . $method . '_' . $elegible_args_values;
        return hash( 'sha1', $hash_parts );
    }
    
    protected function storeCache($key, $value) {
        $value = base64_encode(serialize($value));
        $this->cache->set($key, $value, 60*30); //30 min caching
    }
    
    protected function getCache($key) {
        return unserialize(base64_decode($this->cache->get($key)));
    }
    
    
	public function userExists($uid) {
	    if (isset($this->usersChecked[$uid])) {
	        return $this->usersChecked[$uid];
	    }
        
        Util::log("userExists: " . $uid);
        $query = Database::prepare("SELECT idUser FROM user WHERE idUser = ?");
        if (!$query) {
            return false;
        }
        $query->execute(array($uid));
        $result = ($query->fetch() !== false);
        
        $this->usersChecked[$uid] = $result; 
        return $result;
	}
	
	public function getDisplayName($uid) {
        $key = $this->getKey('getDisplayName', func_get_args());
        $result = $this->getCache($key);
        if (!is_null($result) && $result !== false) {
            return $result;
        }
        $query = Database::prepare("SELECT completeName FROM user WHERE idUser = ?");
        $query->execute(array($uid));
        $row = $query->fetch();
        $result = ($row !== false)?$row['completeName']: false;
              
        $this->storeCache($key, $result);
        
        return $result;    
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
