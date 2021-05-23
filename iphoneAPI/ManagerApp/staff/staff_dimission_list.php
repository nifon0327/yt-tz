<?php 
//员工离职信息列表  
$SearchRows1=$mModuleId==203?" AND M.cSign=3 AND M.OffStaffSign=0 ":" AND M.OffStaffSign=0 ";
$State=5; $Count3=0;

//modify by cabbage 20141211 加上搜尋功能，搜尋功能是跨月的，這時候不需要加上月份的篩號
if (strlen($checkMonth) > 0) {
	$SearchRows1.=" AND DATE_FORMAT(D.outDate,'%Y-%m')='$checkMonth' ";
}

//add by cabbage 20141211 加上搜尋條件，搜尋條件：名字,电话号,上班地点
$SearchCondition = "";
if (strlen($searchCondition) > 0) {
	$SearchCondition .= " AND (S.Mobile LIKE '%$searchCondition%'";
	$SearchCondition .= " OR S.Dh LIKE '%$searchCondition%'";
	$SearchCondition .= " OR M.Name LIKE '%$searchCondition%')";
}

$mySql="SELECT D.outDate,D.Reason,DATE_FORMAT(D.outDate,'%Y-%m') AS outMonth, D.Type,DT.Name AS TypeName,M.Number,M.Name,M.ComeIn,
M.ContractSDate,M.ContractEDate,M.Estate,M.cSign,M.BranchId,M.GroupId,A.Name AS WorkAdd,S.Mobile,S.Dh,S.Photo,S.Tel,B.Name AS Branch,J.Name AS Job 
			  FROM $DataPublic.dimissiondata D  
			  LEFT JOIN $DataPublic.dimissiontype DT ON DT.Id=D.Type 
			  LEFT JOIN  $DataPublic.staffmain M ON M.Number=D.Number 
			 LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
			 LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
			 LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
			 LEFT JOIN $DataPublic.staffworkadd A ON A.Id=M.WorkAdd 
			 WHERE  M.Estate=0 $SearchRows1 $SearchCondition ORDER BY  outDate DESC ";
/*  echo $mySql;  */
 $Result = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($Result)) {
   do{
         $Number=$myRow["Number"];
         $Name = $myRow["Name"];
		 $Job=$myRow["Job"];
         $Photo=$myRow["Photo"];
		//入职日期
		 $ComeIn=$myRow["ComeIn"];
		 
		 $outMonth=$myRow["outMonth"];
		 $outDate=$myRow["outDate"];
		 //工作时间计算
		 $MonthNums=getDifferMonthNum($outDate,$ComeIn);
		 $ComeYears=floor($MonthNums/12);
		 $ComeMonths=$MonthNums-$ComeYears*12;
         $glPhone=$ComeYears . "|" . $ComeMonths;
         
		 $Mobile=$myRow["Mobile"]==""?"":$myRow["Mobile"];
	     $Dh=$myRow["Dh"]==""?"":$myRow["Dh"];
		 $Mail = $myRow["Mail"] == ""?"":strtolower($myRow["Mail"]);

         $WorkAdd=$myRow["WorkAdd"];
                              
		 $Remark=$myRow["TypeName"] . " " .  $myRow["Reason"];
		 
		 $BranchId=$myRow["BranchId"];
		 $GroupId=$myRow["GroupId"];
		 if ($BranchId==8){
			$DataLink=$myRow["cSign"]==7?$DataIn:$DataOut;
			$checkGroupName=mysql_fetch_array(mysql_query("SELECT GroupName FROM $DataLink.staffgroup WHERE  GroupId='$GroupId' ",$link_id));
			$Job=$checkGroupName["GroupName"];
		}
		
		 //离职补助
		$DataLink=$myRow["cSign"]==7?$DataIn:$DataOut;
		$TotalRate="0%";$Amount="0";
		$checkSubsidySql=mysql_query("SELECT TotalRate,(AveAmount*TotalRate) AS Amount FROM $DataLink.staff_outsubsidysheet WHERE Number='$Number' Order by Id LIMIT 1",$link_id);
	   if($SubsidyRow = mysql_fetch_array($checkSubsidySql)){
	       $TotalRate=$SubsidyRow["TotalRate"]*100 . "%";
	       $Amount=number_format($SubsidyRow["Amount"]);
	   }
	   
	   //add by cabbage 20141125 加上判斷離職的類型，辭退(4)、開除(5)、試用(7)是被辭退的類型，其餘為正常離職
	   $leaveType = $myRow["Type"];
	   $isNormalLeave = 1;
	   if (($leaveType == 4) || ($leaveType == 5) || ($leaveType == 7)) {
		   $isNormalLeave = 0;
	   }
	   
	   //在职表现
	  $checkPerformanceSql=mysql_query("SELECT Id FROM $DataPublic.staff_performance WHERE Number='$Number'  LIMIT 1",$link_id);
	  $NoteSign=mysql_num_rows($checkPerformanceSql)>0?1:0;

		 $jsondata[]=array("Tag"=>"data", "Hidden"=>"$hidden","Id"=>"$outMonth",
										 "Number"=>"$Number",
		                                "Name"=>"$Name",
		                                "Job"=>"$Job",
		                                "ComeIn"=>"$ComeIn",
		                                "OutDate"=>"$outDate",
		                                "GrantPer"=>"$TotalRate",
		                                "GrantAmount"=>"$Amount",
		                                "WorkAdd"=>"$WorkAdd",
		                                "Tel"=>"$Mobile",
		                                "Mail"=>"$Mail",
		                                "Gl"=>"$glPhone",
		                                "State"=>"5",
		                                "Photo"=>"$Photo",
		                                "Card"=>"1",
		                                "Vacation"=>array("T0"=>"0","T1"=>"0"),
		                                "Car"=>"0",
		                                "Child"=>"",
		                                "Estate"=>"0",
		                                "Remark"=>"$Remark",
		                                "Note"=>"$NoteSign",
		                                "IsNormalLeave" => "$isNormalLeave"
		 );
		 $Count3++;
	  }while($myRow = mysql_fetch_array($Result));
 }
?>