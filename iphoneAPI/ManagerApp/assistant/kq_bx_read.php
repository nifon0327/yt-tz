<?
//补休记录
 $mySql="SELECT  J.Id,J.StartDate,J.EndDate,J.Date,J.Operator,J.Estate,J.Note, J.Type,J.Hours   
 FROM $DataPublic.bxSheet J 
WHERE J.Number='$LoginNumber'   order by J.Estate DESC,J.StartDate DESC";
//echo $mySql;
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            do {	
                    $Id=$myRow["Id"];
	                $StartDate=$myRow["StartDate"];
                    $EndDate= $myRow["EndDate"];
                    $Estate=$myRow["Estate"];
                    
                    $Hours =$myRow["Hours"];
                    
                    $Operator=$myRow["Operator"];
                    include "../../model/subprogram/staffname.php";
                    
                    $Note=$myRow["Note"];
                     
                     $YearStr=date("Y", strtotime($myRow["StartDate"]));
                     $StartDate= date("m/d H:i", strtotime($myRow["StartDate"]));
                     $EndDate=  date("m/d H:i", strtotime($myRow["EndDate"]));
                    
                    $jsonArray[]=array("Id"=>"$Id","Year"=>"$YearStr","Hours"=>"$Hours" . "h",
                    "Range"=>array("T0"=>"$StartDate","T1"=>"$EndDate"),
                    "Estate"=>"$Estate","Remark"=>"$Note","Operator"=>"$Operator");
            }
            while($myRow = mysql_fetch_assoc($myResult)); 
    }
?>