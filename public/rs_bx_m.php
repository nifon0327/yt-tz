<?php
	include "../model/modelhead.php";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/webClass/BxClass/StaffBxItem.php");
	//步骤2：需处理
	$ColsNumber=12;
	$tableMenuS=600;
	$sumCols="4";		//求和列
	$From=$From==""?"m":$From;
	ChangeWtitle("$SubCompany 部门员工待审核列表");
	$funFrom="office_bx";
	$Th_Col="选项|40|序号|35|部门|50|职位|50|员工Id|45|员工姓名|60|补休开始时间|125|补休结束时间|125|补休工时|60|补休原因|120|登记日期|70|操作人|80|审核|80|审核状态|80";

	//必选，分页默认值
	$Pagination=$Pagination==""?0:$Pagination;	//默认分页方式:1分页，0不分页
	$Page_Size = 100;							//每页默认记录数量
	$ActioToS="1,17,15";				//功能:0查询,1新增,2更新,3删除,4可用,5禁用,6锁定,7解锁,8全选,9反选,10列印,11统计,12其它,13请款,14退回,15取消,16审	核通过
	//步骤3：
	$nowWebPage="rs_bx_m";
	include "../model/subprogram/read_model_3.php";
	if($From!="slist"){$SearchRows="";}

	echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select> $CencalSstr";
	//步骤4：
	$TitlePre="<br>&nbsp;&nbsp;退回原因&nbsp;<input type=\"text\" id=\"ReturnReasons\" name=\"ReturnReasons\" style=\"width:600\"><p>";
	include "../model/subprogram/read_model_5.php";
	//步骤5：需处理数据记录处理
	$i=1;
	$j=($Page-1)*$Page_Size+1;
	List_Title($Th_Col,"1",0);
	//取得当前用户的部门ID

	if($Login_P_Number=='10082' || $Login_P_Number == "11008"  || $Login_P_Number == "10006")
	{
		$BranchIdSTR="";
	}
	else 
	{
		$bResult = mysql_query("SELECT BranchId FROM $DataPublic.staffmain WHERE Number='$Login_P_Number' ",$link_id);
		while($bRow = mysql_fetch_array($bResult)){
		$BranchIdSTR=$BranchIdSTR==""?$bRow["BranchId"]:"," .$bRow["BranchId"];
		}
		$BranchIdSTR="AND M.BranchId IN ($BranchIdSTR)"; 
	}
	
	$bxItemOriginal = new StaffBxItem();
	$mySql="SELECT J.Id,J.Number,J.StartDate,J.EndDate,J.Note,J.Reason,J.Date,J.Checker,J.Estate,J.type,J.Operator,J.hours
			FROM $DataPublic.bxsheet J 
			LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number 
			WHERE 1  $BranchIdSTR  AND   J.Estate=1 order by J.StartDate DESC";
	$myResult = mysql_query($mySql." $PageSTR",$link_id);
	while($myRow = mysql_fetch_array($myResult))
	{
		$m=1;
		$cloneBxItem = clone $bxItemOriginal;
		$cloneBxItem->setupBxItem($myRow, $DataIn, $DataPublic, $link_id);
		$cloneBxItem->setStaffInfomaiton($cloneBxItem->getStaffNumber(), $DataIn, $DataPublic, $link_id);
		$Id = $myRow["Id"];
		$ValueArray=array(
			array(0=>$cloneBxItem->getStaffBranchName(),		1=>"align='center'"),
			array(0=>$cloneBxItem->getStaffJobName(), 			1=>"align='center'"),
			array(0=>$cloneBxItem->getStaffNumber(), 		1=>"align='center'"),
			array(0=>$cloneBxItem->getStaffName(),			1=>"align='center'"),
			array(0=>$cloneBxItem->getStartDate(), 	1=>"align='center'"),
			array(0=>$cloneBxItem->getEndDate(), 		1=>"align='center'"),
			array(0=>$cloneBxItem->getHours(), 	1=>"align='center'"),
			array(0=>$cloneBxItem->getNote(), 		1=>"align='center'"),
			array(0=>$cloneBxItem->getLogDate(), 		1=>"align='center'"),
			array(0=>$cloneBxItem->getOperator(),			1=>"align='center'"),
			array(0=>$cloneBxItem->getChecherName(),			1=>"align='center'"),
			array(0=>$cloneBxItem->getStateName(), 	1=>"align='center'")
			);
		$LockRemark = "";
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";
	}
	
	if(mysql_num_rows($myResult) == 0)
	{
		noRowInfo($tableWidth);
	}
	List_Title($Th_Col,"0",1);
	$myResult = mysql_query($mySql,$link_id);
	$RecordToTal= mysql_num_rows($myResult);
	pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
	include "../model/subprogram/read_model_menu.php";
		
?>