<?php 
//电信-joseph
include "../basic/chksession.php";
include "../basic/parameter.inc";
$selId=$_GET["Id"];
$mySql="SELECT Id,ListName,Name,Tel,Fax,Address,Remark FROM  $DataPublic.otdata_kfinfo WHERE Id=$selId";	
  if ($myResult = mysql_query($mySql,$link_id)) 
  {
	while ($myRow =mysql_fetch_array($myResult)){
      echo $myRow['Name']  . "|*|" . $myRow['Tel'] . "|*|" . $myRow['Fax'] . "|*|" . $myRow['Address']  . "|*|" . $myRow['Remark'];
    }
  }
  else {
    echo "";
   }
?>