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

/**
 * Class Proxy
 * @package OCA\Proton
 */
class Share {

	public static function postShared($params) {
	    $docIds = Util::getDocIds($params['itemSource']);
        if (is_null($docIds['docId'])) {
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
        $thing = $pest->post('/documents/'.$docIds['docId'].'/rules', json_encode($permissionsObj), array("Content-Type: application/json"));
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
	
	public static function postUnshare($params) {
        $docIds = Util::getDocIds($params['itemSource']);
        if (is_null($docIds['docId'])) {
            return;
        }
        $isGroup = ($params['shareType'] == \OCP\Share::SHARE_TYPE_GROUP);
        $id = $params['shareWith'];
        if ($isGroup) {
            $id = Group::getGroupId($id);            
        }
        Util::log("Unshare call received, permissions with id: $id which is a group: ".($isGroup?"true":"false"));
        $permissionsObj = array(); 
        $permissionsObj[] = array('entityid' => $id, 'permission' => array(), 'group' => $isGroup);
        $pest = Util::getPest();
        $thing = $pest->post('/documents/'.$docIds['docId'].'/rules', json_encode($permissionsObj), array("Content-Type: application/json"));
		
	}
	
}
?>