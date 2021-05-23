<?php
	
	include "../model/modelhead.php";
	include "../model/kq_YearHolday.php";
	
	$ColsNumber=12;
	$tableMenuS=500;
	$sumCols="3";		//求和列
	$From=$From==""?"read":$From;
	ChangeWtitle("$SubCompany 我的补休登记记录");
	$funFrom="office_bx";
	$Th_Col="选项|40|序号|35|部门|50|职位|50|员工Id|45|员工姓名|60|申请开始时间|125|申请结束时间|125|补休工时|50|补休原因|120|审核状态|60|登记时间|70|操作人|80|审核|60|退回原因|100";
	
	$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
	$Page_Size = 100;							//每页默认记录数量
	$ActioToS="1,2,3,4";	
	
	//步骤3：
	$nowWebPage=$funFrom."_read";
	include "../model/subprogram/read_model_3.php";
	//步骤4：需处理-条件选项
	if($From!="slist"){
		//划分权限:如果没有最高权限，则只显示自己的记录
		$SearchRows="";
		$TempEstateSTR="EstateSTR".strval($Estate); 
		$TempEstateSTR="selected";	
		$SearchRows.=$Estate==""?"":" and J.Estate=$Estate";
		//记录状态
		echo"<select name='Estate' id='Estate' onchange='ResetPage(this.name)'>
		<option value='' >全  部</option>
		<option value='1' 1>申请中</option>
		<option value='0' 0>申请通过</option>
		</select>&nbsp;";
	}
	echo"<select name='Pagination' id='Pagination' onchange='CencelPage()'><option value='1' $Pagination1>分页</option><option value='0' $Pagination0>不分页</option></select>
  	$CencalSstr";
  	//步骤5：
  	include "../model/subprogram/read_model_5.php";
  	//步骤6：需处理数据记录处理
  	$i=1;
  	$j=($Page-1)*$Page_Size+1;
	List_Title($Th_Col,"1",0);

	$mySql="SELECT J.Id,J.StartDate,J.EndDate, J.Note,J.Date,J.Estate,J.Operator,J.Reason,J.Estate,M.Number,M.Name,M.KqSign,M.JobId,M.BranchId,J.Checker,J.type
			FROM $DataPublic.bxsheet J 
			LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number 
			LEFT JOIN $DataPublic.qjtype T ON J.Type=T.Id
			WHERE 1 AND J.Number=$Login_P_Number $SearchRows order by J.StartDate DESC";
	
	$bxResult = mysql_query($mySql);
	while($myRow = mysql_fetch_assoc($bxResult))
	{
		$m=1;
		$Id=$myRow["Id"];
		$StartDate=$myRow["StartDate"];
		$EndDate=$myRow["EndDate"];
		$MonthTemp=substr($EndDate,0,7);
		$Number=$myRow["Number"];
		$Name=$myRow["Name"];
		$KqSign=$myRow["KqSign"];
		$BranchId=$myRow["BranchId"];				
		$B_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.branchdata where 1 and Id=$BranchId LIMIT 1",$link_id));
		$Branch=$B_Result["Name"];				
		$JobId=$myRow["JobId"];
		$J_Result = mysql_fetch_array(mysql_query("SELECT Name FROM $DataPublic.jobdata where 1 and Id=$JobId LIMIT 1",$link_id));
		$Job=$J_Result["Name"];
		$Date=substr($myRow["Date"],0,10);	
		$note = $myRow["Note"];
		$Estate = $myRow["Estate"];	
		$backReason = $myRow["Reason"];
		$calculateType = $myRow["type"];
		switch($Estate)
		{
			case "1":
			case "0":
			{
				$LockRemark = ($Estate == "1")?"申请中":"通过";
				$Estate = ($Estate == "1")?"<div class='yellowB'>申请中</div>":"<div class='greenB'>通过</div>";
			}
			break;
			case "2":
			{
				$Estate = "<div class='redB'>退回</div>";
				$LockRemark = "";
			}
		}
		
		$bxHours = ($calculateType == "0")?calculateHours($StartDate, $EndDate):GetBetweenDateDays($Number,$StartDate,$EndDate,"1",$DataIn,$DataPublic,$link_id);
		
		$checker = $myRow["Checker"];
		if($checker != "")
		{
			$checkerResult = mysql_query("Select Name From $DataPublic.staffmain where Number = '$checker'");
			$checkerRow = mysql_fetch_assoc($checkerResult);
			$checker = $checkerRow["Name"];
		}
		
		$Operator=$myRow["Operator"];
		if($Operator != "")
		{
			$operatorResult = mysql_query("Select Name From $DataPublic.staffmain where Number = '$Operator'");
			$operatorRow = mysql_fetch_assoc($operatorResult);
			$Operator = $operatorRow["Name"];
		}
		
		$ValueArray=array(
			array(0=>$Branch,		1=>"align='center'"),
			array(0=>$Job, 			1=>"align='center'"),
			array(0=>$Number, 		1=>"align='center'"),
			array(0=>$Name,			1=>"align='center'"),
			array(0=>$StartDate, 	1=>"align='center'"),
			array(0=>$EndDate, 		1=>"align='center'"),
			array(0=>$bxHours, 		1=>"align='center'"),
			array(0=>$note, 		1=>"align='center'"),
			array(0=>$Estate, 		1=>"align='center'"),
			array(0=>$Date,			1=>"align='center'"),
			array(0=>$Operator, 	1=>"align='center'"),
			array(0=>$checker, 	1=>"align='center'"),
			array(0=>$backReason,			1=>"align='center'"),

			);
		$checkidValue=$Id;
		include "../model/subprogram/read_model_6.php";

	}
	
	//步骤7：
echo '</div>';
	$myResult = mysql_query($mySql,$link_id);
	$RecordToTal= mysql_num_rows($myResult);
	pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
	include "../model/subprogram/read_model_menu.php";
	
?>