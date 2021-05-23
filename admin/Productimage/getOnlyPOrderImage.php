<?php   
//电信-ZX  2012-08-01
$TestStandardSign=0;
switch($TestStandard){
	case 1://黄色通过 临时变动  已审核通过：订单临时要求改标准图
		$checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id",$link_id);
		if($checkteststandardRow = mysql_fetch_array($checkteststandard)){	
			$TestStandard="<span onClick='viewImage(\"$POrderId\",2,1)' style='CURSOR: pointer; color:#FF00FF; font-weight:bold' title='此订单需更改标准图'>$cName</span>";
			}
		else{//正常已审核通过标准图
			$TestStandard="<span onClick='viewImage(\"$POrderId\",2,1)' style='CURSOR: pointer;color:#FF6633;'>$cName</span>";
			$TestStandardSign=1;
			}
	break;
	case 2://蓝色 审核中
		$TestStandard="<div class='blueB' title='标准图审核中'>$cName</div>";
	break;
	case 3://紫色#ff00ff  需更新标准图（对已通过的标准图做新的修改）
		$TestStandard="<div class='purpleB' style='CURSOR: pointer;' title='需更新标准图'>$cName</div>";
	break;
	case 4://紫色#FF6633 审核退回修改
		$checkRemark=mysql_fetch_array(mysql_query("select Remark FROM $DataIn.test_remark WHERE ProductId='$ProductId'",$link_id));
		$RemarkResult=$checkRemark["Remark"];
		$TestStandard="<div class='redB' style='CURSOR: pointer;' title='审核退回,原因:$RemarkResult'>$cName</div>";
	break;
	default://0未上传标准图 
		$TestStandard=$cName;
		break;
	}
?>