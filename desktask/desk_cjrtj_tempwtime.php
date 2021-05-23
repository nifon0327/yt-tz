<?php   
//临时工薪标准电信---yang 20120801
if ($checkSXFlag!=1){  //首次加载
     $checkSX=mysql_fetch_array(mysql_query("SELECT (Value/2) AS tempSX FROM $DataPublic.cw3_basevalue WHERE Id=3",$link_id));
    $tempygSX=sprintf("%2.f",$checkSX["tempSX"]);  //时薪 
    $checkSXFlag=1;
}

if ($checkTWFlag=="M"){
     $cwtRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(K.SdTime+K.JbTime*1.5+K.JbTime2*2+K.JbTime3*3),0) AS WorkTime,SUM(K.Ybs) AS Ybs  FROM  $DataIn.kqdaytemptj K  
WHERE  DATE_FORMAT(K.Date,'%Y-%m')='$CheckTheDay' GROUP BY DATE_FORMAT(K.Date,'%Y-%m')",$link_id));
	 $WorkTime=$cwtRow["WorkTime"];
	 $Ybs=$cwtRow["Ybs"];
	 $tempygAmount=$WorkTime*$tempygSX+$Ybs*5;
     $tempygAmount=sprintf("%.0f",$tempygAmount);//临时工预估支出
    }
else{
    $kqWorkTime=0;
    $kqygAmount=0;
    $WorkNumber=0;
    $checkwtimeSql=mysql_query("SELECT K.Number,IFNULL(SUM(K.SdTime+K.JbTime*1.5+K.JbTime2*2+K.JbTime3*3) ,0) AS WorkTime,SUM(K.Ybs) AS Ybs    
FROM  $DataIn.kqdaytemptj K  
WHERE 1 $SearchDay GROUP BY K.Number",$link_id);
    if($checkwtimeRow = mysql_fetch_array($checkwtimeSql)) {
      do{
		$Number=$checkwtimeRow["Number"];
        $WorkTime=$checkwtimeRow["WorkTime"];
		$Ybs=$checkwtimeRow["Ybs"];
		$TempSX=$WorkTime*$tempygSX+$Ybs*5;
		$kqygAmount+=$TempSX;
		$kqWorkTime+=$WorkTime;
		$WorkNumber++;
       }while($checkwtimeRow = mysql_fetch_array($checkwtimeSql));
     }
}
?>
