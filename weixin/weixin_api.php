<?php

include_once ("token.php");

include_once ('log.php');

class weixin_api{

	public $access_token;
	
	public function weixin_api(){
		
		$this->access_token = token::get_access_token();
		
	}
	//生成菜单
	public function create_menu($data = array()){
		
		$url = 'cgi-bin/menu/create';
		
		$res = fun::https_request($this->general_array($url), json_encode($data, JSON_UNESCAPED_UNICODE));
		
		return $res;
	}
	//新增非图文素材
	public function add_none_article_material($name, $type){
		
		$url = 'cgi-bin/material/add_material';
		
		$path = $type.'\\'.$name;

		$data = $this->general_path($path);
		
		$res = fun::https_request($this->general_array($url, array('type'=>$type)), $data, true);
			
		return $res;	
	}
	//新增图文素材，****待调试
	public function add_article_material(){
		
		$url = 'cgi-bin/material/add_news';
		
		$data = array(		
			'articles' => array(				
				'title'=>'研砼治筑',
				'thumb_media_id'=>'Jgz5ziauB23bZ419hpYDhSju3F_rGYQLfJrlTrv9CHc',
				'author'=>'研砼治筑',
				'digest'=>'我是摘要',
				'show_cover_pic'=>1,
				'content'=>'
					<h3>研砼治筑</h3>
					<img src=\'http://mmbiz.qpic.cn/mmbiz_jpg/xhlJKqBiahDRS4dyQg2gzISaJt6sh5EprG8fxUPHlWoQTMN7EkAqgTE7icPjMSSibU9YtkYHAEYbtYj0ialAof5Fdg/0?wx_fmt=jpeg\' />
					<p>我是摘要</p>
					<img src=\'http://mmbiz.qpic.cn/mmbiz_gif/xhlJKqBiahDRS4dyQg2gzISaJt6sh5EpranzfZUM73vQX45eTKIbPFO2qGIxvaAibz3tfgPD9wehqHd8USyuWpRw/0\' />
					<p>我是正文</p>
					<img src=\'http://mmbiz.qpic.cn/mmbiz_jpg/xhlJKqBiahDRS4dyQg2gzISaJt6sh5EprpdpT3KNtWF5v0cvTYCpervdYRE4XopLakZ5AY6a0pILgf1eQy1A7fg/0\' />
					<p>我是结尾</p>				
				',
				'content_source_url'=>'http://y19732z685.iok.la/weixin/1.html'				
			)
		
		);
		
		// $data = '
		// {
			// "articles": [{
			// "title": "研砼治筑",
			// "thumb_media_id": "Jgz5ziauB23bZ419hpYDhSju3F_rGYQLfJrlTrv9CHc",
			// "author": "研砼治筑",
			// "digest": "我是摘要",
			// "show_cover_pic": 1,
			// "content": "<h3>研砼治筑</h3>",
			// "content_source_url": "http://y19732z685.iok.la/weixin/1.html"
			// }]			
		// }';
		// echo json_encode($data, JSON_UNESCAPED_UNICODE);
		
		$res = fun::https_request($this->general_array($url), json_encode($data, JSON_UNESCAPED_UNICODE));
		// $res = fun::https_request($this->general_array($url), $data);
		
		return $res->media_id;
		
	}
	//上传图文消息内的图片获取URL
	public function upload_image_for_article($name){
		
		$url = 'cgi-bin/media/uploadimg';
		
		$path = 'image\\'.$name;
		
		$data = $this->general_path($path);
		
		$res = fun::https_request($this->general_array($url), $data, true);
		
		return $res->url;
		
	}
	//获取素材列表
	public function get_material_list(){
		
		$url = 'cgi-bin/material/batchget_material';
		
		$data = array(
			'type'=>'news',//素材类型
			'offset'=>0,
			'count'=>20
		);
		
		$res = fun::https_request($this->general_array($url), json_encode($data));
		
		return $res;
		
	}
	//发送系统登录的模板消息，接收消息微信用户的openid、登录系统的用户名字、登录时间、具体登录信息、备注信息
	public function send_login_temp_msg($touser, $login_user, $login_time, $login_detail, $remark){
		
		$url = 'cgi-bin/message/template/send';		
		
		$data = array(
			
			"touser" => $touser,
			"template_id" => "Kws46R7opQygEBkcUNLK9xWUAmaGQajxO8jNu1A9aO4",
			"data" => array(
				"first" => array(
					'value' => "您好，{$login_user}登录系统成功！",
					'color' => '#173177'
				),
				'keyword1' => array(
					'value' => $login_user,
					'color' => '#173177'
				),
				'keyword2' => array(
					'value' => '登录系统',
					'color' => '#173177'
				),
				'keyword3' => array(
					'value' => $login_time,
					'color' => '#173177'
				),
				'keyword4' => array(
					'value' => $login_detail,
					'color' => '#173177'
				),
				'remark' => array(
					'value' => $remark,
					'color' => '#173177'
				)
			)
			
		);
		
		$res = fun::https_request($this->general_array($url), json_encode($data, JSON_UNESCAPED_UNICODE));
		
		if($res->errcode)

			log::e('errcode: '.$res->errcode, 'token_get_access_token');
		
		return $res;
		
	}
	
	//发送客服消息
	public function send_custom_msg($touser, $msg){
		
		$url = 'cgi-bin/message/custom/send';
		
		$data = array(
			
			"touser" => $touser,
			"msgtype" => "text",
			"text" => array(
				"content" => $msg
			)
			
		);
		
		$res = fun::https_request($this->general_array($url), json_encode($data, JSON_UNESCAPED_UNICODE));
		
		return $res;
		
	}	
	
	//根据版本，设置适用的path参数
	public function general_path($path){
		
		if (class_exists('CURLFile')) {
			
			$data = array('media' => new CURLFile(realpath($path)));
			
		} else {
			
			$data = array('media' => '@' . realpath($path));
			
		}
		
		return $data;
		
	}
	public function general_array($url, $arr = array()){

		$arr['access_token'] = $this->access_token;
		
		$array = array(
			0 => $url,
			1 => $arr	
		);
		
		return $array;
		
	}
	
}
