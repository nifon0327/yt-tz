<?php 
//EWEN 2013-03-29 OK
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_del";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="非BOM供应商资料";//需处理
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

//检查是否闲置，如果不是闲置，则不能删除
//在 1、关系资料库bps 2、配件需求表stocksheet(临时特采) 3、订金表subscription 5中查找如果有使用记录则不能删除
//供应商资料表、在线表、用户表
$delSql= "
DELETE A,B,C,D
	FROM $DataPublic.nonbom3_retailermain A
	LEFT JOIN $DataPublic.nonbom3_retailersheet B ON B.CompanyId=A.CompanyId
	LEFT JOIN $DataPublic.nonbom3_retailerlink C ON C.CompanyId=A.CompanyId
	LEFT JOIN $DataPublic.nonbom3_retailerother D ON D.CompanyId=A.CompanyId
	LEFT JOIN (
		SELECT * FROM (
			SELECT CompanyId FROM $DataIn.nonbom5_goodsstock GROUP BY CompanyId
			UNION ALL
			SELECT CompanyId FROM $DataIn.nonbom11_djsheet GROUP BY CompanyId
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
//$OPTIMIZE=mysql_query("OPTIMIZE TABLE $DataIn.trade_object,$DataIn.companyinfo,$DataIn.linkmandata,$DataIn.usertable,$DataIn.online,$DataIn.providersheet,$DataIn.sys4_gysfunpower");
//如果该页记录全删，则返回第一页
$Page=$IdCount==$y?1:$Page;
$ALType="From=$From&Orderby=$Orderby&Pagination=$Pagination&Page=$Page";
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>