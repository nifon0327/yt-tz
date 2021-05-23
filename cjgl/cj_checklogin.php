<?php   
//电信-zxq 2012-08-01
session_start();
echo"
<html>
<head>
<META content='MSHTML 6.00.2900.2722' name=GENERATOR>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
</head>
<body>";
include "../basic/parameter.inc";			//数据库连接
include "../model/modelfunction.php";		//读入函数
$url="javascript:history.back()";
//检查读入的ID是否有帐号
$mySql="SELECT U.uType,U.Id,U.uName,U.Number,U.Estate FROM $DataIn.UserTable U WHERE 1 AND U.Number='$CardId' AND U.uType=1 ORDER BY U.Id LIMIT 1";
$myResult = mysql_query($mySql." $PageSTR",$link_id) or die ("数据连接错误!");
if($myRow = mysql_fetch_array($myResult)){
	$Estate=$myRow["Estate"];
	if($Estate==0){		//禁用状态
		echo "<SCRIPT LANGUAGE=JavaScript>alert('登录失败，该帐号目前禁用!');";
		echo "location.href='$url'"; 
		echo "</script>";
		}
	else{				//非禁用状态
		//用户类型:0-内部员工，1-客户，3-供应商，4-外部员工，5-参观者
		$Login_uType=$myRow["uType"];				
		$Login_Id=$myRow["Id"];					//用户ID
		$Login_uName=$myRow["uName"];			//用户姓名
		$Login_P_Number=$myRow["Number"];		//用户编号
		$Login_Date=date("m-d H:i");			//登录时间
		//用户IP检测
		$Login_IP=GetIP();
		$webIp="113.105.87.188";
		if(preg_match('/^192\.168/',$Login_IP)){//IP地址为192开头则为内部登录
			$uFrom=1;
			}
		else{
			if($Login_IP==$webIp){				//如果IP地址与公司域名一致，则为内部域名登录
				$uFrom=2;
				}
			else{								//否则为外网登录
				$uFrom=3;
				}
			}
		//登记用户最后一次登录系统的时间
		$LoginDate=date("Y-m-d H:i:s");
		$LoginSql = "UPDATE $DataIn.UserTable SET lDate='$LoginDate' WHERE 1 AND Id='$Login_Id' LIMIT 1";
		$LoginResult = mysql_query($LoginSql);
		
		$Login_cSign=7;							//5、7楼合并时使用，需去掉！！！
		//session_register("DataDir");			//原合并时，独立文件存放位置，需去掉！！！
		$_SESSION["DataDir"] = $DataDir;
		//session_register("Login_cSign");		//登录系统：原5、7楼合并时使用，需去掉！！！
		$_SESSION["Login_cSign"] = $Login_cSign;
		//session_register("Login_IP");			//用户IP
		$_SESSION["Login_IP"] = $Login_IP;
		//session_register("Login_uType");		//用户类型
		$_SESSION["Login_uType"] = $Login_uType;
		//session_register("Login_Id"); 			//用户ID
		$_SESSION["Login_Id"] = $Login_Id;
		//session_register("Login_uName"); 		//用户名称
		$_SESSION["Login_uName"] = $Login_uName;
		//session_register("Login_P_Number"); 	//用户编号
		$_SESSION["Login_P_Number"] = $Login_P_Number;
		//session_register("Login_Date");			//登录时间
		$_SESSION["Login_Date"] = $Login_Date;
		
		//登录记录
		$sTime=date("Y-m-d H:i:s");
		$loginlogSql="INSERT INTO $DataIn.loginlog (Id,uId,uType,uName,uFrom,uIP,sTime,eTime) VALUES (NULL,'$Login_Id','$Login_uType','$Name','$uFrom','$Login_IP','$sTime','0000-00-00 00:00:00')";
		$loginlogRow=@mysql_query($loginlogSql);
		//if($CardId!=10001){
			Header("Location: mainframe.php"); 
			//}
		exit();
		}
	}
else{	//如果用户表没有找到相应的用户数据
	echo "<SCRIPT LANGUAGE=JavaScript>alert('登录失败,无效卡!');";
	echo "location.href='cj_login.php'";
	echo "</script>";
	}
echo"
</body>
</html>";
?>
