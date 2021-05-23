<?php
/*
接口参数:
$bundleId:标识App应用Id
$message:显示内容
$userinfo:自定义内容
*/
// include "../basic/parameter.inc";
//关闭当前页面的PHP警告及提示信息
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING) ; 

   if ($DataIn==""){
	   include "d:/website/ac/basic/parameter.inc";
   }
   
    $rootPath = "d:/website/ac";
    $pushSign=0;
    $pushTime=date("Y-m-d H:i:s");
    
	switch($bundleId){
	   case "AshCloudApp":
		   $pushSign=1;
		   $pass = "123456";
		   $perPath = $rootPath ."/iPhoneAPI/pushCer/PushAshCloudApp.pem";
		   $tokenSql = "Select token From $DataIn.push_mainapp Where bundleId = '$bundleId' and userId IN ($userIdSTR)";
		   break;
		default:
		    $pushSign=1;
		    $pass = "123456";
		    $userId=strtolower($userId);
		    $perPath = $rootPath ."/iPhoneAPI/pushCer/Push".$bundleId.".pem";
		     $tokenSql = "Select token From $DataIn.push_clientapp Where bundleId = '$bundleId' and userId='$userId'";
		  break;
	
	}
 
 if ($pushSign==1){
			  $body = array("aps" => array("alert" => $message, "badge" => 1, "sound" => 'received5.caf',"userinfo"=>$userinfo));
			  
			 $ctx = stream_context_create();
			 stream_context_set_option($ctx, 'ssl', 'local_cert', $perPath); 
			 stream_context_set_option($ctx, 'ssl', 'passphrase', $pass); 
			 
			 //gateway.sandbox.push.apple.com:2195  //gateway.push.apple.com:2195
			$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
			
			if($fp){
				    $OutputInfo="$pushTime connect success \r\n";
                    $OutputInfo.="Push message:$message \r\n";
				    //连接成功，推送信息
					$payload = json_encode($body);
					$counts=0;$sumSuccess=0;$sumFail=0;
					//$tokenSql = "Select token From $DataIn.push_clientapp Where bundleId = '$bundleId' and userId='$userId'";
					$tokenResult = mysql_query($tokenSql,$link_id);
					while($tokenRow = mysql_fetch_assoc($tokenResult))
					{
						$counts++;
						$deviceToken = $tokenRow["token"];
						$msg = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($payload)) . $payload;
						if(fwrite($fp, $msg,strlen($msg)))
						{
						   $sumSuccess++;
						}
						else{
							$sumFail++;
						}
					}
					$OutputInfo.="$bundleId Push Totals:$counts   Success:$sumSuccess  Fail: $sumFail \r\n";	
			}
			else
			{
				$OutputInfo="conncet error! errorNO:$err Description:$errstr \r\n\r\n";
			}
	   fclose($fp);
	   //写入日志文件
	   $fs = fopen($rootPath . "/iphoneAPI/push_autorun.log", "a");
       fwrite($fs, $OutputInfo);
       fclose($fs);
 }
?>