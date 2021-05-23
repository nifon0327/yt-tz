<?php   
//代码共享-EWEN 2012-08-20 登录集合页index.php-登录目标系统判断checklogin.php－本文件，登录信息处理
session_start();
include "../basic/parameter.inc";			//数据库连接
include "../model/modelfunction.php";		//读入函数

$Login_IP=$_POST['IP'];
$Login_IPAdd=$_POST['IPAdd']==""?"":$_POST['IPAdd'];
if ($Login_IP=="" || preg_match('/^192\.168/', $_SERVER['HTTP_HOST'])){
	$Local_IP=GetIP();   
	$Local_IP3=substr($Local_IP,0,3);  //取192开头的则是本地的IP
	if ($Local_IP3=="192") {
		$Login_IP=$Local_IP;	
	}
	else {
		$Login_IP=$_POST['IP'];
	}
}

/*
$Local_IP=GetIP();   
$Local_IP3=substr($Local_IP,0,3);  //取192开头的则是本地的IP
if ($Local_IP3=="192") {
	$Login_IP=$Local_IP;	
}
else {
	$Login_IP=$_POST['IP'];
}
*/

/*
$fp = fopen("login.log", "a");
fwrite($fp, date("Y-m-d H:i:s") . "    Login_IP=" . $Login_IP .  "  Local_IP=". $Local_IP . "\r\n");
fclose($fp); 
*/

if($Login_IP!=""){
	$Name=$_POST['Name'];
	$Password=$_POST['Password'];
	}
else{
	$Name=$_GET['Name'];
	$Password=$_GET['Password'];
	//$Login_IP=GetIP();
	}

$MDPassword=MD5($Password);
$mySql="SELECT * FROM $DataIn.UserTable WHERE uName='$Name' AND uPwd='$MDPassword'";
//目标站点登录信息处理
$myResult = mysql_query($mySql." $PageSTR",$link_id) or die ("数据连接错误!");
if($myRow = mysql_fetch_array($myResult)){
		//网页风格读取
		$WebStyle=$myRow["WebStyle"]=5;
		$webSql=mysql_query("SELECT Dir FROM $DataPublic.webstyle WHERE 1 AND Id='$WebStyle' AND Estate='1' ORDER BY Id LIMIT 1",$link_id);
		if($webRow = mysql_fetch_array($webSql)){
			$Login_WebStyle=$webRow["Dir"];
			}
		else{//如果没有设定则使用默认风格
			$Login_WebStyle="default";			
			}
		//用户类型:1-内部员工，2-客户，3-供应商，4-外部员工，5-参观者
		$Login_uType=$myRow["uType"];				
		$Login_Id=$myRow["Id"];					//用户ID
		$Login_uName=$myRow["uName"];			//用户姓名
		$Login_P_Number=$myRow["Number"];		//用户编号
		//读取该用户最后一次离开时间
		$checkLastTime=mysql_fetch_array(mysql_query("SELECT eTime FROM  $DataIn.loginlog WHERE uId=$Login_Id ORDER BY eTime DESC LIMIT 1",$link_id));
		$Login_LastTime=substr($checkLastTime["eTime"],5,-3);//登录时间
		$checkResulst = mysql_query("SELECT cSign,EShortName,CShortName,OutIp FROM  $DataPublic.companys_group WHERE Db='$DataIn' LIMIT 1");
		$checkMicd=mysql_fetch_array($checkResulst);
		$EShortName=$checkMicd["EShortName"];
		$CShortName=$checkMicd["CShortName"];
		$SubCompany=$Login_uType==2?"$EShortName - ":"$CShortName - ";			//窗口标题前置字符
		$OutIp=$checkMicd["OutIp"];
		$Login_cSign=$checkMicd["cSign"];
		$Login_Dir="system".$Login_cSign;//系统独享目录
		$_SESSION["Login_cSign"] = $Login_cSign;//系统标识:7ＭＣ，3ＰＴ
		$_SESSION["Login_Dir"] = $Login_Dir;
		//用户IP检测
		$uFrom=1;			//登录来自于内部IP
		
		if(preg_match('/^192\.168/',$Login_IP)){//IP地址为192开头则为内部登录
			$uFrom=1;
			}
		else{
			if($Login_IP==$OutIp){				//如果IP地址与公司域名一致，则为内部域名登录
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
		//Session参数
		//用户IP
		$_SESSION["Login_IP"] = $Login_IP;
		//用户类型
		$_SESSION["Login_uType"] = $Login_uType;
		//窗口标题前置字符
		$_SESSION["SubCompany"] = $SubCompany;
		//用户ID
		$_SESSION["Login_Id"] = $Login_Id;
		//用户名称
		$_SESSION["Login_uName"] = $Login_uName;
		//用户编号
		$_SESSION["Login_P_Number"] = $Login_P_Number;
		//网页风格
		$_SESSION["Login_WebStyle"] = $Login_WebStyle;
		//上次离线时间
		$_SESSION["Login_LastTime"] = $Login_LastTime;
		
		switch($Login_uType){
			case 2://客户
				//读取客户ID
				$checkClient=mysql_fetch_array(mysql_query("SELECT CompanyId FROM $DataIn.linkmandata WHERE 1 AND Id='$Login_P_Number'  ORDER BY Id LIMIT 1",$link_id));//AND Type='$Login_uType'
				$myCompanyId=$checkClient["CompanyId"];
				$_SESSION["myCompanyId"] = $myCompanyId;
				//在线登记		
				$LastTime=time();
				$onlineIN="INSERT INTO $DataIn.online (sId,uId,uFrom,IP,LastTime) VALUES (NULL,'$Login_Id','$uFrom','$Login_IP','$LastTime')";
				$onlineRes=@mysql_query($onlineIN);
				//登录记录存档
				$sTime=date("Y-m-d H:i:s");
				$loginlogSql="INSERT INTO $DataIn.loginlog (Id,uId,uType,uName,uFrom,uIP,sTime,eTime) VALUES (NULL,'$Login_Id','$Login_uType','$Name','$uFrom','$Login_IP','$sTime','0000-00-00 00:00:00')";
				$loginlogRow=@mysql_query($loginlogSql);
				//注册sId
				$regOL=mysql_query("SELECT sId FROM $DataIn.online WHERE 1 AND uId='$Login_Id' ORDER BY sId DESC LIMIT 1",$link_id);
				if($regOLRow = mysql_fetch_array($regOL)){
					$onlineId=$regOLRow["sId"];
					$_SESSION["onlineId"] = $onlineId;
					}			
				Header("Location: ../client/"); //转向目标文件//clientdata.php
				exit();
			break;
			case 3://供应商登录
				$checkClient=mysql_fetch_array(mysql_query("SELECT CompanyId FROM $DataIn.linkmandata WHERE 1 AND Id='$Login_P_Number'  ORDER BY Id LIMIT 1",$link_id));//AND Type='$Login_uType'
				$myCompanyId=$checkClient["CompanyId"];
					$_SESSION["myCompanyId"] = $myCompanyId;
					//在线登记		
					$LastTime=time();
					$onlineIN="INSERT INTO $DataIn.online (sId,uId,uFrom,IP,LastTime) VALUES (NULL,'$Login_Id','$uFrom','$Login_IP','$LastTime')";
					$onlineRes=@mysql_query($onlineIN);
					//登录记录存档
					$sTime=date("Y-m-d H:i:s");
					$loginlogSql="INSERT INTO $DataIn.loginlog (Id,uId,uType,uName,uFrom,uIP,sTime,eTime) VALUES (NULL,'$Login_Id','$Login_uType','$Name','$uFrom','$Login_IP','$sTime','0000-00-00 00:00:00')";
					$loginlogRow=@mysql_query($loginlogSql);
					//注册sId
					$regOL=mysql_query("SELECT sId FROM $DataIn.online WHERE 1 AND uId='$Login_Id' ORDER BY sId DESC LIMIT 1",$link_id);
					if($regOLRow = mysql_fetch_array($regOL)){
						$onlineId=$regOLRow["sId"];
						$_SESSION["onlineId"] = $onlineId;
						}
				Header("Location: ../supplier/");//转向目标文件supplierdata.php
				exit();		
			break;
			case 5://参观人员
			break;
			default://内部员工、外部人员
				//判断是否已经登录：是，则踢出前次登录，否，则继续
				$pResult =  mysql_fetch_array(mysql_query("SELECT S.ExtNo,S.BranchId,S.JobId,S.GroupId FROM $DataPublic.staffmain S WHERE S.Number=$Login_P_Number ORDER BY S.Id LIMIT 1",$link_id));
				$Login_ExtNo=$pResult["ExtNo"];//获取分机号码
				$_SESSION["Login_BranchId"] =$pResult["BranchId"];  //取得部门Id
				$_SESSION["Login_JobId"] =$pResult["JobId"]; //取得职位Id
				$_SESSION["Login_GroupId"] =$pResult["GroupId"];//取得工作小组编号
				
				if($Login_ExtNo!=""){
					$_SESSION["Login_ExtNo"] = $Login_ExtNo;
					}
				//该用户是否在线
				$checkOL=mysql_query("SELECT IP FROM $DataIn.online WHERE 1 AND uId='$Login_Id' ORDER BY sId DESC LIMIT 1",$link_id);
				$loginYN=0;
				if($checkOLRow = mysql_fetch_array($checkOL)){
					//如果已经登录，则踢出前次登录
					$Del = "DELETE FROM $DataIn.online WHERE 1 AND uId='$Login_Id' LIMIT 1"; 
					$DelResult = mysql_query($Del);
					}
				//重新在线登记		
				$LastTime=time();//最后一次登录时间
				$onlineIN="INSERT INTO $DataIn.online (sId,uId,uFrom,IP,LastTime) VALUES (NULL,'$Login_Id','$uFrom','$Login_IP','$LastTime')";
				$onlineRes=@mysql_query($onlineIN);
				//登录记录存档
				$sTime=date("Y-m-d H:i:s");
				$loginlogSql="INSERT INTO $DataIn.loginlog (Id,uId,uType,uName,uFrom,uIP,sTime,eTime) VALUES (NULL,'$Login_Id','$Login_uType','$Name','$uFrom','$Login_IP','$sTime','0000-00-00 00:00:00')";
				$loginlogRow=@mysql_query($loginlogSql);
			
				//注册sId
				$regOL=mysql_query("SELECT sId FROM $DataIn.online WHERE 1 AND uId='$Login_Id' ORDER BY sId DESC LIMIT 1",$link_id);
				if($regOLRow = mysql_fetch_array($regOL)){
					$onlineId=$regOLRow["sId"];
					$_SESSION["onlineId"] = $onlineId;
					}
				Header("Location: ../desk/mc.php"); //转向目标文件
				exit();
			break;
			}//enfd switch($Login_uType)
		}
else {
	Header("Location: ../index.php"); //转向目标文件
	exit();	
}		
?> 