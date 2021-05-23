<?php
//2014-01-07 ewen 修正OK
$group1_sjAmount=$group1_ygAmount=$group2_sjAmount=$group2_ygAmount=$kqWorkTime=$WorkNumber=$kqWorkTime1=$kqWorkTime2=$WorkNumber1=$WorkNumber2=0;
//$SearchDay=" AND S.Date='2014-01-01' AND S.GroupId='821'";
//工资已确定和工资未确定的情况下
//计算该组考勤员工整月的预估工资和实际工资(按天累计)
		$Whours=zerotospace($myRow["Whours"]);		//实到工时
		$Ghours=zerotospace($myRow["Ghours"]);				//1.5倍薪工时-标
		$GOverTime=zerotospace($myRow["GOverTime"]);	//1.5倍薪工时-超
		$GDropTime=zerotospace($myRow["GDropTime"]);	//1.5倍薪工时-直
		
		$Xhours=zerotospace($myRow["Xhours"]);				//2倍薪工时-标
		$XOverTime=zerotospace($myRow["XOverTime"]);	//2倍薪工时-超
		$XDropTime=zerotospace($myRow["XDropTime"]);	//2倍薪工时-直
		
		$Fhours=zerotospace($myRow["Fhours"]);				//3倍薪工时-标
		$FOverTime=zerotospace($myRow["FOverTime"]);	//3倍薪工时-超
		$FDropTime=zerotospace($myRow["FDropTime"]);	//3倍薪工时-直
if($CheckTheDay<"2014-08"){//4月份之前按原来的方式去计算
$checkwtimeSql=mysql_query("SELECT S.Number,IFNULL(SUM(K.SdTime+K.JbTime*1.5+K.JbTime2*2+K.JbTime3*3) ,0) AS WorkTime  
FROM $DataIn.sc1_memberset S
LEFT JOIN $DataIn.kqdaytj K ON K.Number=S.Number 
WHERE 1 $SearchDay  AND S.KqSign=1 AND K.Date=S.Date GROUP BY S.Number",$link_id);
}
else{
	$checkwtimeSql=mysql_query("SELECT K.Number, IFNULL(SUM(K.Whours+(Ghours+GOverTime+GDropTime)*1.5+(K.Xhours+K.XOverTime+K.XDropTime)*2+
		(K.Fhours+K.FOverTime+K.FDropTime)*3),0) AS WorkTime  
		FROM  $DataPublic.staffmain M 
		 LEFT JOIN $DataIn.kqdata K ON M.Number=K.Number 
       WHERE M.KqSign=1 AND M.GroupId='$GroupId'  AND  K.Month='$CheckTheDay'   GROUP BY K.Number",$link_id);
}
if($checkwtimeRow = mysql_fetch_array($checkwtimeSql)) {	
	do{
   		$WorkTime=$checkwtimeRow["WorkTime"];
		$Number=$checkwtimeRow["Number"];
		$TempSXSTR="SX".strval($Number);
		$group1_sjAmount+=$WorkTime*$$TempSXSTR;			//员工的实际工资累计
		$group1_ygAmount+=$WorkTime*$OneHourSalaryt;		//员工的预估工资累计
		$kqWorkTime1+=$WorkTime;	//考勤工时累计
		$WorkNumber1++;					//考勤员工数累计
      	}while($checkwtimeRow = mysql_fetch_array($checkwtimeSql));
	}

//计算该组非考勤员工整月的预估工资和实际工资(按天累计)
$SearchDay=$SearchDay3==""?$SearchDay:$SearchDay3;
$checkwtimeSql=mysql_query("SELECT S.Number,IFNULL(count(*)*10,0) AS WorkTime  
FROM $DataIn.sc1_memberset S
WHERE 1 $SearchDay  AND S.KqSign>1 GROUP BY S.Number",$link_id);
if($checkwtimeRow = mysql_fetch_array($checkwtimeSql)) {	
	$p=1;
	do{
   		$WorkTime=$checkwtimeRow["WorkTime"];
		$Number=$checkwtimeRow["Number"];
		$TempSXSTR="SX".strval($Number);
		$WorkTime=$SearchDay3==""?$WorkTime:($checkDays*10-$WorkTime);			//如果是辅助组员工，则上班天数为：当月总天数－当月调至其他组天数
		if($XZSign==1 && $$TempSXSTR==0){//如果该员工当月无工资，则不统计预估和实际支出
			$group2_sjAmount+=0;
			$group2_ygAmount+=0;
			}
		else{
			$group2_sjAmount+=$WorkTime*$$TempSXSTR;			//员工的实际工资累计
			$group2_ygAmount+=$WorkTime*$OneHourSalaryt;		//员工的预估工资累计
			}
		$kqWorkTime2+=$WorkTime;	//考勤工时累计
		$WorkNumber2++;					//考勤员工数累计
      	}while($checkwtimeRow = mysql_fetch_array($checkwtimeSql));
	}
?>
