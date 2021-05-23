<?php 
//电信-zxq 2012-08-01
/*
$DataIn.cw6_orderinmain
$DataIn.cw6_orderinsheet
$DataIn.ch1_shipmain
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="收款记录";			//需处理
$fromWebPage=$funFrom."_cw";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$ALType="fromWebPage=$fromWebPage&Pagination=$Pagination&CompanyId=$CompanyId&cwSign=0";
//新增返回默认页面（参数只保留月份、分页、即可，其它均使用默认值，以便可以看到刚新增的记录）
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理
$Remark=FormatSTR($Remark);
//拆分明细数据
$PayAmount=0;
$Recodearray=explode("|",$AddIds);
$RecodeLenght=count($Recodearray);
//锁定表
$Handingfee=$Handingfee=""?0:$Handingfee;
//$sql=" LOCK TABLES $DataIn.cw6_orderinmain WRITE";$res=@mysql_query($sql);
$IN_recode="INSERT INTO $DataIn.cw6_orderinmain (Id,BankId,CompanyId,PreAmount,PayAmount,Handingfee,Remark,PayDate,Locks,Operator) 
VALUES (NULL,'$BankId','$CompanyId','0','$PayAmount','$Handingfee','$Remark','$PayDate','0','$Operator')";
$inRes=@mysql_query($IN_recode);
$Mid=mysql_insert_id();
$inResTemp=mysql_affected_rows();	
//$sql="UNLOCK TABLES";$res=@mysql_query($sql);
if($inRes && $inResTemp>0 && $Mid>0){
	$Log="收款主单成功入库！<br>";
	for($i=0;$i<$RecodeLenght;$i++){
		$FieldArray=explode("!",$Recodearray[$i]);
		$tempId=$FieldArray[0];
		$tempAmount=$FieldArray[1];$tempCwSign=$FieldArray[2];
		$PayAmount=$PayAmount+$tempAmount;
		$tempIds=$tempIds==""?$tempId:($tempIds.",".$tempId);
		$InsertSql.=$InsertSql==""?"INSERT INTO $DataIn.cw6_orderinsheet (Id,Mid,chId,Amount,Locks) VALUES (NULL,$Mid,'$tempId','$tempAmount','0')":",(NULL,$Mid,'$tempId','$tempAmount','0')";
		$UpdateSign.=$UpdateSign==""?"UPDATE $DataIn.ch1_shipmain SET cwSign=(CASE Id WHEN $tempId THEN $tempCwSign":" WHEN $tempId THEN $tempCwSign";
		}
	if($InsertSql!="" && $UpdateSign!=""){
		$UpdateSign.=" ELSE 1 END) WHERE Id IN ($tempIds)";
		//更新收款总额
		$upAmountSql="UPDATE $DataIn.cw6_orderinmain SET PayAmount='$PayAmount' WHERE Id='$Mid' LIMIT 1";
		$upAmountRes=@mysql_query($upAmountSql);		
		
		$InsertRes=@mysql_query($InsertSql);
		if($InsertRes && mysql_affected_rows()>0){
			$Log.="&nbsp;&nbsp;收款明细记录成功入库.<br>";
			$upResult = mysql_query($UpdateSign);
			if($upResult){
				$Log.="&nbsp;&nbsp;明细记录的收款标记成功.<br>";
				}
			else{
				$Log.="<div class=redB>&nbsp;&nbsp;明细记录的收款标记失败. $UpdateSign </div><br>";
				$OperationResult="N";
				}
			}
		else{
			$Log.="<div class=redB>&nbsp;&nbsp;收款明细记录入库失败. $InsertSql </div><br>";
			$OperationResult="N";
			//清除主单?
			}
		}
	else{//没有明细记录
		//清除主单?
		}//end if($InsertSql!="" && $UpdateSign!="")
	}
else{
	$Log="<div class=redB>收款主单添加失败！$IN_recode </div><br>";
	$OperationResult="N";
	}
//步骤4：
$InsertLog="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$InsertRes=@mysql_query($InsertLog);
include "../model/logpage.php";
?>
