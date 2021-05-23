<?
    $ipadTag = "yes";
 	include "../../model/kq_YearHolday.php";
 //请假审核
	$banchIdSql=mysql_fetch_array(mysql_query("SELECT M.BranchId,M.GroupId,M.JobId FROM $DataPublic.staffmain M WHERE M.Number='$LoginNumber' LIMIT 1",$link_id));
	$BanchId= $banchIdSql["BranchId"];
	$GroupId=$banchIdSql["GroupId"];
	$JobId=$banchIdSql["JobId"];
	$CheckManager=mysql_query("SELECT B.Manager  FROM $DataIn.branchmanager B  WHERE B.Manager='$LoginNumber' ",$link_id);
	if (mysql_num_rows($CheckManager)>0){
	     $SearchRows=" AND M.BranchId='$BanchId'";
	}
	else{
	     $SearchRows=" AND M.GroupId='$GroupId'";
	}                                    
	
	if ($JobId==1 || $JobId==39 || $JobId==57|| $JobId==9) {//总经理、经理、主管显示所有人的请假
	     $SearchRows=" ";
	 }
	 
	 
	 
	 
	 /*
	 if ($LoginUserId=="10019"){
		 $SearchRows=" AND M.cSign=3 ";
	 }
	 */
 //$SearchRows.=" AND M.cSign=7";
 if ($test_cz  == 1) {
		 $SearchRows=" ";
	 }
 $hidden=1;$OverNums=0;
 $mySql="SELECT J.Id,J.Number,J.StartDate,J.EndDate,J.Reason,J.Proof,J.bcType,J.Date,J.Estate,J.Locks,J.OPdatetime,J.Type,M.WorkAdd,
                              M.JobId,M.Name,M.cSign,M.BranchId,M.GroupId,B.Name AS Branch,D.Name AS Job,W.Name AS   WorkAdd 
			    FROM $DataPublic.kqqjsheet J 
				LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number 
				LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
				LEFT JOIN $DataPublic.jobdata D ON D.Id=M.JobId
				LEFT JOIN $DataPublic.staffworkadd W ON W.Id=M.WorkAdd
				WHERE 1 AND J.Estate=1 AND M.Estate=1 $SearchRows order by J.Id";
//echo $mySql;
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
    {
           $qjTypeArray=array(2,6,7,8,9);
            do 
            {	
                    $Id=$myRow["Id"];
                    $Number=$myRow["Number"];
                    $Name=$myRow["Name"];
                    $WorkAdd=$myRow["WorkAdd"];
                    $Branch=$myRow["Branch"] . "-" . $myRow["Job"] . "($WorkAdd)";
	                $StartDate=$myRow["StartDate"];
                    $EndDate= $myRow["EndDate"];
                    $bcType=$myRow["bcType"];
                    $qjHours = GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id);
                    
                    $Type=$myRow["Type"];
                    $Date=$myRow["Date"];
                    
                    $Reason=$myRow["Reason"];
                    $OPdatetime=$myRow["OPdatetime"]=="0000-00-00 00:00:00"?$Date:$myRow["OPdatetime"];
				    $opHours= geDifferDateTimeNum($OPdatetime,"",1);
                    if ($opHours>=$timeOut[0]) $OverNums++;
                    
                    $Operator=$myRow["Name"];
                    $Estate=$myRow["Estate"];
                    
                    $cSign=$myRow["cSign"];
                    $BranchId=$myRow["BranchId"];
                    $GroupId=$myRow["GroupId"];
                    $JobId=$myRow["JobId"];
                    
                    //审核人
                    $Manager=$cSign==3?"李莉":"陈冠义";
                    $tmpDataIn=$cSign==3?$DataSub:$DataIn;
                     if (in_array($Type,$qjTypeArray)){
                             $Manager="侯兴华";
                     }
                     else{
		                    if ($JobId!=39){
		                       $CheckManager=mysql_query("SELECT B.Manager,M.Name FROM $tmpDataIn.branchmanager B   
				                    LEFT JOIN $DataPublic.staffmain M ON M.Number=B.Manager   
				                     WHERE B.BranchId='$BranchId' ",$link_id);
			                    if ($CheckManagerRow = mysql_fetch_array($CheckManager)){
				                     $Manager=$Number==$CheckManagerRow["Manager"]?$Manager:$CheckManagerRow["Name"];
			                    }

		                        $CheckGroupLeader=mysql_query("SELECT G.GroupLeader,M.Name FROM $tmpDataIn.staffgroup G   
				                    LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader 
				                     WHERE G.GroupId='$GroupId' ",$link_id);
			                    if ($CheckGroupLeaderRow = mysql_fetch_array($CheckGroupLeader)){
				                     $Manager=$Number==$CheckGroupLeaderRow["GroupLeader"]?$Manager:$CheckGroupLeaderRow["Name"];
			                    }
		                }
                    }
                    
                    $Manager=$myRow["WorkAdd"]==6?"陈忆甬":$Manager;
                    //年假
                      $YearHolHours=GetYearHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataPublic,$link_id)*8;
				      $UsedYearHolHours=HaveYearHolDayDays($Number,$StartDate,$EndDate,$DataIn,$DataPublic,$link_id) ;
				      if ($Type==4 && $UsedYearHolHours)  $UsedYearHolHours-=$qjHours;
				      
				      $YearDays=$YearHolHours-$UsedYearHolHours;
				      $YearDays=$YearDays>0?$YearDays/8:0;
				      if (is_float($YearDays) && abs($YearDays-round($YearDays))>=0.1) {
				         $YearDays=number_format($YearDays,1);
				      }
				      else{
					      $YearDays=number_format($YearDays);
				      }
				       
                    //补休
                    $bxHours=getTakeDeferredHolidays($Number,$DataIn,$DataPublic,$link_id);
                    $bxDays=$bxHours>0?$bxHours*(1.0/8.0):0;
                     if (is_float($bxDays) && abs($bxDays-round($bxDays))>=0.1) {
                          $bxDays=number_format($bxDays,1);
                     }
                     else{
	                     $bxDays=number_format($bxDays);
                     }
                    
                     $StartDate= date("m/d H:i", strtotime($myRow["StartDate"]));
                     $EndDate=  date("m/d H:i", strtotime($myRow["EndDate"]));
                    
                    $dataArray[]=array("Id"=>"$Id","Number"=>"$Number","Name"=>"$Name","Job"=>"$Branch",
                    "Date"=>"$Date","Type"=>"$Type","Hours"=>"$qjHours" . "h",
                    "Range"=>array("T0"=>"$StartDate","T1"=>"$EndDate"),"Vacation"=>array("T0"=>"$YearDays","T1"=>"$bxDays"),
                    "Estate"=>"$Estate","Remark"=>$Reason,"Operator"=>"$Manager",
                     "onTap"=>array("Value"=>"1","hidden"=>"$hidden","Args"=>"$Number","Audit"=>"$AuditSign"),"List"=>array());
            }
            while($myRow = mysql_fetch_assoc($myResult)); 
    }
?>