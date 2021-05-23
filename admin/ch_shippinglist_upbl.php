<?php   
//电信-zxq 2012-08-01
/*
$DataIn.ch1_shipmain
$DataIn.ch1_shipsheet
$DataIn.ch5_sampsheet
$DataIn.yw1_ordersheet
二合一已更新
*/
include "../model/modelhead.php";
//步骤2：
$Log_Item="出货资料";			//需处理
$fromWebPage=$funFrom."_noll";
$nowWebPage=$funFrom."_upbl";
$_SESSION["nowWebPage"]=$nowWebPage;
$Log_Funtion="保存";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Date=date("Y-m-d");
$sDate=$Date;
$chooseDate=substr($rkDate,0,7);
$ALType="CompanyId=$CompanyId";
	$CheckPId=mysql_fetch_array(mysql_query("SELECT POrderId FROM $DataIn.yw1_ordersheet  WHERE Id='$Id'",$link_id));
	$POrderId=$CheckPId["POrderId"];
if ($POrderId>0){
	   ///////////补增备料主单
	   $checkResultB = mysql_query("SELECT Id FROM $DataIn.yw9_blsheet WHERE POrderId='$POrderId'  LIMIT 1",$link_id);
	  if($checkRowB = mysql_fetch_array($checkResultB)){
		  //更新备料日期
	      $UpdateSqlB="Update $DataIn.yw9_blsheet SET blDate='$sDate',Estate='1',Operator='$Operator' WHERE POrderId='$POrderId'";
          $UpdateResultB = mysql_query($UpdateSqlB);
		   if($UpdateResultB){
	          $Log.="<div class=greenB>订单:" . $POrderId . "备料日期更新成功!</div><br>";
	          } 
            else{
  	         $Log.="<div class=redB>订单:" . $POrderId . "备料日期更新失败!</div><br>";
	          }
	       }
	  else{
        //////////新增备料主单信息
       //检查主ID
	     $checkNum=mysql_query("",$link_id);
		 $maxSql = mysql_query("SELECT IFNULL(MAX(Num),0) AS Num FROM $DataIn.yw9_blsheet",$link_id);
		 $Num=mysql_result($maxSql,0,"Num");
		 $Num+=1;
	     $inRecodeB="INSERT INTO $DataIn.yw9_blsheet(Id,Num,POrderId,blDate,Estate,Date,Operator) VALUES (NULL,'$Num','$POrderId','$sDate','1','$Date','$Operator')";
		$inResultB=@mysql_query($inRecodeB);
		if($inResultB){
			$Log.="&nbsp;&nbsp;订单:" . $POrderId . "备料主单生成成功.</br>";
			}
		else{
			$Log.="<div class='redB'>&nbsp;&nbsp;订单:" . $POrderId . "备料主单生成失败.$inRecode </div></br>";
			}
		 } 
    }
else{
	 $Log.="<div class='redB'>&nbsp;&nbsp;订单:" . $POrderId . "备料主单生成失败. </div></br>"; 
    }
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion',\"$Log\",'$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>