<?php
namespace OCA\Proton;

/**
 * Class Proxy
 * @package OCA\Proton
 */
class Share {

	public static function postShared($params) {
		// NOTE: $params has keys:
		// [itemType] => file
		// itemSource -> int, filecache file ID
		// [parent] => 
		// [itemTarget] => /13
		// shareWith -> string, uid of user being shared to
		// fileTarget -> path of file being shared
		// uidOwner -> owner of the original file being shared
		// [shareType] => 0
		// [shareWith] => test1
		// [uidOwner] => admin
		// [permissions] => 17
		// [fileSource] => 13
		// [fileTarget] => /test8
		// [id] => 10
		// [token] =>
		// [run] => whether emitting script should continue to run
		// TODO: Should other kinds of item be encrypted too?
		
		Util::log('Share call received');

	}
	
	public static function postUnshare($params) {

		// NOTE: $params has keys:
		// [itemType] => file
		// [itemSource] => 13
		// [shareType] => 0
		// [shareWith] => test1
		// [itemParent] =>
		
		Util::log('Unshare call received');
	}
	
}
?>