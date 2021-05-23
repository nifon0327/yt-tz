<?php

session_start();

include_once ('configure.php');

include_once ('fun.php');

include_once ('log.php');

class auth{
	
	private $url, $code, $state, $access_token, $openid, $nickname, $headimgurl;
	
	public function auth(){
		
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		
		$this->url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		if(isset($_GET['code'])){//重新授权
			
			$this->code = $_GET['code'];
		
			$this->state = $_GET['state'];
			
			$this->first_auth();			
	
		}else{			
			
			$this->second_auth();//二次授权
			
		}
		
	}
	//完整授权
	public function first_auth(){
		
		$url_array = array(

			0 => 'sns/oauth2/access_token',
			1 => array(
				'appid' => APPID,
				'secret' => APPSECRET,
				'code' => $this->code,
				'grant_type' => 'authorization_code'
			)
			
		);
		
		$res = fun::https_request($url_array);
//		var_dump($res);die;
		if($res->errcode)
			
			log::e($res->errcode, 'auth_sns/oauth2/access_token');
		
		$this->register($res);
		
	}
	
	//二次授权
	public function second_auth(){
		
		if(isset($_SESSION['openid'])){			
			
			return;//有sesseion 不需要鉴权
			
		}
		
		if(isset($_COOKIE['openid'])){
			
			//有cookie，拉取详细信息
			$this->openid = $_COOKIE['openid'];
		
			//[0]=>access_token [1]=>过期时间 [2]=>refresh_token [3]=>过期时间
			$token_array = $this->get_db_token($this->openid);			
		
			if($token_array[1] < time()){//access_token过期
				
				//access_token 过期，判断refresh_token 是否过期，不过期则通过其刷新，否则重新鉴权		
				if($token_array[3] < time()){//refresh_token 过期了，完整授权
					
					header('Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid='.APPID.'&redirect_uri='.urldecode($this->url).'&response_type=code&scope=snsapi_userinfo&state=cndotabestdota#wechat_redirect');
					
					exit();
					
				}else{
								
					$this->refresh_token($token_array[2]);//可一直到写入session
					
				}
				
			}else{//access_token 有效，拉取详细信息
				
				$this->access_token = $token_array[0];
				
				$this->get_user_detail();
				
			}
		
		}else{
			
			header('Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid='.APPID.'&redirect_uri='.urldecode($this->url).'&response_type=code&scope=snsapi_userinfo&state=cndotabestdota#wechat_redirect');
			
			exit();
			
		}		
		
	}
	//赋值
	public function register($res){
		
		$this->access_token = $res->access_token;//网页授权access_token
		
		$expires_in = $res->expires_in;//access_token过期时间
		
		$refresh_token = $res->refresh_token;//刷新access_token的凭证，30天过期

		$this->openid = $res->openid;//用户id
		
		$this->set_db_token($this->openid, $this->access_token, (int)$expires_in + time() - 200, $refresh_token, time()+2591800);
		
		//写入或更新token
		setcookie('openid', $this->openid, time() + 7200);
		
		$this->get_user_detail();
		
	}
		
	//通过refresh_token 刷新access_token
	public function refresh_token($refresh_token){
		
		$url_array = array(

			0 => 'sns/oauth2/refresh_token',
			1 => array(
				'appid' => APPID,
				'grant_type' => 'refresh_token',
				'refresh_token' => $refresh_token
			)
			
		);
		
		$res = fun::https_request($url_array);	
		
		if($res->errcode)
			
			log::e($res->errcode, 'auth_refreh_token');
		
		$this->register($res);
		
	}
	
	public function get_user_detail(){
		
		//拉取用户具体信息
		$url_array = array(

			0 => 'sns/userinfo',
			1 => array(
				'access_token' => $this->access_token,
				'openid' => $this->openid,
				'lang' => 'zh_CN'
			)

		);

		$res = fun::https_request($url_array);
		
		if($res->errcode)
			
			log::e($res->errcode, 'auth_sns/userinfo');

		$this->nickname = $res->nickname;//用户昵称

		$this->headimgurl = $res->headimgurl;//用户头像
		
		$this->set_session();
		
	}
	
	//设置session
	public function set_session(){
		
		$_SESSION['openid'] = $this->openid;
		
		$_SESSION['nickname'] = $this->nickname;
		
		$_SESSION['headimgurl'] = $this->headimgurl;
		
	}
	//设置各类型token及其有效期
	public static function set_db_token($openid, $access_token, $at_expire_time, $refresh_token, $rt_expire_time){
		
		$query = 'select count(*) from wx_token where openid=".$openid."';
		
		$cursor = mysql_query($query);
		
		$row = mysql_fetch_row($cursor);
		
		if($row[0]){//有记录更新
		
			$query = "update wx_token set 
				access_token='$access_token', 
				at_expire_time='$at_expire_time', 
				refresh_token='$refresh_token', 
				rt_expire_time='$rt_expire_time' 
			where openid='$openid'";
			
			mysql_query($query);
			
		}else{//无记录新增
			
			$query = "insert into wx_token(openid, access_token, at_expire_time, refresh_token, rt_expire_time)
				value('$openid', '$access_token', '$at_expire_time', '$refresh_token', '$rt_expire_time')";
			
			mysql_query($query);
		}
	}
	//获取各类型token及其有效期	
	public static function get_db_token($openid){
		
		$query = "select 
			access_token, at_expire_time, refresh_token, rt_expire_time 
			from wx_token
			where openid='$openid'";
		
		$cursor = mysql_query($query);
		
		$row = mysql_fetch_row($cursor);
		
		return array($row[0], $row[1], $row[2], $row[3]);
		
	}	
	
	
}

?>
