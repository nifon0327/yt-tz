<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
?>
<body style="background:#ffffff;">
<?php
$i=1;
$mySql="SELECT Id,Caption,Attached FROM $DataIn.zw2_hzdoc WHERE  TypeId=1 AND Id IN(108,188,190,222,384,316) ORDER BY Id";
$myResult = mysql_query($mySql,$link_id);
if($myRow = mysql_fetch_array($myResult)){
	do{
         $m=1;
         $Id=$myRow["Id"];
		$Caption=$myRow["Caption"];
		$Attached=$myRow["Attached"];
		$d=anmaIn("download/hzdoc/",$SinkOrder,$motherSTR);		
		if($Attached!=""){
			$f=anmaIn($Attached,$SinkOrder,$motherSTR);
			$Attached="(<span onClick='OpenOrLoad(\"$d\",\"$f\",6)' style='CURSOR: pointer;' class='yellowN'>查阅</span>)";
			}
		else{
			$Attached="&nbsp;";
			}
		
		echo "<br><div style='font-size:13px;'>&nbsp;&nbsp;".$i."、".$Caption. "&nbsp;&nbsp;$Attached</div>";
		$i++;
		} while($myRow = mysql_fetch_array($myResult));
	}
?>
</body>
</html>