<?
//请假记录
$ipadTag = "yes";
include "../../model/kq_YearHolday.php";

 $mySql="SELECT J.Id,J.StartDate,J.EndDate,J.Reason,J.bcType,J.Type,J.Estate,IFNULL(J.Checker,J.Operator) AS Operator,M.cSign,M.BranchId,M.ComeIn  
 FROM $DataPublic.kqqjsheet J 
LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number   
WHERE J.Number='$LoginNumber'  order by J.Estate DESC,J.StartDate DESC";
//echo $mySql;
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            do {	
                    $Id=$myRow["Id"];
	                $StartDate=$myRow["StartDate"];
                    $EndDate= $myRow["EndDate"];
                    $Estate=$myRow["Estate"];
                    
                    $bcType=$myRow["bcType"];
                    $qjHours = GetBetweenDateDays($Number,$StartDate,$EndDate,$bcType,$DataIn,$DataPublic,$link_id);
                    
                    $Operator=$myRow["Operator"];
                    if ($Number==$Operator || $Operator==0){
                         $Operator="系统";
                    }
                    else{
	                     include "../../model/subprogram/staffname.php";
                    }
                    
                    $Type=$myRow["Type"];
                    $Reason=$myRow["Reason"];
                     
                     $YearStr=date("Y", strtotime($myRow["StartDate"]));
                     $StartDate= date("m/d H:i", strtotime($myRow["StartDate"]));
                     $EndDate=  date("m/d H:i", strtotime($myRow["EndDate"]));
                    
                    $jsonArray[]=array("Id"=>"$Id","Year"=>"$YearStr","Type"=>"$Type","Hours"=>"$qjHours" . "h",
                    "Range"=>array("T0"=>"$StartDate","T1"=>"$EndDate"),
                    "Estate"=>"$Estate","Remark"=>$Reason,"Operator"=>"$Operator");
            }
            while($myRow = mysql_fetch_assoc($myResult)); 
    }
?>