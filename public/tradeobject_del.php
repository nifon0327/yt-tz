<?php 
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
$Log_Item="交易对象资料";//需处理
$upDataSheet="trade_object";	//需处理
$Type=2;
$Log_Funtion="删除";
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

$delSql= "DELETE A,B,C,E,D,F ,G,H
	FROM $DataIn.trade_object A
	LEFT JOIN $DataIn.companyinfo B ON A.CompanyId=B.CompanyId AND B.Type='8'
	LEFT JOIN $DataIn.linkmandata C ON B.CompanyId=C.CompanyId AND C.Type='8'
	LEFT JOIN $DataIn.usertable D ON C.Id=D.Number
	LEFT JOIN $DataIn.online E ON D.Id=E.uId
    LEFT JOIN $DataIn.providersheet F ON F.CompanyId=A.CompanyId
	LEFT JOIN $DataIn.sys4_gysfunpower G ON G.UserId=D.Id
	LEFT JOIN $DataIn.sys_clientfunpower H ON H.UserId=D.Id
	LEFT JOIN (
		SELECT * FROM (
			SELECT CompanyId FROM $DataIn.bps GROUP BY CompanyId
			UNION ALL
			SELECT CompanyId FROM $DataIn.cg1_stocksheet GROUP BY CompanyId
			UNION ALL
			SELECT CompanyId FROM $DataIn.cw2_fkdjsheet GROUP BY CompanyId
            UNION ALL 
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
//如果该页记录全删，则返回第一页
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Orderby=$Orderby&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>