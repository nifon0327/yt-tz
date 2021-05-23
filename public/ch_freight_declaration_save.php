<?php 
//电信-zxq 2012-08-01
//步骤1： $DataIn.ch4_freight  二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="中港报关费";			//需处理
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
$Date=date("Y-m-d");
$chArray=explode("^^",$chId); 
$count=count($chArray);
if($count==1){$chId=$chArray[0];}
else{$chId=$chArray[1];}
$declarationCharge=$declarationCharge==""?0:$declarationCharge;
$checkCharge=$checkCharge==""?0:$checkCharge;
$depotCharge=$depotCharge==""?0:$depotCharge;
$PayType=$PayType==""?0:$PayType;
$Termini ="";
$Price ="0.00";
$inRecode="INSERT INTO $DataIn.ch4_freight_declaration 
(Id,Mid,chId,TypeId,CompanyId,PayType,Termini,ExpressNO,BoxQty,mcWG,Volume,CarType,Price,Amount,depotCharge,declarationCharge,checkCharge,carryCharge,xyCharge,wfqgCharge,ccCharge,djCharge,stopcarCharge,expressCharge,otherCharge,Remark,Estate,Locks,Date,Operator) VALUES (NULL,'0','$chId','$TypeId','$CompanyId','$PayType','$Termini','$ExpressNO','$BoxQty','$mcWG','$Volume','$CarType','$Price',
'$Amount','$depotCharge','$declarationCharge','$checkCharge','$carryCharge','$xyCharge','$wfqgCharge','$ccCharge','$djCharge','$stopcarCharge','$expressCharge','$otherCharge','$Remark','1','1','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
$Pid=mysql_insert_id();
if ($inAction && mysql_affected_rows()>0){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode</div><br>";
	$OperationResult="N";
	} 
  for($i=0;$i<$count;$i++){
     $arrayId=$chArray[$i];
	 if($arrayId!="" && $Pid!=0 && $Pid!=""){
	  $InResult="INSERT INTO $DataIn.ch4_freight_Invoice(Id,Mid,chId,TypeId,Date,Operator)
	             VALUES(NULL,'$Pid','$arrayId','$TypeId','$Date','$Operator')";
	  $InRow=@mysql_query($InResult);
	     if($InRow){
		      $Log.=" $i--相应的Invoice添加成功!<br>";
		       }
		   else{
		      $Log.=" $i--相应的Invoice添加失败! $InResult<br>";
		       }
	  }
   }//end for

//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>