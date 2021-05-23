<?php   
//电信-EWEN
/*
更新:加入清除生产记录动作 2010.12.08
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="产品订单";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Date=date("Y-m-d");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;
if($POrderId!=""){
	if($Attached!=""){
	    $FilePath="../download/orderdelcause/";
		if(!file_exists($FilePath)){
			makedir($FilePath);
			}
		$FileType=substr("$Attached_name", -4, 4);
		$OldFile=$Attached;
		$PreFileName=$POrderId.$FileType;
		$uploadInfo=UploadPictures($OldFile,$PreFileName,$FilePath);
		$Attached=$uploadInfo;
		$sheetResult = mysql_query("SELECT * FROM $DataIn.yw1_ordersheet WHERE POrderId='$POrderId' ORDER BY Id DESC",$link_id);		//读取订单信息
		if($sheetRow = mysql_fetch_array($sheetResult)){
			$OrderNumber=$sheetRow["OrderNumber"];
			$OrderPO=$sheetRow["OrderPO"];
			$ProductId=$sheetRow["ProductId"];
			$Qty=$sheetRow["Qty"];
	        $Price=$sheetRow["Price"];
			$inRecode="INSERT INTO $DataIn.yw1_orderdeleted(Id,OrderNumber,OrderPO ,POrderId,ProductId,Qty,Price,delType,Attached,Estate,Remark,Date,Operator )VALUES (
NULL ,'$OrderNumber','$OrderPO','$POrderId','$ProductId','$Qty','$Price', '$delType','$Attached','1','$Remark','$DateTime','$Operator')";
               $inAction=@mysql_query($inRecode);
               if ($inAction && mysql_affected_rows()>0){ 
	                $Log.="&nbsp;&nbsp; $x - 添加删除订单原因相关信息成功!等待业务经理审核<br>";
	                
	                //系统自动锁定订单，防止继续下采购单操作
		                $Check7Sql=mysql_query("SELECT E.Id FROM $DataIn.yw2_orderexpress E LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=E.POrderId WHERE S. POrderId='$POrderId' AND E.Type='2'",$link_id);
					if(mysql_num_rows($Check7Sql)<=0){
					   if ($DataIn=='ac'){
						   $inRecode="INSERT INTO $DataIn.yw2_orderexpress(Id,	POrderId, Type,Remark,ReturnReasons,Estate,Date,Operator,OPdatetime) SELECT NULL,POrderId,'2','删单时系统自动锁定','','0','$Date','$Operator','$DateTime','0','0','$Operator',NOW(),'$Operator',NOW() FROM $DataIn.yw1_ordersheet WHERE  POrderId='$POrderId'";
						}
						else{
							$inRecode="INSERT INTO $DataIn.yw2_orderexpress(Id,	POrderId, Type,Remark,ReturnReasons,Estate,Date,Operator,OPdatetime) 
                            SELECT NULL,POrderId,'2','删单时系统自动锁定','','0','$Date','$Operator','$DateTime' FROM $DataIn.yw1_ordersheet WHERE  POrderId='$POrderId'";
						}
						$inResult=@mysql_query($inRecode);
						if($inResult){
							$Log.="&nbsp;&nbsp;订单明细： $POrderId 的订单标记为未确认.</br>";
							}
						else{
							$Log.="<div class='redB'>&nbsp;&nbsp;订单： $POrderId 的订单标记未确认失败. $inRecode </div></br>";
							$OperationResult="N";
							}
						}
	                } 
               else{
	                $Log.="<div class=redB>&nbsp;&nbsp; $x - 添加删除订单原因相关信息失败 $inRecode !</div><br>";
	               } 	
			}//end if($sheetRow = mysql_fetch_array($sheetResult))
	    }
   else{
       $Log.="<div class=redB>&nbsp;&nbsp;请上传附件!<br>";
	   $OperationResult="N";
       }
}//end if
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.yw1_ordersheet,$DataIn.ck1_stocksheet,$DataIn.ck5_llsheet,$DataIn.sc1_cjtj");
$ALType="From=$From&CompanyId=$CompanyId";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>