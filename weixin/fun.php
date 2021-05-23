<?php

include_once ("configure.php");

class fun{
	
	public static function https_request($url_array, $data = null, $upload_file = false){	
		$url = isset($url_array[1])?'?'.@http_build_query($url_array[1]):'';	
		$url = APIURL.$url_array[0].$url;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
		
		if($upload_file){
			if (class_exists('CURLFile')) {
				curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
			} else {
				if (defined('CURLOPT_SAFE_UPLOAD')) {
					curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
				}
			}
		}
		
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return json_decode($output);
	}	
	
}
