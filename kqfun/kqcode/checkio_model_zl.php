<?php 
//计算直落的工时  $DataPublic.kqzltime 二合一已更新$DataIn.电信---yang 20120801
$ZL_Result = mysql_query("SELECT sum(Hours) as Hours FROM $DataPublic.kqzltime  WHERE Number=$Number and Date='$CheckDate'",$link_id);
if($ZL_Row = mysql_fetch_array($ZL_Result)){
	//要判断假日加班倍数
	$ZL_Hours=$ZL_Row["Hours"];
if($ZL_Hours!=0){// 有直落
	switch($DateType){
		case "G":
			$sumGJTime+=$ZL_Hours;
			$GJTime="<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($GJTime+$ZL_Hours)."</span>";
			break;
		case "X":
			$sumXJTime+=$ZL_Hours;
			$XJTime="<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($XJTime+$ZL_Hours)."</span>";
			break;
		case "F":
			if($jbTimes==3){
				$sumFJTime+=$ZL_Hours;
				$FJTime="<span title='包括直落：$ZL_Hours 工时'style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($FJTime+$ZL_Hours)."</span>";
				}
			else{
				$sumXJTime+=$ZL_Hours;
				$XJTime="<span title='包括直落：$ZL_Hours 工时'style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($XJTime+$ZL_Hours)."</span>";
				}
			break;
		case "Y"://相当于工作日的加点
			$sumGJTime+=$ZL_Hours;
			$GJTime="<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($YXJTime+$ZL_Hours)."</span>";
			break;
		case "W"://相当于工作日的加点
			$sumGJTime+=$ZL_Hours;
			$GJTime="<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($WXJTime+$ZL_Hours)."</span>";
			break;
		}
	}
}
?>