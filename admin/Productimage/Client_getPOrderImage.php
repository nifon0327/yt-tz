<?php   
//电信-ZX  2012-08-01
//客户页面用，绑定英文代码eCode
if($TestStandard==1){
	//输出标准图
	
	$checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id",$link_id);
	if($checkteststandardRow = mysql_fetch_array($checkteststandard)){	
		$TestStandard="<span onClick='viewImage(\"$POrderId\",2,1)' style='CURSOR: pointer; color:#FF00FF; font-weight:bold' title='需更改标准图!!.$TestRemark'>$eCode</span>";
		}
	else{
		$TestStandard="<span onClick='viewImage(\"$POrderId\",2,1)' style='CURSOR: pointer;color:#FF6633;' title='$TestRemark'>$eCode</span>";
		}
	}
else{
	if($TestStandard==2){
		$TestStandard="<div class='blueB' title='标准图审核中'>$eCode</div>";
		}
	else{
		$TestStandard=$eCode;
		}
}
?>