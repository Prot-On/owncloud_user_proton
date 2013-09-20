<?php
if (!in_array('curl', get_loaded_extensions())) {
    \OCP\Util::writeLog("ProtOn", 'This app needs cUrl PHP extension', \OCP\Util::DEBUG);
    return false;
}

include_once 'apps/user_proton/common/includes.php';
	
OC::$CLASSPATH['OCA\Proton\User']='user_proton/lib/user.php';
OC::$CLASSPATH['OCA\Proton\UserDB']='user_proton/lib/userDB.php';
OC::$CLASSPATH['OCA\Proton\Group']='user_proton/lib/group.php';
OC::$CLASSPATH['OCA\Proton\Share'] = 'user_proton/lib/share.php';
OC::$CLASSPATH['OCA\Proton\OAuth'] = 'user_proton/lib/oauth.php';
OC::$CLASSPATH['OCA\Proton\Database'] = 'user_proton/lib/db.php';

OC_APP::registerAdmin('user_proton', 'settings');

if (\OCA\Proton\Util::isApiConfigured()) {
//    \OCA\Proton\Util::log("Loaded API dependencies");
    \OCP\Util::addscript( 'user_proton', 'login');
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
}

