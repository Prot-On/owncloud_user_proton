<?php
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
                $elegible_args_values .= sizeof( $arg_value )  . '_';
            }
        }
 
        $hash_parts =  'PROTON_GROUPS_' . $method . '_' . $elegible_args_values;
        return hash( 'sha1', $hash_parts );
    }
    
    protected function storeCache($key, $value) {
        $value = base64_encode(serialize($value));
        $this->cache->set($key, $value, 60*60*3);
    } 
    
    protected function getCache($key) {
        return unserialize(base64_decode($this->cache->get($key)));
    }
    
    
    
    public function inGroup($uid, $gid) {
        $key = $this->getKey('inGroup', func_get_args());
        if ($this->cache->hasKey($key)) {
            return $this->getCache($key);
        }
        
        Util::log('Check if '.$uid.' is inside '.$gid);
        
        $return = in_array($gid, $this->getUserGroups($uid));
        
        $this->storeCache($key, $return);
        return $return;
    }
     
    public function getUserGroups($uid) {
        $key = $this->getKey('getUserGroups', func_get_args());
        if ($this->cache->hasKey($key)) {
            return $this->getCache($key);
        }

        Util::log('List groups where user is: '.$uid);
        try {
            $pest = Util::getPest(true, true);
        } catch (\Exception $e) { //Only triggered when there is not auth stored
            Util::log('Exception: '.$e->getMessage());
            return array();
        }
        $thing = $pest->get('/users/'.$uid.'/groups');
        $users = json_decode($thing, true);
        Util::log('groups '. $thing);
        $return = array();
        
        $this->storeCache($key, $return);
        return $return;
    }
    
    public function getGroups($search = '', $limit = -1, $offset = 0) {
        $key = $this->getKey('getGroups', func_get_args());
        if ($this->cache->hasKey($key)) {
            return $this->getCache($key);
        }

        Util::log('Searching for groups: '.$search.' limit '.$limit.' offset '.$offset);
        try {
            $pest = Util::getPest();
        } catch (\Exception $e) { //Only triggered when there is not auth stored
            Util::log('Exception: '.$e->getMessage());
            return array();
        }
        $thing = $pest->get('/groups/?filter='.$search);
        $groups = json_decode($thing, true);
        $return = array();
        foreach ( $groups as $group) {
            $result[]  = $group['groupname'];
        }
        return $return;
        
        $this->storeCache($key, $return);
        return $return;
    }
    
    
    public function groupExists($gid) {
        $key = $this->getKey('groupExists', func_get_args());
        if ($this->cache->hasKey($key)) {
            return $this->getCache($key);
        }

        Util::log('Check if '.$gid.' exists');
        $return = true;
        
        $this->storeCache($key, $return);
        return $return;        
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