<?php

/**
 * ownCloud - ProtOn user plugin
 *
 * @author Ramiro Aparicio
 * @author Santiago Cuenca Lizcano
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
 

if (!in_array('curl', get_loaded_extensions())) {
    \OCP\Util::writeLog("ProtOn", 'This app needs cUrl PHP extension', \OCP\Util::DEBUG);
    return false;
}

if (!function_exists('proton_include')) {
    include_once 'apps/user_proton/common/includes.php';
    proton_include('user_proton');
}
	
OC::$CLASSPATH['OCA\Proton\User']='user_proton/lib/user.php';
OC::$CLASSPATH['OCA\Proton\UserDB']='user_proton/lib/userDB.php';
OC::$CLASSPATH['OCA\Proton\Group']='user_proton/lib/group.php';
OC::$CLASSPATH['OCA\Proton\Share'] = 'user_proton/lib/share.php';
OC::$CLASSPATH['OCA\Proton\OAuth'] = 'user_proton/lib/oauth.php';
OC::$CLASSPATH['OCA\Proton\Database'] = 'user_proton/lib/db.php';

OC_APP::registerAdmin('user_proton', 'settings');

if (\OCA\Proton\Util::isApiConfigured()) {
//    \OCA\Proton\Util::log("Loaded API dependencies");
    OC_User::useBackend( new \OCA\Proton\User() );
    \OCP\Util::connectHook('OC_User', 'post_login', 'OCA\Proton\User', 'postLogin');
    \OCP\Util::connectHook('OC_User', 'logout', 'OCA\Proton\User', 'logout');
}

if (\OCA\Proton\Database::isDBConfigured()) {
//    \OCA\Proton\Util::log("Loaded DB dependencies");
    OC_Group::useBackend( new \OCA\Proton\Group() );    
    OC_User::useBackend( new \OCA\Proton\UserDB() );
}

if (\OCA\Proton\Database::isDBConfigured() 
    && \OCA\Proton\Util::isApiConfigured() ) {
//    \OCA\Proton\Util::log("Loaded DB + API dependencies");
        
    \OCP\Util::connectHook('OCP\Share', 'post_shared', 'OCA\Proton\Share', 'postShared');
    \OCP\Util::connectHook('OCP\Share', 'post_unshare', 'OCA\Proton\Share', 'postUnshare');
    \OCP\Util::connectHook('OCP\Share', 'post_update_permissions', 'OCA\Proton\Share', 'postShared');
}

if (\OCA\Proton\Util::isOAuthConfigured()) {
//    \OCA\Proton\Util::log("Loaded OAuth dependencies");
    OC_App::registerLogIn(array('href' => \OCP\Util::linkToRoute( 'proton_oauth'), 'name' => 'Prot-On OAuth'));
    OCP\Util::addStyle('user_proton', 'proton');
}

