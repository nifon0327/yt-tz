<?php 
//电信-EWEN
$ZL_Result = mysql_query("SELECT Hours FROM $DataIn.temp_zltime  WHERE Number=$Number and Date='$CheckDate' Limit 1",$link_id);
if($ZL_Row = mysql_fetch_array($ZL_Result)){
	//要判断假日加班倍数
	$ZL_Hours=$ZL_Row["Hours"];
	switch($DateType){
		case "G":
			$sumBTime+=$ZL_Hours;
			$BTime="<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($BTime+$ZL_Hours)."</span>";
			break;
		case "X":
			$sumCTime+=$ZL_Hours;
			$CTime="<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($CTime+$ZL_Hours)."</span>";
			break;
		case "F":
			if($jbTimes==3){
				$sumDTime+=$ZL_Hours;
				$DTime="<span title='包括直落：$ZL_Hours 工时'style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($DTime+$ZL_Hours)."</span>";
				}
			else{
				$sumCTime+=$ZL_Hours;
				$CTime="<span title='包括直落：$ZL_Hours 工时'style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($CTime+$ZL_Hours)."</span>";
				}
			break;
		case "Y"://相当于工作日的加点
			$sumBTime+=$ZL_Hours;
			$BTime="<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($BTime+$ZL_Hours)."</span>";
			break;
		case "W"://相当于工作日的加点
			$sumBTime+=$ZL_Hours;
			$BTime="<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($BTime+$ZL_Hours)."</span>";
			break;
		}
	}
?>