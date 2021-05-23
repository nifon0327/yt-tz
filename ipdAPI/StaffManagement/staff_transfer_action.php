<?php 

	include "../../basic/parameter.inc";
	
	$branchTransfer = $_POST["branch"];
	$jobTransfer = $_POST["job"];
	$kqTransfer = $_POST["kq"];
	$gradeTransfer = $_POST["grade"];
	$Ids = $_POST["ids"];
	$Operator = $_POST["oprator"];
	
	$DateTime=date("Y-m-d H:i:s");
	$Date=date("Y-m-d");
	
	$NumberSTR="AND Number IN ($Ids)";
	
	$numberCountArray = explode(",",$Ids);
	$count = count($numberCountArray);
	
	$infoLine = "";
	
	//部门调动
	if($branchTransfer != "")
	{
		$branchInfo = explode("|",$branchTransfer);
		
		if($count == 1)
		{
			$BranchId = $branchInfo[0];
			$ActionIn = $branchInfo[1];
			$Month = $branchInfo[2];
			$Remark = $branchInfo[3];
		
			$getBranchIdResult = mysql_query("Select Id From $DataPublic.branchdata Where Name = '$BranchId'");
			$getBranchId = mysql_fetch_assoc($getBranchIdResult);
			$BranchId = $getBranchId["Id"];
		
			$ActionIn = substr($ActionIn,0,1);
		    if($DataIn=="ac"){
			   $inRecode = "INSERT INTO $DataPublic.redeployb SELECT NULL,Number,'$BranchId','$ActionIn','$Month','$Remark','$Date','0','$Operator' ,'1','0','$Operator','$DateTime','$Operator','$DateTime'
             FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
             }else{
			    $inRecode = "INSERT INTO $DataPublic.redeployb SELECT NULL,Number,'$BranchId','$ActionIn','$Month','$Remark','$Date','0','$Operator' FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
              }
		}
		else
		{
			$ActionIn = $branchInfo[0];
			$Month = $branchInfo[1];
			$Remark = $branchInfo[2];
			
			$branchHolder = explode("-",$ActionIn);
			$ActionIn = $branchHolder[0];
		    if($DataIn=="ac"){
			  $inRecode = "INSERT INTO $DataPublic.redeployb SELECT NULL,Number,BranchId,'$ActionIn','$Month','$Remark','$Date','0','$Operator' ,'1','0','$Operator','$DateTime','$Operator','$DateTime'
                                          FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
             }
           else{
			     $inRecode = "INSERT INTO $DataPublic.redeployb SELECT NULL,Number,BranchId,'$ActionIn','$Month','$Remark','$Date','0','$Operator' 
                                             FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
             }
		}
		
		$inResult=@mysql_query($inRecode);
		
		if($inResult)
		{
			$infoLine = $infoLine." 部门调动成功!";
			$upSql = "UPDATE $DataPublic.staffmain SET BranchId='$ActionIn' WHERE 1 $NumberSTR";
			$upResult = mysql_query($upSql);
		}
		else
		{
			$infoLine = $infoLine." 部门调动失败!";
		}
		
	}
	
	//职位调动
	if($jobTransfer != "")
	{
		$jobInfo = explode("|",$jobTransfer);
		if($count == 1)
		{
			$JobId = $jobInfo[0];
			$jobActionIn = $jobInfo[1];
			$jobMonth = $jobInfo[2];
			$jobRemark = $jobInfo[3];
			
			$getJobIdResult = mysql_query("Select Id From $DataPublic.jobdata Where Name = '$JobId'");
			$getJobId = mysql_fetch_assoc($getJobIdResult);
			$JobId = $getJobId["Id"];
			$jobIdArray = explode("-",$jobActionIn);
			$jobActionIn = $jobIdArray[0];
			if($DataIn == "ac"){
		    	  $jobInRecode = "INSERT INTO $DataPublic.redeployj SELECT NULL,Number,'$JobId','$jobActionIn','$jobMonth','$jobRemark','$Date','0','$Operator','1','0','$Operator','$DateTime','$Operator','$DateTime'
FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
                   }
            else{
		    	$jobInRecode = "INSERT INTO $DataPublic.redeployj SELECT NULL,Number,'$JobId','$jobActionIn','$jobMonth','$jobRemark','$Date','0','$Operator' FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
               }	
		}
		else
		{
			$JobId = "";
			$jobActionIn = $jobInfo[0];
			$jobMonth = $jobInfo[1];
			$jobRemark = $jobInfo[2];
			
			$jobIdArray = explode("-",$jobActionIn);
			$jobActionIn = $jobIdArray[0];
			if($DataIn == "ac"){
			      $jobInRecode = "INSERT INTO $DataPublic.redeployj SELECT NULL,Number,JobId,'$jobActionIn','$jobMonth','$jobRemark','$Date','0','$Operator' ,'1','0','$Operator','$DateTime','$Operator','$DateTime'
                  FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
              }
             else{
			      $jobInRecode = "INSERT INTO $DataPublic.redeployj SELECT NULL,Number,JobId,'$jobActionIn','$jobMonth','$jobRemark','$Date','0','$Operator' 
                  FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
               }	
		}
		$jobInResult = @mysql_query($jobInRecode);
		if($jobInResult)
		{
			$upSql = "UPDATE $DataPublic.staffmain SET JobId='$jobActionIn' WHERE 1 $NumberSTR";
			$upResult = mysql_query($upSql);
			$infoLine = $infoLine." 职位调动成功!";
		}
		else
		{
			$infoLine = $infoLine." 职位调动失败!";
		}		
	}
	
	//等级调动
	if($gradeTransfer != "")
	{
	
		$gradeInfo = explode("|",$gradeTransfer);
		$gradeId = $gradeInfo[0];
		$gradeActionIn = $gradeInfo[1];
		$gradeMonth = $gradeInfo[2];
		$gradeRemark = $gradeInfo[3];
	
		//$gradeInfo = explode("|",$gradeTransfer);
		$idArray = explode(",",$Ids);
		
		$gradeStr = "";
		
		for($i=0;$i<count($idArray);$i++)
		{
			$Number = $idArray[$i];
		if($DataIn == "ac"){
			$gradeInRecode = "INSERT INTO $DataPublic.redeployg SELECT NULL,'$Number',Grade,'$gradeActionIn','$gradeMonth','$gradeRemark','$Date','0','$Operator','1','0','$Operator','$DateTime','$Operator','$DateTime'
FROM $DataPublic.staffmain WHERE 1 AND Number='$Number' AND Grade!='$gradeActionIn'";
          }
     else{
			$gradeInRecode = "INSERT INTO $DataPublic.redeployg SELECT NULL,'$Number',Grade,'$gradeActionIn','$gradeMonth','$gradeRemark','$Date','0','$Operator' FROM $DataPublic.staffmain WHERE 1 AND Number='$Number' AND Grade!='$gradeActionIn'";
           }
			$gradeAction=@mysql_query($gradeInRecode);
			if($gradeAction)
			{
				$gradeStr = $gradeStr."$Number等级调动成功";
				$upSql = "UPDATE $DataPublic.staffmain SET Grade='$gradeActionIn' WHERE Number='$Number' AND Grade!='$$gradeId' LIMIT 1";
				$upResult = mysql_query($upSql);
			}
		}
		
		
		$infoLine = $infoLine." ".$gradeStr;
		
	}
	
	//考勤调动
	if($kqTransfer != "")
	{
		$kqInfo = explode("|",$kqTransfer);
		
		if($count == 1)
		{
			
			$kqId = $kqInfo[0];
			$kqIdResult = mysql_query("Select Id From $DataPublic.kqtype Where Name = '$kqId'");
			$kqIdRow = mysql_fetch_assoc($kqIdResult);
			$kqId = $kqIdRow["Id"];
			
			/*
			$kqActionIn = $kqInfo[1];
			$kqInResult = mysql_query("SELECT Id FROM  $DataPublic.kqtype WHERE  Name =  '$kqActionIn'");
			$kqInRow = mysql_fetch_assoc($kqInResult);
			$kqActionIn = $kqInRow["Id"];
			*/
			$kqActionIn = $kqInfo[1];
			$kqActionInArray = explode("-",$kqActionIn);
			$kqActionIn = $kqActionInArray[0];
		
			$kqMonth = $kqInfo[2];
			$kqRemark = $kqInfo[3];
			if($DataIn =="ac"){
			          $inRecode = "INSERT INTO $DataPublic.redeployk SELECT NULL,Number,'$kqId','$kqActionIn','$kqMonth','$kqRemark','$Date','0','$Operator','1','0','$Operator','$DateTime','$Operator','$DateTime'
                  FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
              }else{
			          $inRecode = "INSERT INTO $DataPublic.redeployk SELECT NULL,Number,'$kqId','$kqActionIn','$kqMonth','$kqRemark','$Date','0','$Operator' 
                     FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
               }		
		}
		else
		{
			$kqActionIn = $kqInfo[0];
			/*
			$kqInResult = mysql_query("Select Id From $DataPublic.kqtype Where Name = '$kqActionIn'");
			$kqInRow = mysql_fetch_assoc($kqInResult);
			$kqActionIn = $kqInRow["Id"];
			*/
			$kqIdResult = mysql_query("Select Id From $DataPublic.kqtype Where Name = '$kqActionIn'");
			$kqIdRow = mysql_fetch_assoc($kqIdResult);
			$kqActionIn = $kqIdRow["Id"];
		
			$kqMonth = $kqInfo[1];
			$kqRemark = $kqInfo[2];
			if($DataIn =="ac"){
			           $inRecode = "INSERT INTO $DataPublic.redeployk SELECT NULL,Number,KqSign,'$kqActionIn','$kqMonth','$kqRemark','$Date','0','$Operator','1','0','$Operator','$DateTime','$Operator','$DateTime'
FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
                }else{
			           $inRecode = "INSERT INTO $DataPublic.redeployk SELECT NULL,Number,KqSign,'$kqActionIn','$kqMonth','$kqRemark','$Date','0','$Operator' FROM $DataPublic.staffmain WHERE 1 $NumberSTR ";
                }
		}
		
		if(mysql_query($inRecode))
		{
			$infoLine = $infoLine." 考勤调动成功!";
			$upSql = "UPDATE $DataPublic.staffmain SET KqSign='$kqActionIn' WHERE 1 $NumberSTR";
			$upResult = mysql_query($upSql);
		}
		else
		{
			$infoLine = $infoLine." 考勤调动失败!";
		}
	}
	
	
	$result = array($infoLine);
	echo json_encode($result);

?>