<?php
namespace OCA\Proton;
 
class Group extends \OC_Group_Backend {
    
    public function inGroup($uid, $gid) {
        Util::log('Check if '.$uid.' is inside '.$gid);
        return false;
    }
     
    public function getUserGroups($uid) {
        Util::log('List groups where user is: '.$uid);
        return array();
    }
    
    public function getGroups($search = '', $limit = -1, $offset = 0) {
        Util::log('Searching for groups: '.$search.' limit '.$limit.' offset '.$offset);
        try {
            $pest = Util::getPest();
        } catch (\Exception $e) { //Only triggered when there is not auth stored
            Util::log('Exception: '.$e->getMessage());
            return array();
        }
        $thing = $pest->get('/groups/?filter='.$search);
        $groups = json_decode($thing, true);
        $result = array();
        foreach ( $groups as $group) {
            $result[]  = $group['groupname'];
        }
        return $result;
        
    }
    
    
    public function groupExists($gid) {
        Util::log('Check if '.$gid.' exists');
        return true;
    }
    
    public function usersInGroup($gid, $search = '', $limit = -1, $offset = 0){
        Util::log('Find users in group '.$gid.' pattern:' . $search.' limit '.$limit.' offset '.$offset);
        return array();
    }

}
    

?>