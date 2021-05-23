<?php 
include "../model/modelhead.php";
//步骤2：
$Log_Item="配件分类";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$TypeName=FormatSTR($TypeName);
$chinese=new chinese;
$Letter=substr($chinese->c($TypeName),0,1);
$Date=date("Y-m-d");


$Sql = mysql_query("SELECT MAX(TypeId) AS Mid FROM $DataIn.StuffType",$link_id);
$TypeId=mysql_result($Sql,0,"Mid");
if($TypeId){
	$TypeId=$TypeId+1;}
else{
	$TypeId=9001;
	}
	
$DevelopField=explode("|",$DevelopId);	
$DevelopGroupId=$DevelopField[0]==""?0:$DevelopField[0];
$DevelopNumber=$DevelopField[1]==""?0:$DevelopField[1];


$PicJobid="0|0";$GicJobid="0|0"; //已弃用
$JNField=explode("|",$PicJobid);	
$PicJobid=$JNField[0];
$PicNumber=$JNField[1];

$GicField=explode("|",$GicJobid);	
$GicJobid=$GicField[0];
$GicNumber=$GicField[1];

$jhDays=0;$AQL=0;
$ActionId=$ActionId==""?0:$ActionId;
$WorkShopId=$WorkShopId==""?0:$WorkShopId;

$inRecode="INSERT INTO $DataIn.StuffType (Id,Letter,TypeId,mainType,TypeName,ActionId,WorkShopId,NameRule,Position,AQL,BlType,ForcePicSign,PicJobid,PicNumber,GicJobid,GicNumber,BuyerId,DevelopGroupId,DevelopNumber,jhDays,Estate,Locks,Date,Operator) 
	VALUES (NULL,'$Letter','$TypeId','$mainType','$TypeName','$ActionId','$WorkShopId','$NameRule','$Position','$AQL','0','$ForcePicSign','$PicJobid','$PicNumber','$GicJobid','$GicNumber','$BuyerId','$DevelopGroupId','$DevelopNumber','$jhDays','1','0','$Date','$Operator')";
$inAction=@mysql_query($inRecode);

if ($inAction){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	} 
//步骤4：
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
