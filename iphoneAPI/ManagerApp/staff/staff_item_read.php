<?php 
//员工信息列表
$defaultState=1;
$ipadTag="yes";
include "../../model/kq_YearHolday.php";
include "submodel/staff_worktime.php";
include "../../basic/class.php";


$BundleId = 'AshCloudApp';
$Device = 'iphoneAPI';
$appVersion = $AppVersion;
$segment = 'staff';
$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];

$uri = 'staff/staff_item_read';
				          
				          
$sql = "INSERT INTO `ac`.`app_userlog`
(
`BundleId`,
`Device`,
`Version`,
`IP`,
`Segment`,
`Uri`,
`creator`,
`Parameter`)
VALUES
(
'$BundleId',
'$Device',
'$appVersion',
'$user_IP',
'$segment',
'$uri',
'$LoginNumber',
'SegmentId=>$SegmentId');
";


mysql_query($sql);
 		
$SearchRows=$ModuleId==203?" AND M.cSign=3 AND M.OffStaffSign=0 ":"  AND M.OffStaffSign=0 ";
$OrderBy=$ModuleId==203?"KqSign DESC,":"";

$SearchRows.=$MC_FactoryCheckSign ==1?' AND M.JobId!=38 AND M.Number NOT IN(12150,12172,11880,12173,10744)':'';

$userResult=mysql_fetch_array(mysql_query("SELECT JobId FROM  $DataPublic.staffmain WHERE Number='$LoginNumber' AND Estate=1 LIMIT 1",$link_id));
$AllowEdit=$userResult["JobId"]==1 || $userResult["JobId"]==44   || $userResult["JobId"]==39 || $userResult["JobId"]==47 || $userResult["JobId"]==57?1:0;

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
	case 4:
	     $EstateRows=" M.Estate=1  AND M.JobId!=38"; 
	      $curMonth=date("Y-m");
	     //$SearchRows.=" AND NOT EXISTS (SELECT J.Number FROM $DataPublic.kqqjsheet J WHERE J.Number=M.Number AND DATE_FORMAT(J.EndDate,'%Y-%m-%d')>=CURDATE() AND DATE_FORMAT(J.StartDate,'%Y-%m-%d')<=CURDATE()) ";
	     
	      $SearchRows.=" AND NOT EXISTS (SELECT C.Number FROM $DataIn.checkinout C  WHERE C.Number=M.Number AND DATE_FORMAT(C.CheckTime,'%Y-%m-%d')=CURDATE()  ) ";
	      
	      $SearchRows.=" AND NOT EXISTS (SELECT C.Number FROM $DataOut.checkinout C  WHERE C.Number=M.Number AND DATE_FORMAT(C.CheckTime,'%Y-%m-%d')=CURDATE())";
	  break;
	case 0:
	default:
	  break;
}
$mySql="SELECT M.Number,M.Name,M.Mail,M.ExtNo,M.ComeIn,M.ContractSDate,M.ContractEDate,M.Introducer,M.Estate,M.KqSign,M.cSign,M.JobId,
			M.BranchId,M.GroupId,A.Name AS WorkAdd,S.Mobile,S.Dh,S.Photo,S.Tel,B.Name AS Branch,J.Name AS Job,R.Name AS BirthPlace   
			 FROM $DataPublic.staffmain M
			 LEFT JOIN $DataPublic.staffsheet S ON S.Number=M.Number
			 LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
			 LEFT JOIN $DataPublic.jobdata J ON J.Id=M.JobId
			 LEFT JOIN $DataPublic.staffworkadd A ON A.Id=M.WorkAdd 
			 LEFT JOIN $DataPublic.rprdata R ON R.Id=S.Rpr 
			 WHERE  $EstateRows  $SearchRows AND M.Number>0 ORDER BY B.SortId,$OrderBy M.GroupId,J.SortId,M.ComeIn,M.Number ";
//echo $mySql; 
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
		$BirthPlace=$myRow["BirthPlace"];
		
		if ($SegmentId==0 || $SegmentId==3){
			 //工龄计算
			 $ComeInYM=substr($ComeIn,0,7);
			 $GL_CheckFrom='iPhone';
			 include "../../public/subprogram/staff_model_gl.php";//输出$glPhone
		 }
		 
		  $WorkAdd=$myRow["WorkAdd"];
		  $BranchId=$myRow["BranchId"];
		  $Branch=$myRow["Branch"];
		  $GroupId=$myRow["GroupId"];
		  $KqSign=$myRow["KqSign"];
		  
		  $Mobile=$myRow["Mobile"]==""?"":$myRow["Mobile"];
	     $Dh=$myRow["Dh"]==""?"":$myRow["Dh"];
		 $Mail = $myRow["Mail"] == ""?"":strtolower($myRow["Mail"]);
         
		if ($BranchId==8){
			$DataLink=$myRow["cSign"]==7?$DataIn:$DataOut;
			$checkGroupName=mysql_fetch_array(mysql_query("SELECT GroupName FROM $DataLink.staffgroup WHERE  GroupId='$GroupId' ",$link_id));
			$Job=$checkGroupName["GroupName"];
		}
		
         $State=$defaultState;$Remark="";$ClockLoc="";
          if ($SegmentId==0  || $SegmentId==3){
					  //if ($myRow["KqSign"]<3){
						   $DataLink=$myRow["cSign"]==7?$DataIn:$DataOut;
						    if ($myRow["JobId"]!=38){
						    /*
						         $checkqSql=mysql_query("SELECT dFrom FROM $DataIn.checkinout WHERE  DATE_FORMAT(CheckTime,'%Y-%m-%d')=CURDATE() AND  Number='$Number'  
						       UNION ALL
						         SELECT dFrom FROM $DataOut.checkinout WHERE  DATE_FORMAT(CheckTime,'%Y-%m-%d')=CURDATE()   AND Number='$Number'  ",$link_id);*/
						          $checkqSql=mysql_query("SELECT dFrom FROM $DataLink.checkinout   WHERE DATE_FORMAT(CheckTime,'%Y-%m-%d')>=CURDATE()  AND Number='$Number' AND Number NOT IN(SELECT A.Number FROM (SELECT Number,Max(IF(CheckType='I',CheckTime,'')) AS inTime,Max(IF(CheckType='O',CheckTime,'')) AS outTime FROM $DataLink.checkinout WHERE Number='$Number' AND DATE_FORMAT(CheckTime,'%Y-%m-%d')=CURDATE()) A 
       WHERE A.outTime>A.inTime) ",$link_id);
                                 
						         if($checkqRow = mysql_fetch_array($checkqSql)) {
						                 $ClockLoc=$checkqRow["dFrom"];
						                 $State=mysql_num_rows($checkqSql)==1?1:4;
						         }
						         else{
							            $State=5;
						         }
						         
						         //$State=mysql_num_rows($checkqSql)==1?1:4;
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
		 if ($SegmentId==0  || $SegmentId==3){
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
		 $checkqjSql=mysql_query("SELECT StartDate,EndDate,Type,bcType,Reason FROM $DataPublic.kqqjsheet WHERE NOW() BETWEEN StartDate AND EndDate  AND Number='$Number'",$link_id);
		 if($qjRow = mysql_fetch_array($checkqjSql)) {
		         if ($SegmentId==3) continue;
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
		 /*
		 $YearDays=GetYearHolDays($LoginNumber,date("Y-m-d"),"",$DataIn,$DataPublic,$link_id);
		 $YearDays=$YearDays<0?0:$YearDays;
		 
	      //补休
	      $bxCheckSql = "Select  Sum(hours) as hours From $DataPublic.bxSheet Where Number = '$LoginNumber'";
		  $bxCheckResult =mysql_fetch_array(mysql_query($bxCheckSql,$link_id));
	       $bxHours=$bxCheckResult["hours"]*1;
	       
	     if ($bxHours>0){
		        $usedBxHours=0;
		        $bxQjCheckSql = "Select * From $DataPublic.kqqjsheet Where Number = '$LoginNumber' and Type= '5' AND DATE_FORMAT(StartDate,'%Y')>='2013'";
				$bxQjCheckResult = mysql_query($bxQjCheckSql,$link_id);
				
				while($bxQjCheckRow = mysql_fetch_array($bxQjCheckResult))
				{
					$startTime = $bxQjCheckRow["StartDate"];
					$endTime = $bxQjCheckRow["EndDate"];
					$bcType = $bxQjCheckRow["bcType"];
					$usedBxHours+= GetBetweenDateDays($LoginNumber,$startTime,$endTime,$bcType,$DataIn,$DataPublic,$link_id);
					
				}
				// echo "$Number ----$bxHours-$usedBxHours </br>";
				$bxHours-=$usedBxHours;
		}
		 $bxDays=$bxHours>0?$bxHours/8:0;
	     if (is_float($bxDays) && abs($bxDays-round($bxDays))>=0.1) {
	            $bxDays=number_format($bxDays,1);
	     }
	     else{
	          $bxDays=number_format($bxDays);
	     }
	     $BxDays= $bxDays;
		 */
		 $checkHsql=mysql_query("SELECT YearDays,BxDays FROM $DataPublic.staffholiday WHERE Number='$Number'",$link_id);
		 if($checkHrow = mysql_fetch_array($checkHsql)) {
		       $YearDays=$checkHrow["YearDays"];
		       $BxDays=$checkHrow["BxDays"];
		       
		       $YearDays=abs($YearDays-round($YearDays))>=0.1?number_format($YearDays,1):number_format($YearDays);
		       $BxDays=abs($BxDays-round($BxDays))>=0.1?number_format($BxDays,1):number_format($BxDays);
		 }
		 
		 			 
		 //modify by cabbage 20150105 修正年假的算法，改成和系統算法相同
	 	//$YearDays = GetYearHolDayDays($Number, date("Y"), date("Y")."-12-31", $DataIn, $DataPublic, $link_id);
                   
		/*		
		  if ($myRow["Estate"]==0 ){
			     $State=5;
			     $checkDsql=mysql_query("SELECT D.Reason,D.outDate,DT.Name AS TypeName	 FROM $DataPublic.dimissiondata D LEFT JOIN $DataPublic.dimissiontype DT ON DT.Id=D.Type
			      WHERE D.Number='$Number' Order by D.Id DESC LIMIT 1",$link_id);
				 if($checkDrow = mysql_fetch_array($checkDsql)) {
				        $outDate=$checkDrow["outDate"];
				        $Remark=$checkDrow["TypeName"] . " " .  $checkDrow["Reason"];
				 }
		  }
		  */
		  
		  if ($SegmentId==0  || $SegmentId==3){
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
	  else 
	   if ($State==5 && $myRow["JobId"]!=38) {
	           $Count3++; 
	  }
	
	  //在职表现
	  $checkPerformanceSql=mysql_query("SELECT Id FROM $DataPublic.staff_performance WHERE Number='$Number'  LIMIT 1",$link_id);
	  $NoteSign=mysql_num_rows($checkPerformanceSql)>0?1:0;
	  
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
						         "CurrentHours"=>"$CurrentHours" ,
						         "Note"=>"$NoteSign"                 
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
						         "CarLicenseNo"=>"$CarLicenseNo",
						         "Note"=>"$NoteSign"            
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
								 "BirthPlace"=>"$BirthPlace",
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
						         "Dh"=>"$Dh",
						         "AllowEdit"=>"$AllowEdit",
						         "Note"=>"$NoteSign"                    
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
 $outResult = mysql_fetch_array(mysql_query("SELECT count(*) AS Nums FROM $DataPublic.dimissiondata WHERE  outDate=CURDATE()",$link_id));
$DismissionCount=$outResult["Nums"];	
			
 $SegmentArray=array("在勤($Count0)","请假($Count1)","出差($Count2)","异常($Count3)");
 $SegmentId=$SegmentId==4?3:$SegmentId;
 $jsonArray=array("SegmentIndex"=>"$SegmentId","Segmented"=>$SegmentArray,"DismissionCount"=>"$DismissionCount","data"=>$jsondata);
?>