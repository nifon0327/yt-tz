<?   

//使用方法   

require("subprogram/smtp.class.php");   

$smtpserver = "smtp.qq.com";//SMTP服务器   

$smtpserverport =25;//SMTP服务器端口   

$smtpusermail = "270018735@qq.com";//SMTP服务器的用户邮箱   

$smtpemailto = "eamon_08@me.com";//发送给谁   

$smtpuser = "270018735@qq.com";//SMTP服务器的用户帐号   

$smtppass = "yangjianwei";//SMTP服务器的用户密码   

$mailsubject = "php邮件发送成功";//邮件主题   

$mailbody = "<h1>测试邮件的内容</h1>";//邮件内容   

$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件   

$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.   

$smtp->debug = TRUE;//是否显示发送的调试信息   

$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);   

?>