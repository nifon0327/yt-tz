<?php 
//电信-zxq 2012-08-01
include "../model/modelhead.php";
//步骤2：
$Log_Item="海关编码新增记录";			//需处理
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination";
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Date=date("Y-m-d");

if($_POST['ListId']){//如果指定了操作对象
	$Counts=count($_POST['ListId']);
	for($i=0;$i<$Counts;$i++){
		$ProductId=$_POST['ListId'][$i];
	
		$mySql=mysql_query("SELECT * FROM $DataIn.customscode WHERE ProductId='$ProductId'",$link_id);
		if(mysql_num_rows($mySql)>0){
		  $Log.="<div class=redB>该产品的海关编码已经存在!请更新</div><br>";
		  $OperationResult="N";
		}
		else{
		   $inRecode="INSERT INTO $DataIn.customscode (Id,ProductId,HSCode,GoodsName,Remark,Date,Estate,Locks,Operator,
		   PLocks,creator,created,modifier,modified)VALUES(NULL,'$ProductId','$HSCode','$GoodsName','$Remark','$Date','1','0',
		    '$Operator','0','$Operator','$DateTime','$Operator','$DateTime')";
		    $inAction=@mysql_query($inRecode);
		    if ($inAction){ 
			   $Log.="$TitleSTR 成功!<br>";
			    } 
		    else{
			    $Log.="<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
			    $OperationResult="N";
			   } 
		}
	}
}
//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>