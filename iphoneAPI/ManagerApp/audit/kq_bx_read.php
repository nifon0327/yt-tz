<?
    $ipadTag = "yes";
 	include_once  "../../model/kq_YearHolday.php";
 	
 //补休审核
	$banchIdSql=mysql_fetch_array(mysql_query("SELECT M.BranchId,M.GroupId FROM $DataPublic.staffmain M WHERE M.Number='$LoginNumber' LIMIT 1",$link_id));
	$BanchId= $banchIdSql["BranchId"];
	$GroupId=$banchIdSql["GroupId"];
	$CheckManager=mysql_query("SELECT B.Manager  FROM $DataIn.branchmanager B  WHERE B.Manager='$LoginNumber' ",$link_id);
	if (mysql_num_rows($CheckManager)>0){
	     $SearchRows=" AND M.BranchId='$BanchId'";
	}
	else{
	     $SearchRows=" AND M.GroupId='$GroupId'";
	}                    
	       
	if ($LoginUserId=="10868" || $LoginUserId=="10006" || $LoginUserId=="10001" || $LoginUserId=="10341") {
	     $SearchRows="";
	 }
	 /*
	 if ($LoginUserId=="10019"){
		 $SearchRows=" AND M.cSign=3 ";
	 }
	 */
 $SearchRows.=" AND M.cSign=7";
 
 $hidden=1;$OverNums=0;
 $mySql="SELECT J.Id,J.Number,J.StartDate,J.EndDate,J.Note,J.Reason,J.Date,J.Checker,J.Estate,J.type,J.hours,J.Operator,M.Name,M.cSign,M.BranchId,M.GroupId,
                B.Name AS Branch,D.Name AS Job,W.Name AS   WorkAdd 
 FROM $DataPublic.bxsheet J 
LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number 
LEFT JOIN $DataPublic.branchdata B ON B.Id=M.BranchId
LEFT JOIN $DataPublic.jobdata D ON D.Id=M.JobId
LEFT JOIN $DataPublic.staffworkadd W ON W.Id=M.WorkAdd
WHERE  J.Estate=1  AND M.Estate=1 $SearchRows order by J.Id ";
//echo $mySql;
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            do 
            {	
                    $Id=$myRow["Id"];
                    $Number=$myRow["Number"];
                    $Name=$myRow["Name"];
                    $WorkAdd=$myRow["WorkAdd"];
                    $Branch=$myRow["Branch"] . "-" . $myRow["Job"] . "($WorkAdd)";
	                $StartDate=$myRow["StartDate"];
                    $EndDate= $myRow["EndDate"];
                     $hours= $myRow["hours"];
                    
                    $calculateType = $myRow["type"];	
		           // $HourTotal=($calculateType == "0")?calculateHours($StartDate, $EndDate):GetBetweenDateDays($Number,$StartDate,$EndDate,"1",$DataIn,$DataPublic,$link_id);
                    
                    $Date=$myRow["Date"];
                    $Note=$myRow["Note"];
                    
                    $Operator=$myRow["Name"];
                    $Estate=$myRow["Estate"];
                    
                    $cSign=$myRow["cSign"];
                    $BranchId=$myRow["BranchId"];
                    $GroupId=$myRow["GroupId"];
                    
                    //审核人
                    $Manager=$cSign==3?"李莉":"陈冠义";
                    $tmpDataIn=$cSign==3?$DataSub:$DataIn;
                    $CheckManager=mysql_query("SELECT G.GroupLeader,M.Name FROM $tmpDataIn.staffgroup G   
	                    LEFT JOIN $DataPublic.staffmain M ON M.Number=G.GroupLeader 
	                     WHERE G.GroupId='$GroupId' ",$link_id);
                    if ($CheckManagerRow = mysql_fetch_array($CheckManager)){
	                     $Manager=$Number==$CheckManagerRow["GroupLeader"]?$Manager:$CheckManagerRow["Name"];
                    }

                     $StartDate= date("m/d H:i", strtotime($myRow["StartDate"]));
                     $EndDate=  date("m/d H:i", strtotime($myRow["EndDate"]));
                    
                    $dataArray[]=array("Id"=>"$Id","Number"=>"$Number","Name"=>"$Name","Job"=>"$Branch",
                    "Date"=>"$Date","Type"=>"99","Hours"=>"$hours" . "h",
                    "Range"=>array("T0"=>"$StartDate","T1"=>"$EndDate"),
                    "Estate"=>"$Estate","Remark"=>"$Note","Operator"=>"$Manager",
                     "onTap"=>array("Value"=>"0","hidden"=>"$hidden","Args"=>"$Number","Audit"=>"$AuditSign"),"List"=>array());
            }
            while($myRow = mysql_fetch_assoc($myResult)); 
    }
?>