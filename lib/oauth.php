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

require_once('PHP-OAuth2/Client.php');
require_once('PHP-OAuth2/GrantType/IGrantType.php');
require_once('PHP-OAuth2/GrantType/AuthorizationCode.php');
require_once('PHP-OAuth2/GrantType/RefreshToken.php');


class OAuth {
    
    const AUTHORIZATION_ENDPOINT = '/external/oauth/authorize';
    const TOKEN_ENDPOINT         = '/external/oauth/token';

    public static function OAuth(){
        if (!\OCA\Proton\Util::isOAuthConfigured()) {
            echo "You must configure Prot-On OAuth settings first";
            die();
        }
		$client = new \OAuth2\Client(\OC_Config::getValue( "user_proton_oauth_client_id" ), \OC_Config::getValue( "user_proton_oauth_secret" ), \OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
		if (!isset($_GET['code'])) {
			$auth_url = $client->getAuthenticationUrl(\OC_Config::getValue( "user_proton_url" ).self::AUTHORIZATION_ENDPOINT, \OC_Helper::makeURLAbsolute(\OCP\Util::linkToRoute( 'proton_oauth')));
			header('Location: ' . $auth_url);
			die('Redirect');
		} else {
			$params = array('code' => $_GET['code'], 'redirect_uri' => \OC_Helper::makeURLAbsolute(\OCP\Util::linkToRoute( 'proton_oauth')));
            Util::log("Code retrieved: ".$_GET['code']);
			$response = $client->getAccessToken(\OC_Config::getValue( "user_proton_url" ).self::TOKEN_ENDPOINT, 'authorization_code', $params);
			$token = Util::parseOAuthTokenResponse($response);
			$pest = Util::getPest(false);
			$pest->setupAuth($token['access_token'], '', 'bearer');
			try {
				$thing = $pest->get('/users/userInfo');
			} catch (\Pest_Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
				die();
			}
			
			$info = json_decode($thing, true);
            $hostingConfig = \OC_Config::getValue( "user_proton_hosting");
            if (!empty($hostingConfig) && $hostingConfig !== $info['hostingname']) {
                echo "The current logged user can not use OwnCloud due to Enterprise retrictions";
                return;
            }
            
            Util::setToken($token);
			$uid = $info['id'];
			
			session_regenerate_id(true);
			\OC_User::setUserid($uid);
			\OC_User::setDisplayName($uid, $info['completename']);
			\OC_Hook::emit( "OC_User", "post_login", array( "uid" => $uid, 'password'=>'aaa' ));
			\OC_User::unsetMagicInCookie(); //Disable remember me
            Util::markProtOnUser();
			\OC_Util::redirectToDefaultPage();
		}
    }
}
?>