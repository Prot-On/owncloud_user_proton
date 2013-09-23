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
 
OC_Util::checkAdminUser();

if($_POST) {
	// CSRF check
	OCP\JSON::callCheck();

	if(isset($_POST['proton_url'])) {
		OC_CONFIG::setValue('user_proton_url', strip_tags($_POST['proton_url']));
	}
    if(isset($_POST['proton_oauth_client_id'])) {
        OC_CONFIG::setValue('user_proton_oauth_client_id', strip_tags($_POST['proton_oauth_client_id']));
    }
    if(isset($_POST['proton_oauth_secret'])) {
        OC_CONFIG::setValue('user_proton_oauth_secret', strip_tags($_POST['proton_oauth_secret']));
    }
    if(isset($_POST['proton_api_url'])) {
        OC_CONFIG::setValue('user_proton_api_url', strip_tags($_POST['proton_api_url']));
    }
    if(isset($_POST['proton_hosting'])) {
        OC_CONFIG::setValue('user_proton_hosting', strip_tags($_POST['proton_hosting']));
    }
    if(isset($_POST['proton_mysql_login'])) {
        OC_CONFIG::setValue('user_proton_mysql_login', strip_tags($_POST['proton_mysql_login']));
    }
    if(isset($_POST['proton_mysql_password'])) {
        OC_CONFIG::setValue('user_proton_mysql_password', strip_tags($_POST['proton_mysql_password']));
    }
    if(isset($_POST['proton_db_connection'])) {
        OC_CONFIG::setValue('user_proton_db_connection', strip_tags($_POST['proton_db_connection']));
    }
}

// fill template
$tmpl = new OC_Template( 'user_proton', 'settings');
$tmpl->assign( 'proton_url', OC_Config::getValue( "user_proton_url" ));
$tmpl->assign( 'proton_oauth_client_id', OC_Config::getValue( "user_proton_oauth_client_id" ));
$tmpl->assign( 'proton_oauth_secret', OC_Config::getValue( "user_proton_oauth_secret" ));
$tmpl->assign( 'proton_api_url', OC_Config::getValue( "user_proton_api_url" ));
$tmpl->assign( 'proton_hosting', OC_Config::getValue( "user_proton_hosting" ));
$tmpl->assign( 'proton_mysql_login', OC_Config::getValue( "user_proton_mysql_login" ));
$tmpl->assign( 'proton_mysql_password', OC_Config::getValue( "user_proton_mysql_password" ));
$tmpl->assign( 'proton_db_connection', OC_Config::getValue( "user_proton_db_connection" ));


return $tmpl->fetchPage();
