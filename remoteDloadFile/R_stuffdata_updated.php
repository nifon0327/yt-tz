<?php
/*$DataIn.电信---yang 20120801
$DataIn.bps
$DataIn.stuffdata
$DataIn.stuffimg
二合一已更新
*/
//步骤1


//$fromWebPage=$funFrom."_read";
//$nowWebPage=$funFrom."_updated";
//$_SESSION["nowWebPage"]=$nowWebPage;
//步骤2：
$Log_Item="配件资料";		//需处理
$upDataSheet="$DataIn.stuffdata";	//需处理
$Log_Funtion="更新";
//$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
//ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
//$OperationResult="Y";
$FilePath="../download/stufffile/";
//步骤3：需处理，更新操作
$x=1;
if ($R_IdStr!="") {  //远程传过来的全部用$R_IdStr：1001|1002| ,用来模枋$checkid
	$checkid=explode("|",$R_IdStr);
}

switch($ActionId){

	case 73:
		$Date=date("Y-m-d H:i:s");
		$Log_Funtion="图档审核通过";	$SetStr="Gstate=1,GfileDate='$Date'";
		include "../remoteDloadFile/subprogram/R_updated_model_3G.php";

		break;
	}

 ?>