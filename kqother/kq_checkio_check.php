<?php 
//电信-EWEN
$CheckSql="SELECT Number,SdTime,JbTime,JbTime2,JbTime3,Date FROM $CheckTable
              WHERE Date='$CheckDate' AND Number='$Number'";
$CheckResult=mysql_query($CheckSql,$link_id);
if($CheckRow=mysql_fetch_array($CheckResult)){
    $NumberColor="";
	$SdTime=$CheckRow["SdTime"];
    $JbTime=$CheckRow["JbTime"];
    $JbTime2=$CheckRow["JbTime2"];
	$JbTime3=$CheckRow["JbTime3"];
	$SdTime=zerotospace($SdTime);
	$JbTime=zerotospace($JbTime);
	$JbTime2=zerotospace($JbTime2);
	$JbTime3=zerotospace($JbTime3);
	if($SdTime!=$WorkTime || $JbTime!=$GJTime || $JbTime2!=$XJTime || $JbTime3!=$FJTime){
	  $NumberColor="bgcolor='#FF0033'";//作修改的红色显
	  $CheckNumber.=$Number.",";
	  }
    }
else{
     if($WorkTime=="&nbsp;" && $GJTime=="&nbsp;" && $XJTime=="&nbsp;" && $FJTime=="&nbsp;"){
          $NumberColor="";
		  }
	 else{
		  $NumberColor="bgcolor='#FFCC00'";//没保存的黄色显示
		  $CheckNumber.=$Number.",";
		 }
    }

?>