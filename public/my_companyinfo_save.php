<?php 
//新电信---yang 20120801
//代码共享-EWEN 2012-08-15
include "../model/modelhead.php";
//步骤2：
$Log_Item="公司基本资料";			//需处理
$Log_Funtion="保存";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_save";
$_SESSION["nowWebPage"]=$nowWebPage;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";

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
$IN_recode="INSERT INTO $DataIn.my1_companyinfo (Id,cSign,Type,Company,Forshort,Tel,Fax,Address,ZIP,Website,LinkMan,Mobile,Email,Locks) VALUES (NULL,'$cSign','$Type','$Company','$Forshort','$Tel','$Fax','$Address','$ZIP','$Website','$LinkMan','$Mobile','$Email','0')";
$res=@mysql_query($IN_recode);
if($res){
	$Log="$TitleSTR 成功. <br>";
	}
else{
	$Log="<div class='redB'>$TitleSTR 失败. $IN_recode </div><br>";
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>
