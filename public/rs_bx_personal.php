<?php
	
	include "../model/modelhead.php";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/webClass/BxClass/StaffBxItem.php");
	
	$ColsNumber=12;
	$tableMenuS=500;
	$sumCols="3";		//求和列
	$From=$From==""?"personal":$From;
	ChangeWtitle("$SubCompany 我的补休登记记录");
	$funFrom="rs_bx";
	$Th_Col="选项|40|序号|35|部门|50|职位|50|员工Id|45|员工姓名|60|申请开始时间|125|申请结束时间|125|补休工时|50|补休原因|120|审核状态|60|登记时间|70|操作人|80|审核|60|退回原因|150";
	
	$Pagination=$Pagination==""?1:$Pagination;	//默认分页方式:1分页，0不分页
	$Page_Size = 100;							//每页默认记录数量
	$ActioToS="1,2,3,4";
	
	//步骤3：
	$nowWebPage=$funFrom."_personal";
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
	$bxItemOriginal = new StaffBxItem();
	$bxItemOriginal->setStaffInfomaiton($Login_P_Number, $DataIn, $DataPublic, $link_id);
	
	$mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Date,J.Operator,J.Note, J.type,J.Number,J.hours,J.Estate
			FROM $DataPublic.bxSheet J 
			LEFT JOIN $DataPublic.staffmain M ON J.Number=M.Number
			WHERE J.Number = '".$bxItemOriginal->getStaffNumber()."' and M.Estate = 1 $SearchRows $Orderby";
	
	$myResult = mysql_query($mySql." $PageSTR",$link_id);
	while($myRow = mysql_fetch_array($myResult))
	{
		$m=1;
		$Id = $myRow["Id"];
		$cloneBxItem = clone $bxItemOriginal;
		$cloneBxItem->setupBxItem($myRow, $DataIn, $DataPublic, $link_id);
		
		$ValueArray=array(
			array(0=>$cloneBxItem->getStaffBranchName(),		1=>"align='center'"),
			array(0=>$cloneBxItem->getStaffJobName(), 			1=>"align='center'"),
			array(0=>$cloneBxItem->getStaffNumber(), 		1=>"align='center'"),
			array(0=>$cloneBxItem->getStaffName(),			1=>"align='center'"),
			array(0=>$cloneBxItem->getStartDate(), 	1=>"align='center'"),
			array(0=>$cloneBxItem->getEndDate(), 		1=>"align='center'"),
			array(0=>$cloneBxItem->getHours(), 		1=>"align='center'"),
			array(0=>$cloneBxItem->getNote(), 		1=>"align='center'"),
			array(0=>$cloneBxItem->getStateName(), 		1=>"align='center'"),
			array(0=>$cloneBxItem->getLogDate(),			1=>"align='center'"),
			array(0=>$cloneBxItem->getOperator(), 	1=>"align='center'"),
			array(0=>$cloneBxItem->getChecherName(), 	1=>"align='center'"),
			array(0=>$cloneBxItem->getBackReason(),			1=>"align='center'"),

			);
		$checkidValue=$Id;
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