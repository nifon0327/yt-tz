<?php 
//验栈模式：2013-04以前的
$xzlSign=0;$gzlSign=0;$fzlSign=0;
$ZL_Result = mysql_query("SELECT SUM(Hours) AS  Hours FROM $DataPublic.kqzltime  WHERE Number=$Number and Date='$CheckDate'",$link_id);
if($ZL_Row = mysql_fetch_array($ZL_Result)){
	//要判断假日加班倍数
	$ZL_Hours=$ZL_Row["Hours"];
if($ZL_Hours!=0){// 有直落
	switch($DateType){
		case "G":
			$sumGJTime+=$ZL_Hours;
			$sumGJTimeIpad = $sumGJTime;
			$GJTimeAll=$GJTime+$ZL_Hours;
			$gzlSign=1;
			$GJTime="<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($GJTimeAll)."</span>";
			$GJTimeIpad = $GJTimeAll;
			break;
		case "X":
			$sumXJTime+=$ZL_Hours;
			$sumXJTimeIpad = $sumXJTime;
			$XJTimeAll=$XJTime+$ZL_Hours;
			$xzlSign=1;
			$XJTime="<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($XJTime+$ZL_Hours)."</span>";
			$XJTimeIpad = $XJTimeAll;
			break;
		case "F":
			if($jbTimes==3){
				$sumFJTime+=$ZL_Hours;
				$sumFJTimeIpad = $sumFJTime;
				$FJTimeAll=$FJTime+$ZL_Hours;
				$fzlSign=1;
				$FJTime="<span title='包括直落：$ZL_Hours 工时'style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($FJTimeAll)."</span>";
				$FJTimeIpad = $FJTimeAll;
				}
			else{
				$sumXJTime+=$ZL_Hours;
				$sumXJTimeIpad = $sumXJTime;
				$XJTimeAll=$XJTime+$ZL_Hours;
				$xzlSign=1;
				$XJTime="<span title='包括直落：$ZL_Hours 工时'style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($XJTimeAll)."</span>";
				$XJTimeIpad = $XJTimeAll;
				}
			break;
		case "Y"://相当于工作日的加点
			$sumGJTime+=$ZL_Hours;$GJTimeAll=$GJTime+$ZL_Hours;
			$sumGJTimeIpad = $sumGJTime;
			$gzlSign=1;
			$GJTime="<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($GJTimeAll)."</span>";
			$GJTimeIpad = $GJTimeAll;
			break;
		case "W"://相当于工作日的加点
			$sumGJTime+=$ZL_Hours;$GJTimeAll=$GJTime+$ZL_Hours;
			$sumGJTimeIpad = $sumGJTime;
			$gzlSign=1;
			$GJTime="<span title='包括直落：$ZL_Hours 工时' style='CURSOR: pointer;color: #009900;font-weight: bold;'>".($GJTimeAll)."</span>";
			$GJTimeIpad = $GJTimeAll;
			break;
		}
    }
}
?>