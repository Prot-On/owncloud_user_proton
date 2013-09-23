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
 
class Group extends \OC_Group_Backend {
    
    private $cache;
    
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
 
        $hash_parts = 'PROTON_GROUPS_' . $method . '_' . $elegible_args_values;
        return hash( 'sha1', $hash_parts );
    }
    
    protected function storeCache($key, $value) {
        $value = base64_encode(serialize($value));
        $this->cache->set($key, $value, 60*10);
    }
    
    protected function getCache($key) {
        return unserialize(base64_decode($this->cache->get($key)));
    }
    
    public function inGroup($uid, $gid) {
        if (!Util::checkProtOnUser()) {
            return null;
        }
        Util::log('Check if '.$uid.' is inside '.$gid);
        return in_array($gid, $this->getUserGroups($uid));
    }
     
    public function getUserGroups($uid) {
        if (!Util::checkProtOnUser()) {
            return array();
        }
        $key = $this->getKey('getUserGroups', func_get_args());
        $result = $this->getCache($key);
        if (is_array($result)) {
            return $result;
        }
        
        Util::log('List groups where user is: '.$uid);
        $query = "SELECT groupname, idGroup, isDomainGroup, u2.username as creator, p.name as domain ".
                "FROM group_of_users g LEFT JOIN user u2 ON (u2.idUser = g.User_idUser) LEFT JOIN proton_domain p ON (p.idProtOnDomain = g.ProtOnDomain_idProtOnDomain), group_membership m ".
                "WHERE isCircleOfTrust = 0 AND m.User_idUser = ? AND m.Group_idGroup = idGroup ;";
        $params = array($uid);
        $query = Database::prepare($query);
        $query->execute($params);
        $result = array();
        foreach ( $query as $row) {
            $result[]  = $this->generateGroupName($row['idGroup'], $row['groupname'], $row['isDomainGroup'], $row['creator'], $row['domain']);
        }
        
        $this->storeCache($key, $result);
        return $result;
    }
    
    public function getGroups($search = '', $limit = -1, $offset = 0) {
        Util::log('Searching for groups: '.$search.' limit '.$limit.' offset '.$offset);
        $query = "SELECT groupname, idGroup, isDomainGroup, u2.username as creator, p.name as domain". 
            "FROM group_of_users g LEFT JOIN user u2 ON (u2.idUser = g.User_idUser) LEFT JOIN proton_domain p ON (p.idProtOnDomain = g.ProtOnDomain_idProtOnDomain), group_membership m , user u". 
            "WHERE LOWER(groupname) LIKE LOWER(?) AND isCircleOfTrust = 0". 
            "AND m.Group_idGroup = idGroup AND u.idUser = ?". 
            "AND ((m.User_idUser = ? AND m.role = 0) OR g.visibility = 1 OR (g.visibility = 2 AND g.ProtOnDomain_idProtOnDomain = u.ProtOnDomain_idProtOnDomain)) GROUP BY (idGroup) ;";
        $params = array('%'.$search.'%', \OC_User::getUser(), \OC_User::getUser());
        $query = Database::prepare($query, $limit, $offset);
        $query->execute($params);
        $result = array();
        foreach ( $query as $row) {
            $result[]  = $this->generateGroupName($row['idGroup'], $row['groupname'], $row['isDomainGroup'], $row['creator'], $row['domain']);
        }
        return $result;
    }

    protected function generateGroupName($idGroup, $groupName, $domainGroup, $creator, $domain) {
        $name = null;
        if ($domainGroup) {
            $name = $groupName.'<<'.$domain.'<<'.$idGroup;
        } else {
            $name = $groupName.'<<'.$creator.'<<'.$idGroup;
        }
        if (Util::isApiConfigured()) {        //This seems to be a heavy approach but any more fine grained approach seems to need more queries :(
            $query = \OC_DB::prepare( 'UPDATE `*PREFIX*share` SET share_with = ? WHERE share_type = 1 AND share_with LIKE ?');
            $query->execute( array( $name, '%<<'.$idGroup ));
        }
        return $name;
    }
    
    public static function getGroupId($gid) {
        $pieces = explode("<<", $gid);
        return $pieces[2];
    }
    
    public function groupExists($gid) {
        $query = Database::prepare("SELECT idGroup FROM group_of_users g WHERE g.idGroup = ?");
        $query->execute(array(self::getGroupId($gid)));
        return ($query->fetch() !== false);
    }
    
}
    

?>