<?php 
	
	include "../../basic/parameter.inc";
	
	$userName = $_POST["userName"];
	$userName = "joseph";
	$Password = $_POST["password"];
	$Password = "vulcan";
	
	$Password=MD5($Password);
	$mySql="SELECT U.uType,U.Id,U.uName,U.Number,U.Estate
	FROM $DataIn.UserTable U 
	WHERE 1 AND U.uName='$userName' AND U.uPwd='$Password' ORDER BY U.Id LIMIT 1";
	
	$myResult = mysql_query($mySql." $PageSTR",$link_id) or die ("数据连接错误!");
	$info = "";
	if($myRow = mysql_fetch_array($myResult))
	{
		$Estate=$myRow["Estate"];
		if($Estate==0)
		{//禁用状态
			$info = "登录失败，该帐号目前禁用!";			
		}
		else
		{
			//用户类型:0-内部员工，1-客户，3-供应商，4-外部员工，5-参观者
			$uType=$myRow["uType"];
			$Id=$myRow["Id"];					//用户ID
			$uName=$myRow["uName"];			//用户帐号
			$Number=$myRow["Number"];		//用户编号
			//按内部员工或外部员工分别提取姓名等资料
			if($Login_uType==0)
			{//内部员工
				$checkStaff=mysql_fetch_array(mysql_query("SELECT M.Name,M.GroupId,G.GroupName,G.TypeId FROM $DataPublic.staffmain M LEFT JOIN $DataIn.staffgroup G ON M.GroupId=G.GroupId WHERE M.Number='$Number' LIMIT 1",$link_id));
				$Name=$checkStaff["Name"];				//用户姓名
				$TypeId=$checkStaff["TypeId"];			//小组操作配件的分类
				$GroupId=$checkStaff["GroupId"];		//小组ID
				$GroupName=$checkStaff["GroupName"];	//小组名称
			}
			else
			{
				$checkStaff=mysql_fetch_array(mysql_query("SELECT M.Name FROM $DataIn.ot_staff M WHERE M.Number='$Number' LIMIT 1",$link_id));
				$Name=$checkStaff["Name"];				//用户姓名
				$GroupName="外部人员";
			}
		}
		
		include "Get_Item.php";
		$Item_info["其他功能"][] = "离职"."|"."staffOut";

	}
	else
	{
		$info = "登录失败,帐号或密码错误!";
	}
	
	echo json_encode(array($info,$Number,$Item_info));
	
?>