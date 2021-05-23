<?php 
/*
$DataIn.kqdata 二合一已更新
更新：取消所有罚扣设定
加班费计算
加班费=1.5倍工时*时薪*1.5
      -事假工时*(时薪+(生活补助+住房补助+岗位津贴)/$DefaultWtime) 超3天以上扣补助和津贴
	  -病假工时*时薪*40%	
	  -无薪假工时*时薪		公司放假
	  -缺勤工时*时薪	迟到早退缺勤
	  -旷工工时*时薪*(生活补助+住房补助+主管津贴+岗位津贴)/$DefaultWtime)
	  (+2倍工时*时薪*2+3倍工时*时薪*3   另外计算)
*/

//计算： 加班费/夜宵补助/考勤扣款/无薪扣款;加班费不足时，列入考勤扣款
$kqdata_Result = mysql_query("SELECT * FROM $DataIn.kqdata WHERE Number=$Number and Month='$chooseMonth'",$link_id);
if($kqdata_Row = mysql_fetch_array($kqdata_Result)){
	//已经有记录
	$Dhours		=$kqdata_Row["Dhours"];		//当月应到工时
	$Whours		=$kqdata_Row["Whours"];		//当月实到工时
	$Ghours		=$kqdata_Row["Ghours"];		//加点工时
	$Xhours		=$kqdata_Row["Xhours"];		//加班工时
	$Fhours		=$kqdata_Row["Fhours"];		//法定假日工时
	$InLates	=$kqdata_Row["InLates"];	//迟到次数
	$OutEarlys	=$kqdata_Row["OutEarlys"];	//早退次数
	$SJhours	=$kqdata_Row["SJhours"];	//事假
	$BJhours	=$kqdata_Row["BJhours"];	//病假
	$WXJhours	=$kqdata_Row["WXJhours"];	//无薪假
	$QQhours	=$kqdata_Row["QQhours"];	//因迟到等缺勤工时
	$YBs		=$kqdata_Row["YBs"];		//夜班次数		
	$WXhours	=$kqdata_Row["WXhours"];	//无效工时:未入职或已离职
	$KGhours	=$kqdata_Row["KGhours"];	//旷工工时
	//时薪
	$DefaultWtime=$Whours==0?$Dhours:174;
	$oneHours=sprintf("%.2f",$Dx/$DefaultWtime);//21.75天
	//是否扣补助的标记：如果事假时间+旷工时间>32小时的，需扣补助
	$BZSTR=($SJhours+$KGhours)>31?($Shbz+$Zsbz+$Gwjt):0;
	//$BZSTR=($SJhours+$KGhours+$WXhours)>31?($Shbz+$Zsbz+$Gwjt):0;
	//无薪扣款：未在职的工时
	$Wxkk=SpaceValue0($WXhours*(($Dx+$Shbz+$Zsbz+$Gwjt)/$DefaultWtime));//离职工时扣款
	//夜宵补助	
	$Yxbz=$YBs*5;
	//加班费 +intval($Xhours*2*$oneHours)+intval($Fhours*3*$oneHours)
	$Jbf=intval($Ghours*$jbAmount);		
	//考勤扣款	：
	//echo"($InLates+$OutEarlys)*5+$Wxkk+intval($SJhours*($oneHours+$BZSTR/$DefaultWtime))+intval($BJhours*$oneHours*0.4)+intval($WXJhours*$oneHours)+intval($QQhours*$oneHours)+intval($KGhours*($oneHours*1+($Shbz+$Zsbz+$Gwjt)/$DefaultWtime))";
	$Kqkk=($InLates+$OutEarlys)*5
		  +$Wxkk
		  +intval($SJhours*($oneHours+$BZSTR/$DefaultWtime))
		  +intval($BJhours*$oneHours*0.4)
		  +intval($WXJhours*$oneHours)
		  +intval($QQhours*$oneHours)//*1.5
		  +intval($KGhours*($oneHours*1+($Shbz+$Zsbz+$Gwjt)/$DefaultWtime));//=无效工时+迟到+早退
		 /*
		 echo"($InLates+$OutEarlys)*5
		  +$Wxkk
		  +intval($SJhours*($oneHours+$BZSTR/$DefaultWtime))
		  +intval($BJhours*$oneHours*0.4)
		  +intval($WXJhours*$oneHours)
		  +intval($QQhours*$oneHours)
		  +intval($KGhours*($oneHours*1+($Shbz+$Zsbz+$Gwjt)/$DefaultWtime))";*/
	}
else{
	$Jbf=0;$Kqkk=0;$Yxbz=0;
	}
?>