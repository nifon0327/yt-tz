<?php 
include "../model/modelhead.php";
//步骤2：
$Log_Item="上网设备MAC地址资料";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
if($IPad=="" && $IPhone=="" && $Mac=="" && $PC=="" && $Other==""){
	$Log.="<div class=redB>不能全部为空</div><br>";
}
else{
$CheckNumResult=mysql_fetch_array(mysql_query("SELECT MAX(Num) AS MaxNum FROM $DataPublic.it_mac",$link_id));
$MaxNum=$CheckNumResult["MaxNum"];
if($MaxNum==""){
$MaxNum=1;
}
else{
$MaxNum+=1;
}
$inRecode="INSERT INTO $DataPublic.it_mac (Id,Num,Name,IPad,IPhone,Mac,PC,Other,Date,Operator) 
  VALUES (NULL,'$MaxNum','$Name','$IPad','$IPhone','$Mac','$PC','$Other','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
$Id=mysql_insert_id();
if ($inAction){ 
	 $Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log.="<div class=redB>$TitleSTR 失败!$inRecode</div><br>";
	$OperationResult="N";
	} 
}

$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
