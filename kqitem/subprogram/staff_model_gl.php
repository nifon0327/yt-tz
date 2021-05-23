<?php 
//电信-ZX  2012-08-01
$chooseMonth=$chooseMonth==""?date("Y-m"):$chooseMonth;

//工龄计算:参数-入职日期ComeIn、当月chooseMonth  二合一已更新
$ThisEndDay=date("Y-m-t",strtotime($chooseMonth."-01"));	//薪资当月最后一天
$sumY=substr($chooseMonth,0,4)-substr($ComeIn,0,4);			//入职至薪资的年数
$ThisMonth=date("m",strtotime($chooseMonth."-01"));			//薪资当月月份
$CominMonth=date("m",strtotime($ComeIn));					//入职当月月份

//有效扣除工龄月份数
$checkKCGLSql=mysql_query("SELECT SUM(Months) AS kcgl FROM $DataPublic.rs_kcgl WHERE Number=$Number AND Month<='$chooseMonth'",$link_id);
if ($checkKCGLSql){
	if($checkKCGLRow=mysql_fetch_array($checkKCGLSql)){
		$kcgl=$checkKCGLRow["kcgl"];	//需扣除的工龄月份数
		}
}
$kcgl=$kcgl==""?0:$kcgl;
//年计算
if($ThisMonth<$CominMonth){//计薪月份少于进公司月份,有不足年，年数需减1
	$sumY=($sumY-1);
	$sumM=$ThisMonth+12-$CominMonth;
	}
else{
	$sumM=$ThisMonth-$CominMonth;
	}

//月计算
if(date("d",strtotime($ComeIn))==1){
	//$sumM=$sumM+1;
	}
//需扣除的工龄月
if($sumM<=$kcgl && $kcgl>0){
	$sumY=$sumY-1;	//工龄减一年
	$sumM=$sumM+12-$kcgl;
	if ($sumM==12) {
		$sumY=$sumY+1;
		$sumM=0;
		}
	}
else{
	$sumM=$sumM-$kcgl;
	}
//天数工龄
if($sumY==0 && $sumM==2 && $CominMonth!=$ThisMonth){
	$sumD=date("d",strtotime($ThisEndDay))-date("d",strtotime($ComeIn))+1-($kcgl*30);//有效在职天数
	}
//月总天数
$theMonthDays=date("t",strtotime($ThisEndDay));

//转输出字符
$Gl="";
$Gl_STR="";

//用于ipad接口的变量
$glPad = "";

if($sumY==0){
	if($sumM>0){
		$Gl="(".$sumM.")";
		$Gl_STR=$sumM."个月";
		$glPad = $sumM."个月";
		}
	else{
		$Gl_STR="&nbsp;";
		}
	}
else{
	if($sumM>0){
		$Gl="<span class='redB'>".$sumY."</span>"."(".$sumM.")";
		$Gl_STR="<span class='redB'>".$sumY."</span>年".$sumM."个月";
		$glPad = $sumY."年".$sumM."个月";
		}
	else{
		$Gl="<span class='redB'>".$sumY."</span>";
		$Gl_STR="<span class='redB'>".$sumY."</span>年";
		$glPad = $sumY."年";
		}
	}

/*
//计算在职时间
		$ThisDate=date("Y-m-d");
		$ThisYM=date("Ym");
		$InYM=date("Ym",strtotime($ComeIn));
		//比较
		$ThisDay=date("d",strtotime($ThisDate));
		$InDay=date("d",strtotime($ComeIn));
		if($InYM<=$ThisYM-1){//在职时间在一个月或1个月以上
			if($InYM==$ThisYM-1){//如果是入职的前一个月，则要对比天数来确定是否足月
				if($ThisDay>=$InDay){//满一个月
					$gl_STR="1个月";
					}
				}
			else{//一个月以上
				/////////////////////////////////////////////////
				$Years=date("Y",strtotime($ThisDate))-date("Y",strtotime($ComeIn));
				$ThisMonth=date("m",strtotime($ThisDate));	//当前月份
				$CominMonth=date("m",strtotime($ComeIn));	//入职月份
				//年计算
				if($ThisMonth<$CominMonth){//当前月份少于入职月份，年数需减一，即有不足年
					$Years=($Years-1);
					$gl_STR=$Years<=0?"&nbsp;":$Years."年";
					if($ThisDay>=$InDay){//当前日》入职日
						$MonthSTR=$ThisMonth+12-$CominMonth;
						}
					else{//有一个月不足月
						$MonthSTR=$ThisMonth+11-$CominMonth;
						}
					}
				else{
					if($ThisMonth==$CominMonth){
						if($ThisDay<$InDay){//有不足年,年数减1，月份数为11
							$Years=$Years-1;
							$MonthSTR=11;
							}
						$gl_STR=$Years<=0?"&nbsp;":$Years."年";
						}
					else{					//如果当前月份比入职月份大,则足年
						$gl_STR=$Years<=0?"&nbsp;":$Years."年";
						if($ThisDay>=$InDay){//当前日》入职日
							$MonthSTR=$ThisMonth-$CominMonth;
							}
						else{
							$MonthSTR=$ThisMonth-$CominMonth-1;
							}
						}
					}
				$MonthSTR=$MonthSTR>0?$MonthSTR."个月":"";
				$gl_STR=$gl_STR.$MonthSTR;
				/////////////////////////////////////////////////
				}
			}
*/
?>