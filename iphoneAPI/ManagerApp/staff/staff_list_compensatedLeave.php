<?
//员工补休记录

$ipadTag = "yes";
include "../../model/kq_YearHolday.php";

 $mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Note,J.Type,J.Estate,J.Operator,M.cSign,M.BranchId,M.ComeIn,J.hours  
 FROM $DataPublic.bxSheet J 
LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number   
WHERE J.Number='$Number'  AND TIMESTAMPDIFF(DAY,J.StartDate,Now())<366 order by J.Estate DESC,J.StartDate DESC";
//echo $mySql;
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            $ComeIn=""; 
            do {	
                    $Id=$myRow["Id"];
	                $StartDate=$myRow["StartDate"];
                    $EndDate= $myRow["EndDate"];
                    $Estate=$myRow["Estate"];
                    if ($ComeIn=="" && $ReadBranchSign==1){
                         $ComeIn=$myRow["ComeIn"];
                         $ComeInYM=substr($ComeIn,0,7);
		                  include "../../public/subprogram/staff_model_gl.php";//输出$glPhone='Y|m'
		                 
	                     $cSign=$myRow["cSign"];
                         $BranchId=$myRow["BranchId"];
	                    //部门人数
	                    $CheckBranch=mysql_fetch_array(mysql_query("SELECT Count(*) AS Nums FROM $DataPublic.staffmain M  WHERE M.cSign='$cSign' AND  M.BranchId='$BranchId' AND M.Estate=1",$link_id));
	                    $BranchNums=$CheckBranch["Nums"];
	                    //部门请假人数
	                      $CheckNums=mysql_fetch_array(mysql_query("SELECT Count(*) AS Nums FROM $DataPublic.kqqjsheet J  
	                      LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number 
	                      WHERE M.cSign='$cSign' AND  M.BranchId='$BranchId' 
	                                    AND ('$StartDate' BETWEEN J.StartDate and J.EndDate OR '$EndDate' BETWEEN J.StartDate and J.EndDate)",$link_id));
	                    $qjNums=$CheckNums["Nums"];
	                    $qjPercent=$BranchNums>0?round($qjNums/$BranchNums*100):"";
	                    
	                     $jsonArray[]=array("Id"=>"0","ComeIn"=>"$ComeIn","Gl"=>"$glPhone","Branchs"=>"$BranchNums",
                          "qjNums"=>"$qjNums","Percent"=>"$qjPercent");
	                    continue;
                    }
                    if ($Estate>0) continue;
                    
                    $bcType=$myRow["bcType"];
                    $hours=$myRow["hours"];
                    //$qjHours = GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id);
                    
                    $Operator=$myRow["Operator"];
                    if ($Number==$Operator){
                         $Operator="系统";
                    }
                    else{
	                     include "../../model/subprogram/staffname.php";
                    }
                    
                    $Type=$myRow["Type"];
                    $Reason=$myRow["Note"];
                     
                     $YearStr=date("Y", strtotime($myRow["StartDate"]));
                     $StartDate= date("m/d H:i", strtotime($myRow["StartDate"]));
                     $EndDate=  date("m/d H:i", strtotime($myRow["EndDate"]));
                    
                    $jsonArray[]=array("Id"=>"$Id","Year"=>"$YearStr","Type"=>"$Type","Hours"=>"$hours" . "h",
                    "Range"=>array("T0"=>"$StartDate","T1"=>"$EndDate"),
                    "Estate"=>"$Estate","Remark"=>"$Reason","Operator"=>"$Operator");
            }
            while($myRow = mysql_fetch_assoc($myResult)); 
    }
?>