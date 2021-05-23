<?php    
require("../class.phpmailer.php");

$mail = new PHPMailer();
$mail->IsSMTP();					// 启用SMTP
$mail->Host = "smtp.qq.com";			//SMTP服务器
$mail->SMTPAuth = true;					//开启SMTP认证
$mail->Username = "270018735@qq.com";			// SMTP用户名
$mail->Password = "yangjianwei";				// SMTP密码

$mail->From = "270018735@qq.com";			//发件人地址
$mail->FromName = "eamon";				//发件人

$mail->CharSet = "utf-8";				// 这里指定字符集！
$mail->Encoding = "base64"; 

$mail->AddAddress("eamon_08@me.com", "Josh Adams");	//添加收件人
$mail->AddAddress("eamon_08@hotmail.com");
$mail->AddReplyTo("eamon_08@me.com", "Information");	//回复地址
$mail->WordWrap = 50;					//设置每行字符长度
/** 附件设置
$mail->AddAttachment("/var/tmp/file.tar.gz");		// 添加附件
$mail->AddAttachment("/tmp/image.jpg", "new.jpg");	// 添加附件,并指定名称
*/
$mail->IsHTML(true);					// 是否HTML格式邮件

$mail->Subject = "Here is the subject";			//邮件主题
$mail->Body    = "This is the HTML message body <b>in bold!</b>";		//邮件内容
$mail->AltBody = "This is the body in plain text for non-HTML mail clients";	//邮件正文不支持HTML的备用显示

if(!$mail->Send())
{
   echo "Message could not be sent. <p>";
   echo "Mailer Error: " . $mail->ErrorInfo;
   exit;
}

echo "Message has been sent"; 
?>  