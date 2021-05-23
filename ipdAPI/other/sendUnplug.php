<?php 

	include "../basic/parameter.inc";
	require_once("phpMailer/class.phpmailer.php");
	include "phpMailer/class.smtp.php";
	
	//$IP="192.168.1.254";	//公司摄像头ＩＰ地址
	//$Port="2540";			//摄像头端口
	//$url="http://$IP:$Port/ImageViewer?Mode=Motion&Resolution=640x400&Quality=Standard&Interval=10";//通过摄像头捉拍图片
	
	
	$ipCamera = $_POST["ipCamera"];
	$ipCam = split("-",$ipCamera);
	$floor = $ipCam[0];
	
	$ipAddress = split(":",$ipCam[1]);
	
	$IP = $ipAddress[0];
	$Port = $ipAddress[1];
	
	$ServerPath = $_SERVER['DOCUMENT_ROOT']."/warming";
	if(!file_exists($ServerPath))
	{
		makedir($ServerPath);
	}
	
	$url="http://$IP:$Port/SnapshotJPEG?Resolution=640x480&Quality=Standard";
	echo $url;
	$RecordImg="../warming/".time().".bmp"; ;//报警图片存放位置
	$filePath = GrabImage($url, "$RecordImg"); //捉拍图片并保存
	//加入邮件或短信处理
	$emailArray = array();
	//$emailArray = array("joseph_liu@me.com");
	$date = date("Y-m-d H:i:s");
	
	if($filePath != "")
	{
		for($i=0;$i<count($emailArray);$i++)
		{
			$mail = new PHPMailer();
	
			$address = $emailArray[$i];
			$mail->IsSMTP();
			$mail->Host = "smtp.163.com";
			$mail->SMTPAuth = true; // 打开SMTP   
			$mail->Username = ""; // SMTP账户   
			$mail->Password = ""; // SMTP密码
			$mail->From = "";   
			$mail->FromName = "AshCloud Alerter";   
			$mail->AddAddress("$address", "");  
	
			$mail->CharSet = "UTF-8";//设置字符集编码   
			$mail->Subject = "断电报警".$date;  
			//$mail->Body = "<img src='$filePath'>";//邮件内容（可以是HTML邮件）
			$mail->Body = $floor."—ipad电源断开"."  ica:".$ipCamera;
			$mail->addattachment('../warning/'.$filePath, 'pic.bmp');
	
			if(!$mail->Send())   
			{   
				echo "Message could not be sent. <p>";   
				echo "Mailer Error: " . $mail->ErrorInfo;   
 				exit;   
			}   
    
		echo "Message has been sent to ".$emailArray[$i];//发送成功显示的信息
		
	}	
		
		$unplugSqlStr = "Insert into $DataIn.unplug_data (Id,Floor,ImgURL,Date) value (NULL,'$floor','$RecordImg','$date')"; 
		mysql_query($unplugSqlStr);
		//echo $unplugSqlStr;
	}
	
	
	function GrabImage($url, $filename){ 
	if($url == ""){//$url 为空则返回 false; 
		return false;
	} 
	$ext = strrchr($url, ".");//得到图片的扩展名 
	//if($ext != ".gif" && $ext != ".jpg" && $ext != ".bmp"){echo "格式不支持！";return false;} 
	if($filename == ""){
	$filename = time()."$ext";//以时间戳另起名 
	}
		//开始捕捉 
	ob_start(); 							//打开输出缓冲
	readfile($url); 						//读入一个文件并写入到输出缓冲
	$img = ob_get_contents(); 		//返回输出缓冲的内容
	ob_end_clean(); 					//清除输出缓冲
	$size = strlen($img); 
	$fp2 = fopen($filename , "a");  //打开文件
	fwrite($fp2, $img); 				//复制文件
	fclose($fp2); 
	return $filename; 
	}   
	
	//创建目录
function makedir( $dir, $mode = "0777" ) {
 if( ! $dir ) return 0;
 $dir = str_replace( "\\", "/", $dir );
 
 $mdir = "";
 foreach( explode( "/", $dir ) as $val ) {
  $mdir .= $val."/";
  if( $val == ".." || $val == "." ) continue;
  
  if( ! file_exists( $mdir ) ) {
   if(!@mkdir( $mdir, $mode )){
    echo "创建目录 [".$mdir."]失败.";
    exit;
   }
  }
 }
 return true;
}

	
?>