<?php

class cUrl {
	
	// The cUrl object
	private $curl;
	
	function __construct() {
		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($this->curl, CURLOPT_COOKIESESSION, TRUE);
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($this->curl, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($this->curl, CURLOPT_COOKIEFILE, 'cookie.txt');
	}
	
	function post($page, $data) {
		curl_setopt($this->curl, CURLOPT_POST, 1);
		curl_setopt($this->curl, CURLOPT_URL, $page);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($this->curl);
		if ($result === false) {
			echo "cUrl error: ".curl_error($this->curl);
		}
		
		return $result;
	}
	
	function get($page) {
		curl_setopt($this->curl, CURLOPT_HTTPGET, 1);
		curl_setopt($this->curl, CURLOPT_URL, $page);
		$result = curl_exec($this->curl);
		if ($result === false) {
			echo "cUrl error: ".curl_error($this->curl);
		}
		
		return $result;
	}
}
