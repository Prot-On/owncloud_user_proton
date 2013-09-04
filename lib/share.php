<?php
namespace OCA\Proton;

/**
 * Class Proxy
 * @package OCA\Proton
 */
class Share {

	public static function postShared($params) {
	    $docId = self::getDocId($params['itemSource']);
        if (is_null($docId)) {
            return;
        }
        $permissions = self::parsePermissions($params['permissions']);
        $isGroup = ($params['shareType'] == \OCP\Share::SHARE_TYPE_GROUP);
        $id = $params['shareWith'];
        if ($isGroup) {
            $id = Group::getGroupId($id);            
        }
        Util::log("Share call received, permissions ".print_r($permissions,true)." with id: $id which is a group: ".($isGroup?"true":"false"));
        $permissionsObj = array(); 
        $permissionsObj[] = array('entityid' => $id, 'permission' => $permissions, 'group' => $isGroup);
        $pest = Util::getPest();
        $thing = $pest->post('/documents/'.$docId.'/rules', json_encode($permissionsObj), array("Content-Type: application/json"));
	}
    
    protected static function parsePermissions($perm) {
        $permissions = array();
        if ($perm & \OCP\PERMISSION_READ) {
            $permissions[] = 'read';
        }
        if ($perm & \OCP\PERMISSION_UPDATE) {
            $permissions[] = 'modify';
        }
        if ($perm & \OCP\PERMISSION_SHARE) {
            $permissions[] = 'manage';
        }
        return $permissions;        
    }
	
    protected static function getDocId($fileId) {
        $query = \OC_DB::prepare( 'SELECT `docId` FROM `*PREFIX*proton_docid` WHERE fileId = ?' );
        $result = $query->execute( array( $fileId ))->fetch();
        if ($result !== false) {
            return $result['docId'];
        }
        
        $path = \OC\Files\Filesystem::getPath($fileId);
        $temp = Util::toTmpFile(dirname($path) . '/' . basename($path));
        
        $pest = Util::getPest();
        $thing = $pest->post('/documents/getInfo', array("file" => "@".$temp));
        $info = json_decode($thing, true);
        $result = null;
        if (!is_null($info)) {
            $result = $info['docid'];
        } 
        $query = \OC_DB::prepare( 'INSERT INTO `*PREFIX*proton_docid` (`docId`, `fileId`) VALUES (?, ?)' );
        $query->execute(array($result, $fileId));
        return $result;
    }
    
	public static function postUnshare($params) {
        $docId = self::getDocId($params['itemSource']);
        if (is_null($docId)) {
            return;
        }
        $isGroup = ($params['shareType'] == \OCP\Share::SHARE_TYPE_GROUP);
        $id = $params['shareWith'];
        if ($isGroup) {
            $id = Group::getGroupId($id);            
        }
        Util::log('Unshare call received'.print_r($params, true));
        Util::log("Share call received, permissions with id: $id which is a group: ".($isGroup?"true":"false"));
        $permissionsObj = array(); 
        $permissionsObj[] = array('entityid' => $id, 'permission' => array(), 'group' => $isGroup);
        $pest = Util::getPest();
        $thing = $pest->post('/documents/'.$docId.'/rules', json_encode($permissionsObj), array("Content-Type: application/json"));
		
	}
	
}
?>