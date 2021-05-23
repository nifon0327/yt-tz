<?php 
//读取通知标题
$recordSql=mysql_query("SELECT ReadTime FROM  $DataPublic.app_readrecord WHERE Number='$LoginNumber' AND Item='Msg' LIMIT 1",$link_id);
if ($recordRow = mysql_fetch_array($recordSql)){
     $readTime=$recordRow["ReadTime"];
}
else{
     $readTime=date("Y-m-d") . " 00:00:00"; 
}

$dataArray = array();
$Today=date("Y-m-d");
$LastMonth=substr(date("Y-m-d",strtotime("$Today  -3   month") ),0,7);
$mySql = "select A.Sign,A.Id,A.Title,A.Date,A.Operator,A.SMSTime,A.cSign  FROM (
	                       select  '1' as Sign,Id,Title,Date,Operator,SMSTime,cSign FROM $DataPublic.msg1_bulletin WHERE Date>='$LastMonth' 
	                 UNION ALL 
	                      select  '3' as Sign,Id,'人事通知' as Title,Date,Operator,SMSTime,cSign FROM $DataPublic.msg3_notice  WHERE Date>='$LastMonth' 
	                   )A  order by A.Date desc";//AND (cSign=7 OR cSign=0) 
$myResult = mysql_query($mySql);
if($myRow = mysql_fetch_assoc($myResult))
{
	do {
                   $Id=$myRow["Id"];
                   $Sign=$myRow["Sign"];
	               $Title=$myRow["Title"];		
	               $Date=$myRow["Date"];
	               $SMSTime=$myRow["SMSTime"];
	               
	               $cSign=$myRow["cSign"]==3?"(47栋)":"";
	               $IsNewSign=$SMSTime>$readTime?"icon_new":"";
                   $Operator=$myRow["Operator"];
                   include "../../model/subprogram/staffname.php";
                   
                   $dataArray[]=array(
                      "Id"=>"$Id",
                      "Title"=>array("Text"=>"$Title$cSign"),
                      "Col1"=>array("Text"=>"$Date"),
                      "Col2"=>array("Text"=>"$Operator","Operator"=>$myRow["Operator"],"Align"=>"R"),
                      "onTap"=>array("Target"=>"Detail","Args"=>"$Id|$Sign"),
                      "rIcon"=>"$IsNewSign"
                   );
	}while($myRow = mysql_fetch_assoc($myResult));
    $jsonArray=array("Status"=>"OK","data"=>$dataArray);    
}
?>