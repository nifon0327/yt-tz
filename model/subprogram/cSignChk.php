<?php 
//电信---yang 20120801
//代码-EWEN
$checkcSignRow = mysql_fetch_array(mysql_query("SELECT CShortName,ColorValue FROM $DataPublic.companys_group WHERE cSign=$cSign ORDER BY Id LIMIT 1",$link_id));
$cSign=$checkcSignRow["CShortName"];
if($checkcSignRow["ColorValue"]!=""){
	$cSign="<spnn style='color:$checkcSignRow[ColorValue]'><strong>".$cSign."</strong></span>";
	}
?>