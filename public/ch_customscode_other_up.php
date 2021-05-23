<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_other_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="海关编码的其它功能";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
//步骤3：需处理，更新操作

if($_POST['ListId']){//如果指定了操作对象
	$Counts=count($_POST['ListId']);
	$Ids="";
	for($i=0;$i<$Counts;$i++){
		$thisId=$_POST[ListId][$i];
		$Ids=$Ids==""?$thisId:$Ids.",".$thisId;
		}
	$TypeIdSTR=" AND A.ProductId IN ($Ids)";
	}
switch($Action){
	case "1"://配件替换
		$Log_Funtion="海关编码中的商品名称更新";
		if($TypeIdSTR!=""){
			$up_Sql = "UPDATE $DataIn.customscode  A  SET A.GoodsName='$newGoodsName',A.modified='$DateTime',A.modifier='$Operator' 
			WHERE 1 $TypeIdSTR ";
			$up_Result = mysql_query($up_Sql);
			if($up_Result){
				$Log.="$TypeIdSTR 海关编码中的商品名称更新成功!";
				}
			else{
				$Log.="<div class='redB'>$TypeIdSTR 海关编码中的商品名称更新失败! $up_Sql </div>";
				$OperationResult="N";
				}
		}else{
			$Log.="<div class='redB'>未选择产品</div>";
			$OperationResult="N";
		}
	break;
	}
	
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>

