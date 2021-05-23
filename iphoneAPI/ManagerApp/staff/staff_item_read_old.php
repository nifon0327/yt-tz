<?php 
//员工信息列表
$defaultState=0;
$ipadTag="yes";
include "../../model/kq_YearHolday.php";
include "submodel/staff_worktime.php";
 		  
$SearchRows=$ModuleId==203?" AND M.cSign=3":"AND M.cSign IN (3,7)";
$OrderBy=$ModuleId==203?"KqSign DESC,":"";

$jsondata=array();
$Count0=0;$Count1=0;$Count2=0;$Count3=0;

 $EstateRows=" M.Estate>0";
switch($SegmentId){
   case 1:
        $SearchRows.=" AND EXISTS (SELECT K.Number FROM $DataPublic.kqqjsheet  K WHERE NOW() BETWEEN K.StartDate AND K.EndDate AND K.Number=M.Number)"; 
	    break;
    case 2:
        $SearchRows.=" AND EXISTS (SELECT B.Businesser FROM $DataPublic.info1_business  B WHERE (B.EndTime='0000-00-00 00:00:00'  OR B.EndTime>NOW()) AND B.Businesser=M.Name )";
      break;
	case 3:
	     $EstateRows=" M.Estate=0"; 
	      $curMonth=date("Y-m");
	     $SearchRows.=" AND EXISTS (SELECT K.Number  FROM $DataPublic.dimissiondata K WHERE  DATE_FORMAT(K.outDate,'%Y-%m')='$curMonth' AND K.Number=M.Number )";
	  break;
	case 0:
	default:
	  break;
}
$mySql="SELECT M.Number,M.Name,M.Mail,M.ExtNo,M.ComeIn,M.ContractSDate,M.ContractEDate,M.Introducer,M.Estate,M.KqSign,M.cSign,M.JobId,
			M.BranchId,M.GroupId,A.Name AS WorkAdd,S.Mobile,S.Dh,S.Photo,S.Tel,B.Name AS Branch,J.Name AS Job 
			 FROM $DataPublic.staffmain M
			 LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
			 LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
			 LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
			 LEFT JOIN $DataPublic.staffworkadd A ON A.Id=M.WorkAdd 
			 WHERE  $EstateRows  $SearchRows ORDER BY M.BranchId,$OrderBy M.GroupId,M.JobId,M.ComeIn,M.Number ";
// echo $mySql; 
 $Result = mysql_query($mySql,$link_id);
while($myRow = mysql_fetch_array($Result)) {
         $Number=$myRow["Number"];
         $Name = $myRow["Name"];
		 $Job=$myRow["Job"];
         $Photo=$myRow["Photo"];
         $Mobile=$myRow["Mobile"];
         $Dh=$myRow["Dh"];
		//入职日期
		$ComeIn=$myRow["ComeIn"];
		
		if ($SegmentId==0){
			 //工龄计算
			 $ComeInYM=substr($ComeIn,0,7);
			 $GL_CheckFrom='iPhone';
			 include "../../public/subprogram/staff_model_gl.php";//输出$glPhone
		 }
		 
		  $WorkAdd=$myRow["WorkAdd"];
		  $BranchId=$myRow["BranchId"];
		  $Branch=$myRow["Branch"];
		  $GroupId=$myRow["GroupId"];
		  
		  $Mobile=$myRow["Mobile"]==""?"":$myRow["Mobile"];
	     $Dh=$myRow["Dh"]==""?"":$myRow["Dh"];
		 $Mail = $myRow["Mail"] == ""?"":strtolower($myRow["Mail"]);
         
         $State=$defaultState;$Remark="";$ClockLoc="";
          if ($SegmentId==0){
					  //if ($myRow["KqSign"]<3){
						   $DataLink=$myRow["cSign"]==7?$DataIn:$DataOut;
						    if ($myRow["JobId"]!=38){
						         $checkqSql=mysql_query("SELECT dFrom FROM $DataLink.checkinout   WHERE CheckTime>=CURDATE()  AND Number='$Number' AND Number NOT IN(SELECT A.Number FROM (SELECT Number,Max(IF(CheckType='I',CheckTime,'')) AS inTime,Max(IF(CheckType='O',CheckTime,'')) AS outTime FROM $DataIn.checkinout WHERE Number='$Number' AND DATE_FORMAT(CheckTime,'%Y-%m-%d')=CURDATE()) A 
       WHERE A.outTime>A.inTime) ",$link_id);
						         if($checkqRow = mysql_fetch_array($checkqSql)) {
						                 $ClockLoc=$checkqRow["dFrom"];
						         }
						         
						         $State=mysql_num_rows($checkqSql)==1?1:4;
						   }
						   else {//保安跨日值班
							      $kqCheckRow=mysql_fetch_array(mysql_query("SELECT  MAX(IF(CheckType='I',CheckTime,'')) AS inTime,MAX(IF(CheckType='O',CheckTime,'')) AS outTime  FROM $DataLink.checkinout   WHERE CheckTime>=CURDATE()  AND Number='$Number'",$link_id));
							       $inTime=$kqCheckRow["inTime"];
					               $outTime=$kqCheckRow["outTime"];
					               $State=$inTime>$outTime?1:4;
				                       
					               if ($outTime=="" && $State==4 ){
					                   $krCheckRow=mysql_fetch_array(mysql_query("SELECT  MAX(IF(CheckType='I',CheckTime,'')) AS inTime,MAX(IF(CheckType='O',CheckTime,'')) AS outTime FROM $DataLink.checkinout    WHERE  DATE_FORMAT(CheckTime,'%Y-%m-%d')=DATE_ADD(CURDATE(),INTERVAL -1 DAY)  AND Number='$Number'",$link_id));
					                  $inTime=$krCheckRow["inTime"];
					                  $outTime=$krCheckRow["outTime"];
					                  $State=$inTime>$outTime?1:$State;
					             }
				           }
				//	 }
		 }
		 
		 //车辆信息
		 $carSign=0;
		 $CarNo="";
		 if ($SegmentId==0){
				  //$UserName=$Number==10001?"陈经理":$Name;
				 $checkCarSql=mysql_query("SELECT Id,BrandId,CarNo FROM $DataPublic.cardata WHERE User='$Name' and Estate=1",$link_id);
				 if($checkCarRow = mysql_fetch_array($checkCarSql)) {
				        $carSign=$checkCarRow["BrandId"];
				        $CarNo=$checkCarRow["CarNo"];
				 }
		 }

		
		 //出差记录
		 $BusinessRemark="";$DepartTime="";
		 $checkBusinessSql=mysql_query("SELECT C.CarNo,C.BrandId,B.Remark,B.StartTime,B.EndTime FROM $DataPublic.info1_business  B 
		         LEFT JOIN $DataPublic.cardata C ON C.Id=B.CarId 
		         WHERE (B.EndTime='0000-00-00 00:00:00'  OR B.EndTime>NOW())  AND B.Businesser='$Name'",$link_id);
		  if($businessRow = mysql_fetch_array($checkBusinessSql)) {
		          $State=2; $Count2++;  //if ($State==1) $Count0++;
		          
		          $carSign=$businessRow["BrandId"];
		          $DepartTime= date("m/d H:s",strtotime($businessRow["StartTime"])) . "~" .  date("m/d H:s",strtotime($businessRow["EndTime"]));
		          $CarLicenseNo=$businessRow["CarNo"];
		          $BusinessRemark=$businessRow["Remark"];
		  }
		// $State=mysql_num_rows($checkBusinessSql)>0?2:$State;
		 
		 //请假记录
		 $qjType="";
		 $checkqjSql=mysql_query("SELECT StartDate,EndDate,Type,bcType,Reason FROM $DataPublic.kqqjsheet WHERE NOW() BETWEEN StartDate AND EndDate AND Number='$Number'",$link_id);
		 if($qjRow = mysql_fetch_array($checkqjSql)) {
		         if ($SegmentId==0 && $State==1){
		               $Count0++;$Count1++;
		         }
		         else{
			           $State=3;$Count1++;
		         }
		        
		         if ($SegmentId==1){
			         $qjType=$qjRow["Type"];
			         $bcType=$qjRow["bcType"];
			         $StartDate= $qjRow["StartDate"];
	                 $EndDate=  $qjRow["EndDate"];
	                 
			         $qjReason=$qjRow["Reason"]==""?"":$qjRow["Reason"];
			         $TotalHours=GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id);  //本次请假换算小时数
			         
			         $CurDate=date("Y-m-d H") . ":00:00";
			         $CurrentHours=GetBetweenDateDays($Number,$StartDate,$CurDate,$bcType,$DataIn,$DataPublic,$link_id);  //已休时长
	                 $qjRemark=date("m/d H:i", strtotime($qjRow["StartDate"])) . "~" . date("m/d H:i", strtotime($qjRow["EndDate"]));
			   }
		         
		 }
		 
		  //年、休假信息
		 $YearDays=0;$BxDays=0;
		 $checkHsql=mysql_query("SELECT YearDays,BxDays FROM $DataPublic.staffholiday WHERE Number='$Number'",$link_id);
		 if($checkHrow = mysql_fetch_array($checkHsql)) {
		       $YearDays=$checkHrow["YearDays"];
		       $BxDays=$checkHrow["BxDays"];
		       
		       $YearDays=(is_float($YearDays) && abs($YearDays-round($YearDays))>=0.1)?number_format($YearDays,1):number_format($YearDays);
		       $BxDays=(is_float($BxDays) && abs($BxDays-round($BxDays))>=0.1)?number_format($BxDays,1):number_format($BxDays);
		 }  
                   
				
		  if ($myRow["Estate"]==0 ){
			     $State=5;
			     $checkDsql=mysql_query("SELECT D.Reason,D.outDate,DT.Name AS TypeName	 FROM $DataPublic.dimissiondata D LEFT JOIN $DataPublic.dimissiontype DT ON DT.Id=D.Type
			      WHERE D.Number='$Number' Order by D.Id DESC LIMIT 1",$link_id);
				 if($checkDrow = mysql_fetch_array($checkDsql)) {
				        $outDate=$checkDrow["outDate"];
				        $Remark=$checkDrow["TypeName"] . " " .  $checkDrow["Reason"];
				 }
		  }
		  
		  if ($SegmentId==0){
				  //证件信息
				  $Identification="";
				  $certResult =mysql_query("SELECT S.IdcardPhoto,S.DriverPhoto,S.PassPort,S.PassTicket,C.OutIP,M.Name     
			                        FROM $DataPublic.staffsheet S  
			                        LEFT JOIN  $DataPublic.staffmain M ON M.Number=S.Number 
			                        LEFT JOIN $DataPublic.companys_group C ON C.cSign=M.cSign  WHERE  S.Number='$Number'  LIMIT 1",$link_id);
			      if($certRow = mysql_fetch_array($certResult)) {
			              if ($certRow["IdcardPhoto"]==1){//身份证
					             $Identification="I";
					        }
					      
					      if ($certRow["DriverPhoto"]==1){ //驾驶证
					          $Identification.=$Identification==""?"D":"|D";
					     }
					    
					     if ($certRow["PassTicket"]==1){//通行证PassTicket
					          $Identification.=$Identification==""?"H":"|H";
					     }
					     
					 	if ($certRow["PassPort"]==1){	//护照PassPort
					 	     $Identification.=$Identification==""?"P":"|P";
					 	 }
			      }
			      
			      //助学信息
				$childInfo="";
				$checkStudySql=mysql_query("SELECT Sex FROM $DataPublic.childinfo WHERE Number='$Number' Order by Sex desc",$link_id);
				while($studyRow = mysql_fetch_array($checkStudySql)){
				      $childInfo.=$childInfo==""?$studyRow["Sex"]:"|" . $studyRow["Sex"];
				}
		  }
		 
		  if ($State==1) $Count0++;
      
      $Auth=1;
      
	  switch($SegmentId){
		       case 1:
		           $jsondata[]=array(
								 "Tag" =>"data",
								  "Hidden"=>"0",
								 "Card"=>"1",
								 "BranchId"=>"$BranchId", 
								 "GroupId"=>"$GroupId",
								  "Job"=>"$Job",
								 "Name"=>"$Name",
								 "Number"=>"$Number",
								 "Vacation"=>array("T0"=>"$YearDays","T1"=>"$BxDays"),
								 "WorkAdd"=>"$WorkAdd",
								 "Car"=>"$carSign",
								 "Photo"=>"$Photo",
								 "Estate"=>"1",
								 "Auth" =>"$Auth",
								 "qjType"=>"$qjType",
								  "Remark"=>"$qjRemark",
						          "Reason"=>"$qjReason",
						         "TotalHours"=>"$TotalHours",
						         "CurrentHours"=>"$CurrentHours"                  
			              );
		          break;
		     case 2:
		           $jsondata[]=array(
								 "Tag" =>"data",
								  "Hidden"=>"0",
								 "Card"=>"1",
								 "BranchId"=>"$BranchId", 
								 "GroupId"=>"$GroupId",
								  "Job"=>"$Job",
								 "Name"=>"$Name",
								 "Number"=>"$Number",
								 "Vacation"=>array("T0"=>"$YearDays","T1"=>"$BxDays"),
								 "WorkAdd"=>"$WorkAdd",
								  "Car"=>"$carSign",
								 "Photo"=>"$Photo",
								 "Estate"=>"1",
								 "Auth" =>"$Auth",
								 "Remark"=>"$BusinessRemark",
						          "DepartTime"=>"$DepartTime",
						         "CarLicenseNo"=>"$CarLicenseNo"           
			              );

		        break;
		       default:
				 $jsondata[]=array(
								 "Tag" =>"data",
								  "Hidden"=>"0",
								 "Card"=>"1",
								 "BranchId"=>"$BranchId", 
								 "GroupId"=>"$GroupId",
								 "Unit" =>"$Branch",
								  "Job"=>"$Job",
								 "Name"=>"$Name",
								 "Number"=>"$Number",
								 "Vacation"=>array("T0"=>"$YearDays","T1"=>"$BxDays"),
								 "ComeIn"=>"$ComeIn",
								 "WorkAdd"=>"$WorkAdd",
								 "Car"=>"$carSign",
								 "State"=>"$State",
								 "Remark"=>"$Remark",
								 "Photo"=>"$Photo",
								 "Estate"=>"1",
								 "Gl"=>"$glPhone",
								 "Child"=>"$childInfo",
								 "ClockLoc"=>"$ClockLoc",
								 "Identification"=>"$Identification",
								 "Auth" =>"$Auth",
						         "CarLicenseNo"=>"$CarNo",
						         "Mobile"=>"$Mobile",
						         "Dh"=>"$Dh"                   
			              );
			       break;

	 }
 }
 
//按状态排序
if (count($jsondata)>0){
     foreach ($jsondata as $key => $row) { 
           $StateArray[$key] = $row['State']; 
           $BranchArray[$key] = $row['BranchId']; 
           $GroupArray[$key] = $row['GroupId']; 
    } 
     array_multisort($StateArray, SORT_ASC, $BranchArray, SORT_ASC,$GroupArray, SORT_ASC,$jsondata); 

	 //array_multisort($jsondata["State"], SORT_NUMERIC, SORT_ASC);
 }
 
 //统计离职人员
 $curMonth=date("Y-m");
 $outResult = mysql_fetch_array(mysql_query("SELECT count(*) AS Nums FROM $DataPublic.dimissiondata WHERE  DATE_FORMAT(outDate,'%Y-%m')='$curMonth'",$link_id));
$Count3=$outResult["Nums"];	
			
 $SegmentArray=array("在勤($Count0)","请假($Count1)","出差($Count2)","离职($Count3)");
 $jsonArray=array("SegmentIndex"=>"$SegmentId","Segmented"=>$SegmentArray,"data"=>$jsondata);
?>