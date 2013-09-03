<?php

if (!OCP\App::isEnabled('files_proton')) {
	$dir = dirname(dirname(__FILE__)).'/common/3rdparty';
	set_include_path(get_include_path() . PATH_SEPARATOR . $dir);
	
	OC::$CLASSPATH['Pest']='user_proton/common/3rdparty/Pest/Pest.php';
	OC::$CLASSPATH['OCA\Proton\Util'] = 'user_proton/common/lib/util.php';
	OC::$CLASSPATH['OCA\Proton\BearerPest']='user_proton/common/lib/bearer_pest.php';
}
	
OC::$CLASSPATH['OCA\Proton\User']='user_proton/lib/user.php';
OC::$CLASSPATH['OCA\Proton\Group']='user_proton/lib/group.php';
OC::$CLASSPATH['OCA\Proton\Share'] = 'user_proton/lib/share.php';
OC::$CLASSPATH['OCA\Proton\OAuth'] = 'user_proton/lib/oauth.php';

OC_APP::registerAdmin('user_proton', 'settings');

if (\OCA\Proton\Util::isApiConfigured()) {
    OC_User::useBackend( new \OCA\Proton\User() );
    OC_Group::useBackend( new \OCA\Proton\Group() );
    \OCP\Util::connectHook('OC_User', 'post_login', 'OCA\Proton\User', 'postLogin');    
}

\OCP\Util::connectHook('OCP\Share', 'post_shared', 'OCA\Proton\Share', 'postShared');
\OCP\Util::connectHook('OCP\Share', 'post_unshare', 'OCA\Proton\Share', 'postUnshare');

if (\OCA\Proton\Util::isOAuthConfigured()) {
    OC_App::registerLogIn(array('href' => \OCP\Util::linkToRoute( 'proton_oauth'), 'name' => 'Prot-On OAuth'));
}

