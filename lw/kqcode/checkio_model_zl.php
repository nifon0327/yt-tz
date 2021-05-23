<?php 
//内部模式:OK ewen2013-07-30
$xzlSign=$gzlSign=$fzlSign=0;
$ZLG=$ZLX=$ZLF="&nbsp;";
$ZL_Result = mysql_query("SELECT sum(Hours) as Hours FROM $DataPublic.kqzltime  WHERE Number=$Number and Date='$CheckDate'",$link_id);
if($ZL_Row = mysql_fetch_array($ZL_Result)){
	//要判断假日加班倍数
	$ZL_Hours=$ZL_Row["Hours"];
if($ZL_Hours!=0){// 有直落，独立计算
	switch($DateType){
		case "X":
		case "Y":
			$ZLX=$ZL_Hours;
			$sumZLX+=$ZLX;
			$xzlSign=1;//休息日直落标记
			break;
		case "F":
			if($jbTimes==3){
				$ZLF=$ZL_Hours;
				$sumZLF+=$ZLF;
				$xzlSign=1;//休息日直落标记
				}
			else{
				$ZLX=$ZL_Hours;
				$sumZLX+=$ZLX;
				$xzlSign=1;//休息日直落标记
				}
			break;
		default:
			$ZLG=$ZL_Hours;
			$sumZLG+=$ZLG;
			$gzlSign=1;//工作日直落标记
			break;
		}
    }
}
?>