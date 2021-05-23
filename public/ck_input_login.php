<?php 
//电信-zxq 2012-08-01
session_start();
include "../basic/parameter.inc";			//数据库连接
include "../model/modelfunction.php";		//读入函数
//输入检错
if ($LoginN!=""){
//$Password=MD5($Password);

//检查数据库是否存在用户
$mySql="SELECT U.Id,U.Number,S.Name,P.Action FROM 
$DataIn.upopedom P 
LEFT JOIN $DataIn.usertable U ON U.Id=P.UserId
LEFT JOIN $DataPublic.staffmain S ON S.Number=U.Number
WHERE P.ModuleId=1013 AND U.Number='$LoginN' AND U.Estate=1 AND S.Estate>0 ORDER BY Id LIMIT 1";
$myResult = mysql_query($mySql." $PageSTR",$link_id) or die ("数据连接错误!");
if($myRow = mysql_fetch_array($myResult)){
	$Login_Id=$myRow["Id"];				//用户ID
	$Login_Name=$myRow["Name"];			//用户姓名
	$Login_P_Number=$myRow["Number"];		//用户编号
	$Action=$myRow["Action"];
	//权限检查
	if($Action & mADD){//有权限
		$RebackSTR=$Login_P_Number."".$Login_Name;
		//session_register("Login_Name"); 		//用户名称
		$_SESSION["Login_Name"] = $Login_Name;
		//session_register("Login_P_Number"); 	//用户编号
		$_SESSION["Login_P_Number"] = $Login_P_Number;
		/*
		$Login_Date=date("m-d H:i");			//登录时间
		$Login_IP=GetIP();
		$webIp=gethostbyname('www.middlecloud.com');
		if(preg_match('/^192\.168/',$Login_IP)){//IP地址为192开头则为内部登录
			$Login_IP="内网:".$Login_IP;
			}
		else{
			if($Login_IP==$webIp){				//如果IP地址与公司域名一致，则为内部域名登录
				$Login_IP="内网域名:".$Login_IP;
				}
			else{								//否则为外网登录
				$Login_IP="外网:".$Login_IP;
				}
			}
		//登记用户最后一次登录系统的时间
		$LoginDate=date("Y-m-d H:i:s");
		$LoginSql = "UPDATE $DataIn.UserTable SET lDate='$LoginDate' WHERE 1 AND Id='$Login_Id' LIMIT 1";
		$LoginResult = mysql_query($LoginSql);
		*/
		}
	}
	}
echo $RebackSTR;
echo"<script>window.returnValue='$RebackSTR';window.close(); </script>";
?>
