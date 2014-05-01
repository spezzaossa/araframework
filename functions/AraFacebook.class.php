<?php

require_once("facebook/facebook.php");

class AraFacebook {

	private $facebook = NULL;
//	private $access_token = NULL;
	private $user_profile = NULL;

	public function __construct($config = null) {
		if (!$config){
			$config = array();
			$config['appId'] = FACEBOOK_APP_ID;
			$config['secret'] = FACEBOOK_SECRET;
			$config['appUrl'] = FACEBOOK_APP_URL;
		}

		$this->facebook = new Facebook($config);
		$this->getUser();
	}

	public function getUser() {
		$fb_user = $this->facebook->getUser() ? $this->facebook->getUser() : NULL;

		if ($fb_user) {
			try {
				$this->user_profile = $this->facebook->api('/me');
			} catch (FacebookApiException $e) {
				error_log($e);
				$this->user_profile = NULL;
			}
		}

		return $fb_user;
	}

	public function getLoginUrl($params = array()) {
		return $this->facebook->getLoginUrl($params);
	}

	public function getLogoutUrl($params = array()) {
		return $this->facebook->getLogoutUrl($params);
	}

	public function getAccessToken() {
		return $this->facebook->getAccessToken();
	}

	public function api() {
		$args = func_get_args();
		return call_user_func_array(array($this->facebook, 'api'), $args);
	}

	public function setAccessToken($access_token) {
		return $this->facebook->setAccessToken($access_token);
	}

	public function getUserProfile() {
		return $this->user_profile;
	}

	public function getUserProfileName() {
		return $this->user_profile['name'];
	}

	public function getUserProfileId() {
		return $this->user_profile['id'];
	}

	public function getUserPages() {
		return $this->facebook->api('/me/accounts');
	}

}

?>