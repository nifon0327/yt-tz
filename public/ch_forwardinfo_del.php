<?php 
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤2：
$Log_Item="Forward公司资料";//需处理
$Log_Funtion="删除";
$Type=1;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
//检查是否闲置，如果不是闲置，则不能删除
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:$Ids.",".$Id;
		}
	}
//检查是否闲置，如果不是闲置，则不能删除
$delResult = mysql_query("DELETE A,B,C
	FROM $DataIn.forwarddata A
	LEFT JOIN $DataIn.companyinfo B ON A.CompanyId=B.CompanyId
	LEFT JOIN $DataIn.linkmandata C ON B.CompanyId=C.CompanyId
	LEFT JOIN (
			SELECT CompanyId FROM $DataIn.ch3_forward GROUP BY CompanyId 
		) Z ON Z.CompanyId=A.CompanyId
	WHERE A.Id IN ($Ids) AND Z.CompanyId IS NULL 
	",$link_id);
if($delResult && mysql_affected_rows()>0){
	$Log="&nbsp;&nbsp;ID在( $Ids )的 $TitleSTR 成功.<br>";
	}
else{
	$Log="<div class='redB'>&nbsp;&nbsp;ID在( $Ids )的 $TitleSTR 失败!</div><br>";
	$OperationResult="N";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataPublic.freightdata,$DataIn.companyinfo,$DataIn.linkmandata");

$ALType="From=$From&Orderby=$Orderby&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>