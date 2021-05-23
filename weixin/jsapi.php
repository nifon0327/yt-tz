<?php

include_once ('token.php');

class jsapi{
	
	public function get_sign() {
		
		$jsapi_ticket = token::get_jsapi_ticket();

		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$timestamp = time();
		
		$nonceStr = $this->create_nonce();

		$string = "jsapi_ticket=$jsapi_ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
		
		  "appId"     => APPID,
		  "nonceStr"  => $nonceStr,
		  "timestamp" => $timestamp,
		  "url"       => $url,
		  "signature" => $signature,
		  "rawString" => $string//可要可不要
		  
		);
		
		return $signPackage; 
	
  }

  private function create_nonce($length = 16) {
	  
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	
    $str = "";
	
    for ($i = 0; $i < $length; $i++) {
		
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	  
    }
	
    return $str;
	
  }
	
}

?>