<?php
	
	include "../model/modelhead.php";
	
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/ipdAPI/webClass/BxClass/StaffBxItem.php");
	
	$From=$From==""?"read":$From;
	//需处理参数
	$ColsNumber=16;				
	$tableMenuS=450;
	ChangeWtitle("$SubCompany 员工补休记录");
	$funFrom="rs_bx";
	$nowWebPage=$funFrom."_read";
	$sumCols="10";		//求和列
	$Th_Col="选项|40|序号|35|公司|30|工作</br>地点|40|部门|50|职位|70|员工Id|45|员工姓名|60|加班开始时间|125|加班结束时间|125|备注|180|凭证|50|小计|50|登记日期|70|操作员|80|审核人|80";
	$Pagination=$Pagination==""?1:$Pagination;
	$Page_Size = 200;
	$ActioToS="1,2,3,4";
	include "../model/subprogram/read_model_3.php";
	
	if($From!="slist"){
		$SearchRows ="";
		
		//月份
	 	
	$date_Result = mysql_query("SELECT DATE_FORMAT(StartDate,'%Y-%m') AS Month FROM $DataIn.bxSheet group by DATE_FORMAT(StartDate,'%Y-%m') order by StartDate DESC",$link_id);
	if ($dateRow = mysql_fetch_array($date_Result)){
	    echo"<select name='chooseMonth' id='chooseMonth' onchange='RefreshPage(\"$nowWebPage\")'>";
		do{
			$dateValue=$dateRow["Month"];
			$chooseMonth=$chooseMonth==""?$dateValue:$chooseMonth;
			if($chooseMonth==$dateValue){
				echo"<option value='$dateValue' selected>$dateValue</option>";
				
				$day_select = mysql_query("SELECT DATE_FORMAT(StartDate,'%d') AS Day FROM $DataIn.bxSheet Where DATE_FORMAT(StartDate,'%Y-%m')='$chooseMonth' group by DATE_FORMAT(StartDate,'%d') order by StartDate DESC limit 1",$link_id);
				$day = mysql_fetch_assoc($day_select);
				$reloadDay = $day["Day"];
				$SearchRows="and (DATE_FORMAT(J.StartDate,'%Y-%m-%d')='$chooseMonth-$reloadDay' or (DATE_FORMAT(J.StartDate,'%Y-%m')<'$chooseMonth'  AND DATE_FORMAT(J.EndDate,'%Y-%m')>='$chooseMonth' )  )";
				}
			else{
				echo"<option value='$dateValue'>$dateValue</option>";
				}
			}while($dateRow = mysql_fetch_array($date_Result));
		}
	 	echo"</select>&nbsp;";
	 	
	 
	 	
	
	$day_Result = mysql_query("SELECT DATE_FORMAT(StartDate,'%d') AS Day FROM $DataIn.bxSheet Where DATE_FORMAT(StartDate,'%Y-%m')='$chooseMonth' group by DATE_FORMAT(StartDate,'%d') order by StartDate DESC",$link_id);
	if ($dayRow = mysql_fetch_array($day_Result)){
	    echo"<select name='chooseday' id='chooseday' onchange='RefreshPage(\"$nowWebPage\")'>";
	    //echo"<option value=''>全部</option>";
		do{
			$dayValue=$dayRow["Day"];
			$chooseday=$chooseday==""?$dayValue:$chooseday;
			if($chooseday==$dayValue){
				echo"<option value='$dayValue' selected>$dayValue</option>";
				$SearchRows ="and (DATE_FORMAT(J.StartDate,'%Y-%m-%d')='$chooseMonth-$chooseday' or (DATE_FORMAT(J.StartDate,'%Y-%m')<'$chooseMonth'  AND DATE_FORMAT(J.EndDate,'%Y-%m')>='$chooseMonth' )  )";
				}
			else{
				echo"<option value='$dayValue'>$dayValue</option>";
				}
			}while($dayRow = mysql_fetch_array($day_Result));
		}
	 	echo"</select>&nbsp;";
	 	
		$SelectTB="M";$SelectFrom=1; 
		//选择地点
		include "../model/subselect/WorkAdd.php";  
		//选择部门
		include "../model/subselect/BranchId.php";   
	
	 }
	else
	{
		echo $CencalSstr;
	}
	
	include "../model/subprogram/read_model_5.php";
	//步骤6：需处理数据记录处理
	$i=1;
	$j=($Page-1)*$Page_Size+1;
	List_Title($Th_Col,"1",1);
	
	$bxItemOriginal = new StaffBxItem();
	
	$mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Date,J.Operator,J.Checker,J.Note, J.type,J.Number,J.hours,J.Attached
			FROM $DataIn.bxSheet J 
			LEFT JOIN $DataIn.staffmain M ON J.Number=M.Number
			WHERE M.Estate = 1 $SearchRows $Orderby";
	
	$myResult = mysql_query($mySql);
	while($myRow = mysql_fetch_assoc($myResult)){
		$m=1;
	
		$cloneBxItem = clone $bxItemOriginal;
		$cloneBxItem->setupBxItem($myRow, $DataIn, $DataIn, $link_id);
		$cloneBxItem->setStaffInfomaiton($cloneBxItem->getStaffNumber(), $DataIn, $DataIn, $link_id);
		
		
		$Attached=$myRow["Attached"];
		
		$Dir=anmaIn("download/staffbx/",$SinkOrder,$motherSTR);
		if($Attached!=""){
		
			$Attached=anmaIn($Attached,$SinkOrder,$motherSTR);
			$AttachedStr="<span onClick='OpenOrLoad(\"$Dir\",\"$Attached\",\"\",\"Limit\")'  style='CURSOR: pointer;color:#FF6633'>View</span>";
			}
		else{
			$AttachedStr="-";
			}
		
		$ValueArray=array(
			array(0=>$cloneBxItem->getStaffCompany(),		1=>"align='center'"),
			array(0=>$cloneBxItem->getStaffWorkAddress(),	1=>"align='center'"),
			array(0=>$cloneBxItem->getStaffBranchName(),		1=>"align='center'"),
			array(0=>$cloneBxItem->getStaffJobName(), 			1=>"align='center'"),
			array(0=>$cloneBxItem->getStaffNumber(), 		1=>"align='center'"),
			array(0=>$cloneBxItem->getStaffName(),			1=>"align='center'"),
			array(0=>$cloneBxItem->getStartDate(), 	1=>"align='center'"),
			array(0=>$cloneBxItem->getEndDate(), 		1=>"align='center'"),
			array(0=>$cloneBxItem->getNote(),		1=>"align='left'"),
			array(0=>$AttachedStr,		1=>"align='center'"),
			array(0=>$cloneBxItem->getHours(),		1=>"align='center'"),
			array(0=>$cloneBxItem->getLogDate(),			1=>"align='center'"),
			array(0=>$cloneBxItem->getOperator(), 	1=>"align='center'"),
			array(0=>$cloneBxItem->getChecherName(),			1=>"align='center'"),
			);
		
		$checkidValue=$cloneBxItem->getItemId(); 
		include "../model/subprogram/read_model_6.php";
	}
	
	if(mysql_num_rows($myResult) == 0)
	{
		noRowInfo($tableWidth);
	}
	
	//步骤7：
echo '</div>';
	$myResult = mysql_query($mySql,$link_id);
	if ($myResult ) $RecordToTal= mysql_num_rows($myResult);
	pBottom($RecordToTal,$i-1,$Page,$Pagination,$Page_Size,$timer,$Login_WebStyle,$tableWidth);
	include "../model/subprogram/read_model_menu.php";
	
?>