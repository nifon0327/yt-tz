<?php

include_once ("configure.php");

include_once ('token.php');

include_once ('wxBizMsgCrypt.php');

function warning($value){
	
    $file = fopen("warning.html","aw");
	
    fwrite($file,$value."</br>");
	
    fclose($file);
	
}
$a='';
foreach($_GET as $key=>$val){
	$a .= $key.'=>'.$val.'</br>';
}
warning($a);
$wechatObj = new weixin();

if (!isset($_GET['echostr'])) {	

    $wechatObj->responseMsg();	
	
}else{	

    $wechatObj->valid();	
	
}

class weixin{
	
	private $timestamp, $nonce, $signature, $wxcpt, $msg_signature, $encrypt_type;
	
	public function weixin(){	
	
		$this->timestamp = $_GET["timestamp"];
		
		$this->nonce = $_GET["nonce"];	
		
		$this->signature = $_GET["signature"];
		
	} 
	
	public function valid(){		
		
		$echoStr = $_GET["echostr"];
		
		$tmpArr = array(TOKEN, $this->timestamp, $this->nonce);
		
		sort($tmpArr, SORT_STRING);
		
		$tmpStr = implode( $tmpArr );
		
		$tmpStr = sha1( $tmpStr );
		
		if($this->signature == $tmpStr){
			
			echo $echoStr;
			
		}else{
			
			return false;
			
		}
	}
	
	public function responseMsg()
    {
		$this->msg_signature = $_GET['msg_signature'];
		
		$this->encrypt_type= $_GET['encrypt_type'];
		
		$postStr = file_get_contents('php://input');
		
		$this->wxcpt = new WXBizMsgCrypt(TOKEN, EncodingAESKey, APPID);	
		
		$decryptMsg = "";
        
		$errCode = $this->wxcpt->decryptMsg($this->msg_signature, $this->timestamp, $this->nonce, $postStr, $decryptMsg);
		
		if($errCode == 0){
			
			$xml = new DOMDocument();
			
			$xml->loadXML($decryptMsg);

			$MsgType = strtolower($xml->getElementsByTagName('MsgType')->item(0)->nodeValue);
			
			$FromUserName = $xml->getElementsByTagName('FromUserName')->item(0)->nodeValue;
			
			$timestamp = time();
				
			$time = date("Y-m-d H:i:s", $timestamp);
			
			switch ($MsgType){
				
				case "event":
				
				$MsgEvent = strtolower($xml->getElementsByTagName("Event")->item(0)->nodeValue);
				
				$MsgEventKey = $xml->getElementsByTagName("EventKey")->item(0)->nodeValue;//应该是菜单的key
				
				switch ($MsgEvent){
					
					case "click":
					
					switch ($MsgEventKey){
						
						case "ytzz":
						
						//研砼治筑	
						$result = '系统正在维护，请稍候再试！';	
						
						break;
						
						case "cztz":
						
						//研砼治筑	
						$result = '系统正在维护，请稍候再试！';	
						
						break;
						
						case "gncs":
						
						//研砼治筑	
						$result = '系统正在维护，请稍候再试！';	
						
						break;
						
					}
					
					$this->transmitText($xml, $result);
					
					break;
					
					case "location":
					
					break;
					
					case "subscribe"://被关注
					
					$result = "公司新闻专号：研砼治筑工业化建筑专家\n行业新闻专号：研砼治筑\n感谢您的关注！";
					
					$this->transmitText($xml, $result);
					
					break;
					
				}
				
				
				
				break;
				
				case "text":
				
				$content = $xml->getElementsByTagName('Content')->item(0)->nodeValue;				
				
				$this->transmitText($xml, '研砼欢迎您！');
				
				break;
				
			}
			
		}else{
			
			print("ERR: " . $errCode . "\n\n");
			
		}
        
    }
	
	//回复文本消息
    private function transmitText($xml, $content){

		$reqToUserName = $xml->getElementsByTagName('ToUserName')->item(0)->nodeValue;
        
		$reqFromUserName = $xml->getElementsByTagName('FromUserName')->item(0)->nodeValue;

		$xmlTpl = "<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>";
			
        $result = sprintf($xmlTpl, $reqFromUserName, $reqToUserName, time(), $content);
		
		// echo $result;
		
		$sEncryptMsg = ""; //加密密文
		
		$errCode = $this->wxcpt->encryptMsg($result, $this->timestamp, $this->nonce, $sEncryptMsg);
		
		if ($errCode == 0) {

			echo $sEncryptMsg;	
		
		} else {

			print("ERR: " . $errCode . "\n\n");
			
		}
		
    }
	
} 
      
?>