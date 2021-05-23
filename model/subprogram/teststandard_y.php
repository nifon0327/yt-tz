<?php 
//输出标准图电信---yang 20120801
$FileName="T".$ProductId.".jpg";
$tf=anmaIn($FileName,$SinkOrder,$motherSTR);
$td=anmaIn("download/teststandard/",$SinkOrder,$motherSTR);

//更改标准图 add by zx 20100804
$checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id",$link_id);
if($checkteststandardRow = mysql_fetch_array($checkteststandard)){	
	//$TestStandard="<div title='需更改标准图' style='background:#FF0'> $TestStandard </div>";break;		//更改标准图
	$TestStandard="<span onClick='OpenOrLoad(\"$td\",\"$tf\",$ProductId)' style='CURSOR: pointer; color:#F0F; font-weight:bold' title='需更改标准图!!'>$cName</span>";
	}
else{
	$TestStandard="<span onClick='OpenOrLoad(\"$td\",\"$tf\",$ProductId)' style='CURSOR: pointer;' class='yellowB'>$cName</span>";
	}
?>