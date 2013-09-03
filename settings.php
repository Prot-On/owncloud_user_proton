<?php

/**
 * ownCloud - user_webdavauth
 *
 * @author Frank Karlitschek
 * @copyright 2012 Frank Karlitschek frank@owncloud.org
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
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
    if(isset($_POST['proton_hosting_admin_login'])) {
        OC_CONFIG::setValue('user_proton_hosting_admin_login', strip_tags($_POST['proton_hosting_admin_login']));
    }
    if(isset($_POST['proton_hosting_admin_password'])) {
        OC_CONFIG::setValue('user_proton_hosting_admin_password', strip_tags($_POST['proton_hosting_admin_password']));
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
$tmpl->assign( 'proton_hosting_admin_login', OC_Config::getValue( "user_proton_hosting_admin_login" ));
$tmpl->assign( 'proton_hosting_admin_password', OC_Config::getValue( "user_proton_hosting_admin_password" ));
$tmpl->assign( 'proton_db_connection', OC_Config::getValue( "user_proton_db_connection" ));


return $tmpl->fetchPage();
