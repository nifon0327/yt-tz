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
$Password=MD5($Password);
$mySql="SELECT U.uType,U.Id,U.uName,U.Number,U.Estate,M.Name,M.GroupId,G.GroupName,G.TypeId
FROM $DataIn.UserTable U 
LEFT JOIN $DataPublic.staffmain M ON M.Number=U.Number
LEFT JOIN $DataIn.staffgroup G ON G.GroupId=M.GroupId 
WHERE 1 AND U.uName='$UserName' AND U.uPwd='$Password'  ORDER BY U.Id LIMIT 1";//AND U.uType=1
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
		$Login_uName=$myRow["uName"];			//用户帐号
		$Login_P_Number=$myRow["Number"];		//用户编号
		$Login_Name=$myRow["Name"];				//用户姓名
		$Login_TypeId=$myRow["TypeId"];			//小组操作配件的分类
		$Login_GroupId=$myRow["GroupId"];		//小组ID
		$Login_GroupName=$myRow["GroupName"];	//小组名称
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
		//session_register("Login_cSign");		//登录系统：原5、7楼合并时使用，需去掉！！！\
		$_SESSION["Login_cSign"] = $Login_cSign;
		//session_register("Login_IP");			//用户IP
		$_SESSION["Login_IP"] = $Login_IP;
		//session_register("Login_uType");		//用户类型
		$_SESSION["Login_uType"] = $Login_uType;
		//session_register("Login_Id"); 			//用户ID
		$_SESSION["Login_Id"] = $Login_Id;
		//session_register("Login_uName"); 		//用户帐号
		$_SESSION["Login_uName"] = $Login_uName;
		//session_register("Login_P_Number"); 	//用户编号
		$_SESSION["Login_P_Number"] = $Login_P_Number;
		//session_register("Login_Date");			//登录时间
		$_SESSION["Login_Date"] = $Login_Date;
		//session_register("Login_Name");		//用户姓名
		$_SESSION["Login_Name"] = $Login_Name;
		//session_register("Login_TypeId");	//小组操作分类ID
		$_SESSION["Login_TypeId"] = $Login_TypeId;
		//session_register("Login_GroupId");	//小组ID
		$_SESSION["Login_GroupId"] = $Login_GroupId;
		//session_register("Login_GroupName");	//小组名称
		$_SESSION["Login_GroupName"] = $Login_GroupName;
				
		//在线登记
		$LastTime=time();
		$onlineIN="INSERT INTO $DataIn.online (sId,uId,uFrom,IP,LastTime) VALUES (NULL,'$Login_Id','$uFrom','$Login_IP','$LastTime')";
		$onlineRes=@mysql_query($onlineIN);
		
		//登录记录
		$sTime=date("Y-m-d H:i:s");
		$loginlogSql="INSERT INTO $DataIn.loginlog (Id,uId,uType,uName,uFrom,uIP,sTime,eTime) VALUES (NULL,'$Login_Id','$Login_uType','$Login_uName','$uFrom','$Login_IP','$sTime','0000-00-00 00:00:00')";
		$loginlogRow=@mysql_query($loginlogSql);
		
		//注册在线sId
		$regOL=mysql_query("SELECT sId FROM $DataIn.online WHERE 1 AND uId='$Login_Id' ORDER BY sId DESC LIMIT 1",$link_id);
		if($regOLRow = mysql_fetch_array($regOL)){
			$onlineId=$regOLRow["sId"];
			//session_register("onlineId");
			$_SESSION["onlineId"] = $onlineId;
			}
		//if($CardId!=10001){
			Header("Location: mainFrame.php");
			//}
		exit();
		}
	}
else{	//如果用户表没有找到相应的用户数据
	echo "<SCRIPT LANGUAGE=JavaScript>alert('登录失败,帐号或密码错误!');";
	echo "location.href='/cjgl'";
	echo "</script>";
	}
echo"
</body>
</html>";
?>
