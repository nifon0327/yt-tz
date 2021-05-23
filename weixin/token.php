<?php
include_once ("configure.php");
include_once ("fun.php");
include_once ('log.php');

class token{

	public static function get_access_token(){

		$token_array = self::get_db_token('access_token');

		if ($token_array[1] < time()) {

			$url_array = array(
				0 => 'cgi-bin/token',
				1 => array(
					'grant_type' => 'client_credential',
					'appid' => APPID,
					'secret' => APPSECRET
				)
			);

			$res = fun::https_request($url_array);

			$access_token = $res->access_token;

			if($res->errcode){

				log::e('errcode: '.$res->errcode, 'token_get_access_token');

			}

			if ($access_token) {

				$expire_time = time() + 1800;

				self::set_db_token($access_token, 'access_token', $expire_time);

			}

		} else {

			$access_token = $token_array[0];

		}

		return $access_token;

	}
	public static function get_jsapi_ticket(){

		$token_array = self::get_db_token('jsapi_ticket');

		if ($token_array[1] < time()) {

			$access_token = self::get_access_token();

			$url_array = array(
				0 => 'cgi-bin/ticket/getticket',
				1 => array(
					'access_token' => $access_token,
					'type' => 'jsapi'
				)
			);

			$res = fun::https_request($url_array);

			$jsapi_ticket = $res->ticket;

			if($res->errcode){

				log::e('errcode: '.$res->errcode, 'token_get_jsapi_ticket');

			}

			if ($jsapi_ticket) {

				$expire_time = time() + 3600;

				self::set_db_token($jsapi_ticket, 'jsapi_ticket', $expire_time);

			}

		} else {

			$jsapi_ticket = $token_array[0];

		}

		return $jsapi_ticket;

	}
	public static function get_db_token($token_name){

		$query = 'select CodeValue, ExpireTime from wx_code where CodeName="'.$token_name.'"';

		$cursor = mysql_query($query);

		if(!$cursor)

			log::e($query, 'token_get_'.$token_name);

		$row = mysql_fetch_row($cursor);

		return $row;

	}
	public static function set_db_token($token_value, $token_name, $expire_time){

		$query = "update wx_code set CodeValue='$token_value', ExpireTime='$expire_time' where CodeName='$token_name'";

		$cursor = mysql_query($query);

		if(!$cursor)

			log::e($query, 'token_set_'.$token_name);

	}
}
