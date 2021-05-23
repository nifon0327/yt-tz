<?php 
//电信---yang 20120801
//代码共享-EWEN
include "../model/modelhead.php";
$fromWebPage=$funFrom."_read";
$nowWebPage=$funFrom."_other_up";
$_SESSION["nowWebPage"]=$nowWebPage; 
//步骤2：
$Log_Item="供应商其它资料";		//需处理
$Log_Funtion="更新";
$TitleSTR=$SubCompany." ".$Log_Item.$Log_Funtion;
ChangeWtitle($TitleSTR);
$DateTime=date("Y-m-d H:i:s");
$Operator=$Login_P_Number;
$OperationResult="Y";
//步骤3：需处理，更新操作
switch($Action){
	case "1":
		$Log_Funtion="排序字母";
		$Result = mysql_query("SELECT Id,Forshort FROM $DataIn.trade_object order by Id DESC",$link_id);
		if($myRow = mysql_fetch_array($Result)){
			do {
				$Id=$myRow["Id"];
				$Forshort=$myRow["Forshort"];
				$chinese=new chinese;
				$Letter=substr($chinese->c($Forshort),0,1);
				//更新分类资料
				$upSql = "UPDATE $DataIn.trade_object SET Letter='$Letter' WHERE Id='$Id'";
				$upResult = mysql_query($upSql);
				}while ($myRow = mysql_fetch_array($Result));
			$Log="&nbsp;&nbsp;排序字母重整成功!</br>";
			}
		else{
			$Log="<div class=redB>&nbsp;&nbsp;排序字母重整失败!</div></br>";
			}
		break;
	case "2":
		/*
		配件采购供应商关系
		入库ck1_rkmain
		采购主单
		采购明细
		用到采购字段 的表
		bps					:采购、配件、供应商关系表
		cg1_stockmain	:采购主表
		cg1_stocksheet	:采购明细
		ck1_rkmain		:入库主表
		ck4_ctmain			:退货主表
		cw1_fkoutsheet	:供应商货款		是否同时更新?
		cw1_tkoutsheet	:客户回扣		是否同时更新?
		
		developnewstaff	:开发?
		developsheet		:开发项目明细?
		*/
		$Log_Funtion="采购变更";
		$upSql1 = "UPDATE $DataIn.bps SET BuyerId='$BuyerId' WHERE CompanyId='$CompanyId'";
		$upResult1 = mysql_query($upSql1);
		if($upResult1){
			$Log="&nbsp;&nbsp;采购变更成功(供应商 $CompanyId 由 $BuyerId 负责).<br>";
			if($HistoryP==1){
				$upSql2 = "UPDATE $DataIn.cg1_stockmain SET BuyerId='$BuyerId' WHERE CompanyId='$CompanyId'";
				$upResult2 = mysql_query($upSql2);
				if($upResult2){
					$Log.="历史采购主单更新成功。<br>";
					}
				$upSql3 = "UPDATE $DataIn.cg1_stocksheet SET BuyerId='$BuyerId' WHERE CompanyId='$CompanyId'";
				$upResult3 = mysql_query($upSql3);
				if($upResult3){
					$Log.="历史采购明细更新成功。<br>";
					}
				$upSql4 = "UPDATE $DataIn.ck1_rkmain SET BuyerId='$BuyerId' WHERE CompanyId='$CompanyId'";
				$upResult4 = mysql_query($upSql4);
				if($upResult4){
					$Log.="历史入库主单更新成功。<br>";
					}
				}
			}
		else{
			$Log="<div class=redB>&nbsp;&nbsp;采购变更失败! $upSql </div></br>";
			$OperationResult="N";
			}
		break;
	case "3":
		$Log_Funtion="清除闲置供应商";
		$delSql= "DELETE A,B,C,E,D	
			FROM $DataIn.trade_object A
			LEFT JOIN $DataIn.companyinfo B ON A.CompanyId=B.CompanyId AND B.Type='2'
			LEFT JOIN $DataIn.linkmandata C ON B.CompanyId=C.CompanyId AND C.Type='2'
			LEFT JOIN $DataIn.usertable D ON C.Id=D.Number
			LEFT JOIN $DataIn.online E ON D.Id=E.uId
			LEFT JOIN (
				SELECT * FROM (
					SELECT CompanyId FROM $DataIn.bps GROUP BY CompanyId
					UNION ALL
					SELECT CompanyId FROM $DataIn.cg1_stocksheet GROUP BY CompanyId
					UNION ALL
					SELECT CompanyId FROM $DataIn.cw2_fkdjsheet GROUP BY CompanyId
					) Y GROUP BY CompanyId 
				) Z ON Z.CompanyId=A.CompanyId
			WHERE  Z.CompanyId IS NULL";
		$delResult = mysql_query($delSql,$link_id);
		if($delResult){
			$Log="清除闲置供应商成功.<br>";
			}
		else{
			$Log="<div class=redB>清除闲置供应商失败. $delSql</div><br>";
			$OperationResult="N";
			}
		break;
	}
$IN_recode="INSERT INTO $DataIn.oprationlog (DateTime,Item,Funtion,Log,OperationResult,Operator) VALUES ('$DateTime','$Log_Item','$Log_Funtion','$Log','$OperationResult','$Operator')";
$IN_res=@mysql_query($IN_recode);
include "../model/logpage.php";
?>