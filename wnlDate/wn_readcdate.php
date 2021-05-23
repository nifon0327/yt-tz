<?php 
//电信-zxq 2012-08-01
include "../basic/parameter.inc";
$sDate=$_GET["sDate"];
if ($sDate=="")
  {
   $nDate=date("Y-m-d");
  }
  else
  {
  	$nDate=$sDate;
  }
   $sDate=substr($nDate,0,4) . "-" . substr($nDate,5,2) . "-01"; 
   $sDay=date('t');
   $eDate=date('Y-m-d',strtotime("$sDate + $sDay day"));
   $strSel="select Date,Name,Type from " . $DataIn . ".kqchangedate where Date>='" . $sDate . "' and Date<'" . $eDate . "'";
if ($result=mysql_query($strSel,$link_id))
   {
   $FJdate="";
   while($row = mysql_fetch_array($result))
  {
  $FJdate=$FJdate  . "/" . substr($row['Date'],5,2) . substr($row['Date'],8,2) . "*" . $row['Name'] . "#" . $row['Type'];
  }
   echo $FJdate;
  }
 else 
 {
 	 echo "Read Datebase Error:" . mysql_error();
 }
?>