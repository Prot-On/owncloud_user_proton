<?php
namespace OCA\Proton;

require_once('PHP-OAuth2/Client.php');
require_once('PHP-OAuth2/GrantType/IGrantType.php');
require_once('PHP-OAuth2/GrantType/AuthorizationCode.php');
require_once('PHP-OAuth2/GrantType/RefreshToken.php');


const CLIENT_ID     = '5708';
const CLIENT_SECRET = '64QYswnc@C';

const AUTHORIZATION_ENDPOINT = 'https://demo.aws.prot-on.com/external/oauth/authorize';
const TOKEN_ENDPOINT         = 'https://demo.aws.prot-on.com/external/oauth/token';

class OAuth {

    public static function OAuth(){
		$client = new \OAuth2\Client(CLIENT_ID, CLIENT_SECRET, \OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
		if (!isset($_GET['code'])) {
			$auth_url = $client->getAuthenticationUrl(AUTHORIZATION_ENDPOINT, \OC_Helper::makeURLAbsolute(\OCP\Util::linkToRoute( 'proton_oauth')));
			header('Location: ' . $auth_url);
			die('Redirect');
		} else {
			$params = array('code' => $_GET['code'], 'redirect_uri' => \OC_Helper::makeURLAbsolute(\OCP\Util::linkToRoute( 'proton_oauth')));
			$response = $client->getAccessToken(TOKEN_ENDPOINT, 'authorization_code', $params);
			$token = self::parseResponse($response);
			
			$pest = Util::getPest(false);
			$pest->setupAuth($token['access_token'], '', 'bearer');
			try {
				$thing = $pest->get('/users/userInfo');
			} catch (\Pest_Exception $e) {
				echo 'Excepcion capturada: ',  $e->getMessage(), "\n";
				die();
			}
			
			$info = json_decode($thing, true);
			self::setToken($token);
			Util::storeCompleteName($info['completename']);
			$uid = $info['username'];
			
			
			session_regenerate_id(true);
			\OC_User::setUserid($uid);
			\OC_User::setDisplayName($uid);
			\OC_Hook::emit( "OC_User", "post_login", array( "uid" => $uid, 'password'=>'' ));
			\OC_Util::redirectToDefaultPage();
		}
    }
	
	public static function setToken($token) {
		$_SESSION['proton']['access_token'] = $token;
	}

	protected static function _getToken() {
		return isset($_SESSION['proton']['access_token'])?$_SESSION['proton']['access_token']:null;
	}
	
	public static function getToken() {
		$token = self::_getToken();
		if ($token == null) {
			return null;
		}
		$currentDate = new \DateTime("now");
		Util::log('Current: ' . $currentDate->format("Y-m-d\TH:i:s\Z"). ', Expiration: ' . $token['expiration']->format("Y-m-d\TH:i:s\Z"));
		if ($currentDate < $token['expiration']) {
			$client = new \OAuth2\Client(CLIENT_ID, CLIENT_SECRET, \OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
			$params = array('refresh_token' => $token['refresh_token']);
			$response = $client->getAccessToken(TOKEN_ENDPOINT, 'refresh_token', $params);
			$token = self::parseResponse($response);
			self::setToken($token);
		}
		return $token['access_token'];
	}
	
	protected static function parseResponse($response) {
		if ($response['code'] == 200) {
			$token = $response['result'];
			$date = new \DateTime("now");
			$token['expiration'] = $date->add(new \DateInterval('PT'.$token['expires_in'].'S'));
			return $token;
		}
		return null;
	}
}
?>