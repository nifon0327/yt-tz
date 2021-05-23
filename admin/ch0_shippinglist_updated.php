<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_".$From;
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="模拟出货资料";		//需处理
$upDataSheet="$DataIn.ch0_shipmain";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
$ALType="CompanyId=$CompanyId";
switch($ActionId){
         case 26:
            $Log_Funtion="模拟Invoice重置";
            //include "ch0_shippinglist_toinvoice.php";
			include "ch0_shippinglistBlue_toinvoice.php";
         break;
		case 153:
			$Log_Funtion="Invoice重置(新版)";
			include "ch0_shippinglistBlue_toinvoice.php";
			break;		 
     default:
		$OrderArray=explode("|",$OrderIds);
		$OrderNums=count($OrderArray);
		$Ids1="";
		$Ids2="";
		for($i=0;$i<$OrderNums;$i++){
			$Records=$OrderArray[$i];		
			$TEMP=explode("^^",$Records);
			$Type=$TEMP[0];
			$theId=$TEMP[1];
			if($Type==1){
				$Ids1=$Ids1==""?$theId:$Ids1.",".$theId;
				}
			else{
				$Ids2=$Ids2==""?$theId:$Ids2.",".$theId;
				}
			}

			$Wise=FormatSTR($Wise);
			//更新主单信息
			$UpSql="UPDATE $DataIn.ch0_shipmain SET ModelId='$ModelId',InvoiceNO='$InvoiceNO',Wise='$Wise',Notes='$Notes',Terms='$Terms',PaymentTerm='$PaymentTerm',PreSymbol='$PreSymbol',Date='$Date',Locks='0',Operator='$Operator' WHERE Id='$Id'";
			$UpResult = mysql_query($UpSql);
			if($UpResult){
				$Log.="主出货单信息更新成功.<br>";
				}
			else{
				$Log.="<div class='redB'>主出货单信息更新失败.</div><br>";
				$OperationResult="N";
				}
			
			if($Ids1!="" ){
					$sheetInSql="INSERT INTO $DataIn.ch0_shipsheet SELECT NULL,'$Id',POrderId,ProductId,Qty,Price,'1','1','1','1','0','$Operator','$DateTime',NULL,NULL,'$Date','$Operator' FROM $DataIn.yw1_ordersheet WHERE Id IN ($Ids1)";
				$sheetInAction=@mysql_query($sheetInSql);
				if($sheetInAction && mysql_affected_rows()>0){
					$Log.="出货的订单($Ids1)或随货项目($Ids2)加入出货明细表成功.<br>";
				}
                else{
					$Log.="出货的订单($Ids1)或随货项目($Ids2)加入出货明细表失败$sheetInSql.<br>";
                        }
           }
       
			include "ch0_shippinglist_toinvoice.php";
		break;
}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
