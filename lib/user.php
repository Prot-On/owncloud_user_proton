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
		} catch (\Exception $e) {
		    Util::log('Excepcion '.$e);
            return null;		    
		}
		$info = json_decode($thing, true);
        $hostingConfig = \OC_Config::getValue( "user_proton_hosting");
        if (!empty($hostingConfig) && $hostingConfig !== $info['hostingname']) {
            Util::log('The user '. $uid .' can not use OwnCloud due to Hosting retrictions');
            return false;
        }
        self::$userId = $info['id'];
		Util::storePassword($password);
        Util::storeUser($uid);
        Util::storeCompleteName($info['completename']);
		return $uid;
	}

    public static function postLogin($uid, $password = '') {
        if (isset(self::$userId)) {
            Util::log('Post login');
            Util::markProtOnUser();
            \OC_User::setUserid(self::$userId);
        }
    }
    
    public static function logout() {
        session_unset();
        session_destroy();
        \OC_User::unsetMagicInCookie();
        header("Location: " .  \OCP\Util::linkToRoute( 'proton_logout'));
        die();
    }
    
    public static function logoutController(){
        \OCP\Util::addScript('user_proton', 'logout');
        $tmpl = new \OC_Template( 'user_proton', 'logout', 'base');
        $tmpl->assign( 'root', \OC::$WEBROOT);
        $tmpl->assign( 'logout_url', \OC_Config::getValue( "user_proton_url" )); 
        return $tmpl->printPage();
    }
    
    
    public function getDisplayName($uid) {
        if ($uid == \OC_User::getUser() && Util::checkProtOnUser()) {
            return Util::getCompleteName();
        }
        return false;
    }
    
    public function userExists($uid) {
        if ($uid == \OC_User::getUser() && Util::checkProtOnUser()) {
            return true;
        }
        return null;
    }
}
