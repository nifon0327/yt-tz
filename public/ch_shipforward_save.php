<?php 
//电信-zxq 2012-08-01
//步骤1： $DataIn.ch3_forward 二合一已更新
include "../model/modelhead.php";
//步骤2：
$Log_Item="Forward杂费";			//需处理
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

if($ShipType ==1 ){
	$CFSCharge = $CFSCharge1==""?0.00:$CFSCharge1;
	$THCCharge = $THCCharge1==""?0.00:$THCCharge1;
	$WJCharge  = $WJCharge1==""?0.00:$WJCharge1;
	$SXCharge  = $SXCharge1==""?0.00:$SXCharge1;
	$ENSCharge = $ENSCharge1==""?0.00:$ENSCharge1;
	$BXCharge  = 0.00;
	$GQCharge  = $GQCharge1==""?0.00:$GQCharge1;
	$DFCharge  = 0.00;
	$TDCharge  = $TDCharge1==""?0.00:$TDCharge1;
	$OtherCharge = $OtherCharge1==""?0.00:$OtherCharge1;
}else if ($ShipType ==2){
	$CFSCharge = $CFSCharge2==""?0.00:$CFSCharge2;
	$THCCharge = 0.00;
	$WJCharge  = $WJCharge2==""?0.00:$WJCharge2;
	$SXCharge  = $SXCharge2==""?0.00:$SXCharge2;
	$ENSCharge = $ENSCharge2==""?0.00:$ENSCharge2;
	$BXCharge  = $BXCharge2==""?0.00:$BXCharge2;
	$GQCharge  = 0.00;
	$DFCharge  = $DFCharge2==""?0.00:$DFCharge2;
	$TDCharge  = $TDCharge2==""?0.00:$TDCharge2;
	$OtherCharge = $OtherCharge2==""?0.00:$OtherCharge2;
}


$Volume = $Volume ==""?0.00:$Volume;
$HKVolume = $HKVolume ==""?0.00:$HKVolume;
$VolumeKG = $VolumeKG ==""?0.00:$VolumeKG;
$HKVolumeKG = $HKVolumeKG ==""?0.00:$HKVolumeKG;

$inRecode="INSERT INTO $DataIn.ch3_forward 
(Id,Mid,chId,TypeId,CompanyId,PayType,HoldNO,ForwardNO,BoxQty,mcWG,forwardWG,Volume,HKVolume,VolumeKG,HKVolumeKG,CFSCharge,THCCharge,WJCharge,SXCharge,ENSCharge,BXCharge,GQCharge,DFCharge,TDCharge,OtherCharge,Amount,InvoiceDate,ETD,ShipType,Remark,Estate,Locks,Date,Operator) VALUES (NULL,'0','$chId','$TypeId','$CompanyId','$PayType','$HoldNO','$ForwardNO','$BoxQty','$mcWG','$forwardWG','$Volume',
'$HKVolume','$VolumeKG','$HKVolumeKG','$CFSCharge','$THCCharge','$WJCharge','$SXCharge','$ENSCharge','$BXCharge','$GQCharge','$DFCharge','$TDCharge','$OtherCharge','$Amount','$InvoiceDate','$ETD','$ShipType','$Remark','1','1','$Date','$Operator')";
$inAction=@mysql_query($inRecode);
$Pid=mysql_insert_id();
if ($inAction && mysql_affected_rows()>0){ 
	$Log="$TitleSTR 成功!<br>";
	} 
else{
	$Log=$Log."<div class=redB>$TitleSTR 失败! $inRecode </div><br>";
	$OperationResult="N";
	}

  for($i=0;$i<$count;$i++){
     $arrayId=$chArray[$i];
	 if($arrayId!="" && $Pid!=0 && $Pid!=""){
	  $InResult="INSERT INTO $DataIn.ch3_forward_Invoice(Id,Mid,chId,TypeId,Date,Operator)
	             VALUES(NULL,'$Pid','$arrayId','$TypeId','$Date','$Operator')";
	  $InRow=@mysql_query($InResult);
	  }
   }//end for

//步骤4：
$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
