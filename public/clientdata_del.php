<?php 
//电信-zxq 2012-08-01
//代码共享-EWEN 2012-08-13
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 

//步骤2：
$Log_Item="客户资料";//需处理
$upDataSheet="clientdata";	//需处理
$Log_Funtion="删除";
$Type=1;
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
ChangeWtitle($TitleSTR);
//步骤3：需处理，执行动作
$y=1;
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);$y++;
		}
	}
$Type=2;
//检查是否闲置，如果不是闲置，则不能删除，相应客户的权限也要删除
//在 1、订单主表ordermain 2、出货单主表shipmentmain 3、产品资料表productdata 4、提货单主表ch1_deliverymain
$delSql = "DELETE A,B,C,D,E,F	
	FROM $DataIn.trade_object A
	LEFT JOIN $DataIn.companyinfo B ON A.CompanyId=B.CompanyId AND B.Type='$Type'
	LEFT JOIN $DataIn.linkmandata C ON B.CompanyId=C.CompanyId AND C.Type='$Type'
	LEFT JOIN $DataIn.usertable D ON C.Id=D.Number
	LEFT JOIN $DataIn.online E ON D.Id=E.uId
	LEFT JOIN $DataIn.sys_clientfunpower F ON F.UserId=D.Id
	LEFT JOIN (
		SELECT * FROM (
			SELECT CompanyId FROM $DataIn.yw1_ordermain GROUP BY CompanyId
			UNION ALL
			SELECT CompanyId FROM $DataIn.ch1_shipmain GROUP BY CompanyId
			UNION ALL
			SELECT CompanyId FROM $DataIn.productdata GROUP BY CompanyId
			UNION ALL 
			SELECT CompanyId FROM $DataIn.ch1_deliverymain GROUP BY CompanyId
		) Y GROUP BY CompanyId 
	) Z ON Z.CompanyId=A.CompanyId
	WHERE A.Id IN ($Ids)
	AND Z.CompanyId IS NULL
	";
$delResult=mysql_query($delSql);
if($delResult){
	$Log.="ID号在 $Ids 的 $TitleSTR 成功(操作后记录仍在则不符合删除条件)!<br>";
	}
else{
	$Log.="<div class='redB'>ID号在 $Ids 的 $TitleSTR 失败! $delSql </div><br>";
	$OperationResult="N";
	}
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.trade_object,$DataIn.companyinfo,$DataIn.linkmandata,$DataIn.usertable,$DataIn.online,$DataIn.sys_clientfunpower");
//如果该页记录全删，则返回第一页
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Orderby=$Orderby&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>