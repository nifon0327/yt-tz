<?php 
//电信---yang 20120801
//代码共享-EWEN 2012-08-15
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_updated";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="公司基本资料";		//需处理
$upDataSheet="$DataIn.my1_companyinfo";	//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
$x=1;
switch($ActionId){
	case 7:
		$Log_Funtion="锁定";	$SetStr="Locks=0";				include "../model/subprogram/updated_model_3d.php";		break;
	case 8:
		$Log_Funtion="解锁";	$SetStr="Locks=1";				include "../model/subprogram/updated_model_3d.php";		break;
	default:
		$Company=FormatSTR($Company);
		$Forshort=FormatSTR($Forshort);
		$Tel=FormatSTR($Tel);
		$Fax=FormatSTR($Fax);
		$Address=FormatSTR($Address);
		$ZIP=FormatSTR($ZIP);
		$Website=FormatSTR($Website);
		$LinkMan=FormatSTR($LinkMan);
		$Mobile=FormatSTR($Mobile);
		$Email=FormatSTR($Email);
		$SetStr="cSign='$cSign',Type='$Type',Company='$Company',Forshort='$Forshort',Tel='$Tel',Fax='$Fax',
		Address='$Address',ZIP='$ZIP',WebSite='$WebSite',LinkMan='$LinkMan',Mobile='$Mobile',Email='$Email',Locks='0'";
		include "../model/subprogram/updated_model_3a.php";
		break;
	}
$ALType="From=$From&Pagination=$Pagination&Page=$Page&chooseDate=$chooseDate";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>