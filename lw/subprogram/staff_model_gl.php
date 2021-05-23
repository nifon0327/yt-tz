<?php 
//工龄计算:参数-入职日期ComeIn 二合一已更新
$CominMonth=date("m",strtotime($ComeIn));					//入职当月月份
$ThisMonth=date("m");	

$sumY =0;
$sumM =0;
//年计算
if($ThisMonth<$CominMonth){//计薪月份少于进公司月份,有不足年，年数需减1
	$sumY=($sumY-1);
	$sumM=$ThisMonth+12-$CominMonth;
	}
else{
	$sumM=$ThisMonth-$CominMonth;
	}

if ($sumM<0){
	$sumM+=12;
	$sumY-=1;
}
//转输出字符
$Gl="";
$Gl_STR="";
if($sumY==0){
	if($sumM>0){
		$Gl="(".$sumM.")";
		$Gl_STR=$sumM."个月";
		}
	else{
		$Gl_STR="&nbsp;";
		}
	}
else{
	if($sumM>0){
		$Gl="<span class='redB'>".$sumY."</span>"."(".$sumM.")";
		$Gl_STR="<span class='redB'>".$sumY."</span>年".$sumM."个月";
		}
	else{
		$Gl="<span class='redB'>".$sumY."</span>";
		$Gl_STR="<span class='redB'>".$sumY."</span>年";
		}
	}

?>