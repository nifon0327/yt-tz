<?php 
/*
电信-zxq 2012-08-01
更新:加入清除生产记录动作
*/
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="报关出口单";//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$x=1;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		//读取订单信息
		$sheetResult = mysql_query("SELECT M.DeclarationNo,D.BillNumber,M.CertificateEstate FROM $DataIn.cw13_customsmain 	M
									LEFT JOIN $DataIn.cw5_customsfbdh K  ON K.CertificateNo=M.CertificateNo
									LEFT JOIN $DataIn.cw5_fbdh D  ON D.BillNumber=K.BillNumber
								   WHERE M.Id='$Id'  ",$link_id);

		/*
		echo "SELECT M.DeclarationNo,D.BillNumber,CertificateEstate FROM $DataIn.cw13_customsmain 	M
									LEFT JOIN $DataIn.cw5_customsfbdh K  ON K.CertificateNo=M.CertificateNo
									LEFT JOIN $DataIn.cw5_fbdh D  ON D.BillNumber=K.BillNumber
								   WHERE M.Id='$Id' ";
		*/						   
		if($sheetRow = mysql_fetch_array($sheetResult)){
			$DeclarationNo=$sheetRow["DeclarationNo"];
			$BillNumber=$sheetRow["BillNumber"];
			$CertificateEstate=$sheetRow["CertificateEstate"];
			//echo "A: $CertificateEstate : $BillNumber <br>";
		    if($CertificateEstate==0 || $BillNumber!=""){	
				$Log.="<div class='redB'>$x - &nbsp;&nbsp;报关单号为 $DeclarationNo 的单删除失败(是否已核实或已结汇).</div><br>";
				}//删除订单:订单非待出或已出状态
			else{
				$delOrderSql="DELETE FROM $DataIn.cw13_customsmain WHERE Id='$Id' LIMIT 1";
				//echo "DELETE FROM $DataIn.cw13_customsmain  WHERE Id='$Id' LIMIT 1";
				$delOrderRresult = mysql_query($delOrderSql);
				if($delOrderRresult && mysql_affected_rows()>0){
					$Log.="&nbsp;&nbsp; $x - 报关单号为 $DeclarationNo 的单删除成功.<br>";
					$delScSql="DELETE FROM $DataIn.cw13_customssheet WHERE DeclarationNo='$DeclarationNo'";
					//echo "$delScSql <br>";
					$delScRresult = mysql_query($delScSql);
					$Log.="Invoice记录已做清除处理.<br>";
					}
				else{
					$Log.="&nbsp;&nbsp; $x - 报关单号为 $DeclarationNo 的单删除失败.$delOrderSql<br>";
					}
	
			}
		}

	}//end if ($Id!="")
}//end for
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.cw13_customsmain,$DataIn.cw13_customssheet");
//$ALType="From=$From&CompanyId=$CompanyId";
//步骤4：操作日志
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>