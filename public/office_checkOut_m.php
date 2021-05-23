<?php
	
	include "../model/modelhead.php";
	//步骤2：需处理
	$ColsNumber=12;
	$tableMenuS=600;
	$sumCols="4";		//求和列
	$From=$From==""?"m":$From;
	ChangeWtitle("$SubCompany 加班超时审核列表");
	$funFrom="office_checkOut";
	$Th_Col="选项|40|序号|35|部门|50|职位|50|员工Id|45|员工姓名|60|签退始时间|125|超时原因|200|退回原因|200|审核状态|60|审核|80";
	
	//必选，分页默认值
	$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
	$Page_Size = 100;							//每页默认记录数量
	$ActioToS="1,164";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审核通过

	$i=1;
	$j=1;
	$nowWebPage=$funFrom."_m";
	include "../model/subprogram/read_model_3.php";
	include "../model/subprogram/read_model_5.php";
	List_Title($Th_Col,"1",0);
	
	$mySql="Select A.Id,A.Number,A.BranchId,A.JobId,A.CheckTime,A.Estate,B.Name as BranchName,C.Name as JobName,D.Name as StaffName
			From $DataIn.checkinout A
			Left Join $DataPublic.branchdata B On B.Id = A.BranchId
			Left Join $DataPublic.jobdata C On C.Id = A.JobId
			Left Join $DataPublic.staffmain D On D.Number = A.Number
			Where A.Estate = '2' and A.CheckType = 'O'";
	
	$myResult = mysql_query($mySql);
	while($myRow = mysql_fetch_assoc($myResult))
	{
		$m=1;
		$id = $myRow["Id"];
		$number = $myRow["Number"];
		$branchId = $myRow["BranchId"];
		$jobId = $myRow["JobId"];
		$checkTime = $myRow["CheckTime"];
		$branchName = $myRow["BranchName"];
		$jobName = $myRow["JobName"];
		$name = $myRow["StaffName"];
		$eState = $myRow["Estate"];
		
		switch($eState)
		{
			case "2":
			{
				$eState = "审核中";
			}
			break;
			case "3":
			{
				$eState = "退回";
			}
			break;
		}
		
		$ValueArray=array(
			array(0=>$branchName,		1=>"align='center'"),
			array(0=>$jobName, 			1=>"align='center'"),
			array(0=>$number, 		1=>"align='center'"),
			array(0=>$name,			1=>"align='center'"),
			array(0=>$checkTime, 	1=>"align='center'"),
			array(0=>"&nbsp;", 		1=>"align='center'"),
			array(0=>"&nbsp;", 		1=>"align='center'"),
			array(0=>$eState, 		1=>"align='center'"),
			array(0=>"&nbsp;", 		1=>"align='center'")
			);
		$checkidValue=$id;
		include "../model/subprogram/read_model_6.php";
		
	}
	
	if(mysql_num_rows($myResult) == 0)
	{
		noRowInfo($tableWidth);
	}
	
	//步骤7：
echo '</div>';
	$myResult = mysql_query($mySql,$link_id);
	$RecordToTal= mysql_num_rows($myResult);
	pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
	include "../model/subprogram/read_model_menu.php";	
?>
