<?php   
$RowsHight=5;
$InvoiceHeadFontSize=9;
$TableFontSize=8;
$QtySUM=0;
$AmountSUM=0;
$oldPO="";
$OrderPOs="";

$SavePaymentTerm=$PaymentTerm;



$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$checkPI=mysql_query("SELECT Id FROM  $DataIn.yw3_pisheet  WHERE PI='$PI' ",$link_id);
if($checkRow = mysql_fetch_array($checkPI)){
	 $Log.="<div>PI添加失败! $Pi 存在相同名字</div><br>";
}
else{
	$delSql="DELETE FROM $DataIn.yw3_pisheet WHERE oId IN ($Ids)";
	//echo $delSql;
	$delRresult = @mysql_query($delSql,$link_id);
	$IdArr=explode(',',$Ids);
	for ($i=0;$i<count($IdArr);$i++){
	   $Id=$IdArr[$i]; 
	   $Leadtime=$LeadtimeArr[$Id]; 
	   if ($Leadtime==""){//来自未出生成PI
			  $idTempName="Leadtime_" . $Id;
			  $Leadtime=$$idTempName;
	   }
	   $RemarkStr="Remark".$Id;
	   $Remark=$$RemarkStr;
	   
	  $Leadtime=str_replace("*", "", $Leadtime);
      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Leadtime',1) AS Leadweek",$link_id));
      $Leadweek=$dateResult["Leadweek"];

	  $InsertSql="INSERT INTO $DataIn.yw3_pisheet SELECT NULL,'$CompanyId',Id,'$PI','$Leadtime','$Leadweek','$SavePaymentTerm','$Notes','$OtherNotes','$Terms','$ShipTo','$SoldTo','$condition','$Remark','$Date','$Operator','1','1','0','$Operator',NOW(),'$Operator',NOW() FROM $DataIn.yw1_ordersheet WHERE Id = $Id";

	   //echo $InsertSql;
	   $inRes=@mysql_query($InsertSql);
	}
	   
	//生成PI文件
	$CreateXmlFile="SAVE_PI"; 
	include "yw_pi_toxml.php";
	
	//更新未设置采购交期的已下采购单
	include "yw_pi_setcgdate.php";
	
	include "yw_piBlue_reset.php"; //生成PI
}

$IN_Recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>