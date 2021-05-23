<?php 
//电信-EWEN
//代码、数据共享-EWEN 2012-08-14
include "../model/modelhead.php";
//步骤2：
$Log_Item="点餐记录";			//需处理
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
$CheckmainResult=mysql_fetch_array(mysql_query("SELECT Id FROM $DataPublic.ct_myordermain WHERE Date='$Date'",$link_id));
$CheckId=$CheckmainResult["Id"];
if($CheckId==""){
      $IN_main="INSERT INTO $DataPublic.ct_myordermain(Id, Date, Bill, Remark)VALUES(NULL,'$Date','','')";
      $In_Result=@mysql_query($IN_main);
     $Mid=mysql_insert_id();
}
else $Mid=$CheckId;

$MenuArray=explode("^", $OrderMenu);
$count=count($MenuArray);
for ($i=0;$i<$count;$i++)
{
   $Menus=explode("|",$MenuArray[$i]);
   $CtId=$Menus[0];
   $MenuId=$Menus[1];
   $Price=$Menus[2];
   $Qty=$Menus[3];
   $Amount=$Price*$Qty;
   $inRecode="INSERT INTO $DataPublic.ct_myorder (Id, Mid,CtId, MenuId, Price, Qty, Amount,Remark,Estate, Locks, Date, Operator) VALUES (NULL,'$Mid','$CtId','$MenuId','$Price','$Qty','$Amount','$Remark','1','1','$DateTime','$Operator')";
$inAction=@mysql_query($inRecode);
}
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
