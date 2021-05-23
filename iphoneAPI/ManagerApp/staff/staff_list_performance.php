<?
//员工在职表现
 $mySql="SELECT Id,Date,Description,Operator FROM $DataPublic.staff_performance WHERE Number='$Number' ORDER BY Date DESC";
//echo $mySql;
    $myResult = mysql_query($mySql);
    if($myRow = mysql_fetch_assoc($myResult))
    {
            $ComeIn=""; 
            do {	
                    $Id=$myRow["Id"];
                    $Description=$myRow["Description"];    
                     $Date=$myRow["Date"];                  
                     $Operator=$myRow["Operator"];
                    include "../../model/subprogram/staffname.php";
                    
                    $jsonArray[]=array(
                    "Title"=>"",
                    "Remark"=>"$Description",
                    "Col1"=>"$Date",
                    "Col2"=>"",
                    "Operator"=>"$Operator");
            }
            while($myRow = mysql_fetch_assoc($myResult)); 
    }
?>