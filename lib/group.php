<?php
namespace OCA\Proton;
 
class Group extends \OC_Group_Backend {
    
    public function inGroup($uid, $gid) {
        Util::log('Check if '.$uid.' is inside '.$gid);
        return in_array($gid, $this->getUserGroups($uid));
    }
     
    public function getUserGroups($uid) {
        Util::log('List groups where user is: '.$uid);
        $query = "SELECT groupname, idGroup, isDomainGroup, u2.username as creator, p.name as domain ".
                "FROM group_of_users g LEFT JOIN user u2 ON (u2.idUser = g.User_idUser) LEFT JOIN proton_domain p ON (p.idProtOnDomain = g.ProtOnDomain_idProtOnDomain), group_membership m ".
                "WHERE isCircleOfTrust = 0 AND m.User_idUser = ? AND m.Group_idGroup = idGroup ;";
        $params = array($uid);
        $query = Database::prepare($query);
        if (!$query) {
            return array();
        }
        $query->execute($params);
        $result = array();
        foreach ( $query as $row) {
            $result[]  = $this->generateGroupName($row['idGroup'], $row['groupname'], $row['isDomainGroup'], $row['creator'], $row['domain']);
        }
        return $result;
    }
    
    public function getGroups($search = '', $limit = -1, $offset = 0) {
        Util::log('Searching for groups: '.$search.' limit '.$limit.' offset '.$offset);
        $query = "SELECT groupname, idGroup, isDomainGroup, u2.username as creator, p.name as domain ".
            "FROM group_of_users g LEFT JOIN user u2 ON (u2.idUser = g.User_idUser) LEFT JOIN proton_domain p ON (p.idProtOnDomain = g.ProtOnDomain_idProtOnDomain), group_membership m, user u ". 
            "WHERE LOWER(groupname) LIKE LOWER(?) AND isCircleOfTrust = 0 " .
            "AND m.Group_idGroup = idGroup AND m.User_idUser = ? AND u.idUser = ? ". 
            "AND (m.role = 0 OR g.visibility = 1 OR (g.visibility = 2 AND g.ProtOnDomain_idProtOnDomain = u.ProtOnDomain_idProtOnDomain)) ;";
        $params = array('%'.$search.'%', \OC_User::getUser(), \OC_User::getUser());
        $query = Database::prepare($query, $limit, $offset);
        if (!$query) {
            return array();
        }
        $query->execute($params);
        $result = array();
        foreach ( $query as $row) {
            $result[]  = $this->generateGroupName($row['idGroup'], $row['groupname'], $row['isDomainGroup'], $row['creator'], $row['domain']);
        }
        return $result;
    }

    protected function generateGroupName($idGroup, $groupName, $domainGroup, $creator, $domain) {
        if ($domainGroup) {
            return $groupName.'<<'.$domain.'<<'.$idGroup;
        } else {
            return $groupName.'<<'.$creator.'<<'.$idGroup;
        }
    }
    
    protected function getGroupId($gid) {
        $pieces = explode("<<", $gid);
        return $pieces[2];
    }
    
    public function groupExists($gid) {
        return true;
    }
    
/*    
    public function usersInGroup($gid, $search = '', $limit = -1, $offset = 0){
        $key = $this->getKey('usersInGroup', func_get_args());
        if ($this->cache->hasKey($key)) {
            return $this->getCache($key);
        }

        Util::log('Find users in group '.$gid.' pattern:' . $search.' limit '.$limit.' offset '.$offset);
        $return = array();
        
        $this->storeCache($key, $return);
        return $return;        
    }
*/

}
    

?>