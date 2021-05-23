<?php   
//电信-zxq 2012-08-01
include "../model/modelhead.php";
$fromWebPage=$funFrom."_".$From;
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="模拟出货资料";		//需处理
$upDataSheet="$DataIn.ch0_shipmain";	//需处理
$Log_Funtion="删除";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if($Id!=""){
		$CheckPdf=mysql_fetch_array(mysql_query("SELECT InvoiceNO FROM $DataIn.ch0_shipmain WHERE Id='$Id'",$link_id));
		$InvoiceFile=$CheckPdf["InvoiceNO"].".pdf";
		$DelSql="DELETE  M,C,P 
			FROM $DataIn.ch0_shipmain M 
			LEFT JOIN $DataIn.ch0_shipsheet C ON C.Mid=M.Id 
			LEFT JOIN $DataIn.ch0_packinglist P ON P.Mid=M.Id	
			WHERE M.Id='$Id'";
             
		$delRresult = mysql_query($DelSql);
		if ($delRresult && mysql_affected_rows()>0){
			$Log.="模拟出货单 $Id / $InvoiceFile 删除成功.<br>";
			$FilePath="../download/Invoice0/$InvoiceFile";
			if(file_exists($FilePath)){
				unlink($FilePath);
				}
			}
		else{	//删除失败，状态维持已出货状态
			$Log.="<div class='redB'>模拟出货单 $Id / $InvoiceFile 删除失败.</div><br>$DelSql";
			$OperationResult="N";
			}		
		}
	}
//表整理
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.ch0_shipmain,$DataIn.ch0_shipsheet,$DataIn.ch0_packinglist");
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
