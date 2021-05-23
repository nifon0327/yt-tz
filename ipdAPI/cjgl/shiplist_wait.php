<?php 
	
	include "../../basic/parameter.inc";
	
	$SearchRows=" and S.Estate='2' AND S.scFrom=0";
	$SearchRows2=" and S.Estate='1'";
	
	$ship_list_wait = array();
	
	$shiplistSql = "SELECT M.OrderNumber,M.CompanyId,M.OrderDate,'1' AS Type,S.Id,S.OrderPO,S.POrderId,
    S.ProductId,S.Qty,S.Price,S.PackRemark,P.cName,P.eCode,P.TestStandard
	FROM $DataIn.yw1_ordersheet S 
	LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId WHERE 1 $SearchRows	
    UNION ALL 
	SELECT '' AS OrderNumber,S.CompanyId,S.Date AS OrderDate,'2' AS Type,S.Id,'' AS OrderPO,
	S.SampId AS POrderId,'' AS ProductId,S.Qty,S.Price,'' AS PackRemark,
	S.SampName AS cName,S.Description AS eCode,'' AS TestStandard 
	FROM $DataIn.ch5_sampsheet S WHERE 1 $SearchRows2";
	
	//echo $shiplistSql;
	$no = 1;
	$myResult = mysql_query($shiplistSql);
	
	if($myRow = mysql_fetch_array($myResult)){
	do{
		$m=1;	
		$LockRemark="";
		$Id=$myRow["Id"];
		$OrderPO=$myRow["OrderPO"]==""?"&nbsp;":$myRow["OrderPO"];
		$OrderDate=$myRow["OrderDate"];		
		$POrderId=$myRow["POrderId"];
		$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' AND Type=1 ORDER BY Id LIMIT 1",$link_id);
		/*
		if($checkExpressRow = mysql_fetch_array($checkExpress)){
			$ColbgColor="bgcolor='#0066FF'";
			}
		else{
			$ColbgColor="";
			}
		*/	
		$ProductId=$myRow["ProductId"]==""?"&nbsp;":$myRow["ProductId"];
		$Qty=$myRow["Qty"];
		$Price=sprintf("%.2f",$myRow["Price"]);	
		$Amount=sprintf("%.2f",$Qty*$Price);
		$PackRemark=$myRow["PackRemark"]; 
		$cName=$myRow["cName"]; 
		$eCode=$myRow["eCode"]; 
		$Description=$myRow["Description"];
		$Type=$myRow["Type"];
		$TestStandard=$myRow["TestStandard"];
		
		$path = $_SERVER["DOCUMENT_ROOT"];
		
		
		include "../admin/Productimage/getPOrderImage.php";
		$OrderPO=$Type==2?"随货项目":$OrderPO;
		$checkidValue=$Id."^^".$Type;
		$Locks=1;
		if($Type==1){//如果是订单：检查生产数量与需求数量是否一致，如果不一致，不允许选择
			//工序总数
			$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
			$gxQty=$CheckgxQty["gxQty"];
			//已完成的工序数量
			$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty FROM $DataIn.sc1_cjtj C 
			LEFT JOIN $DataIn.stufftype T ON C.TypeId=T.TypeId
			WHERE C.POrderId='$POrderId' AND T.Estate=1",$link_id));
			$scQty=$CheckscQty["scQty"];
			if($gxQty!=$scQty){//生产完毕
				$LockRemark.="生产登记异常! $gxQty<>$scQty ";
				}
			//检查领料记录 备料总数与领料总数比较
			$CheckblQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS blQty 
				FROM $DataIn.cg1_stocksheet G
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=1",$link_id));
			$blQty=$CheckblQty["blQty"];
			$CheckllQty=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty) AS llQty 
				FROM $DataIn.cg1_stocksheet G 										
				LEFT JOIN  $DataIn.ck5_llsheet K ON K.StockId = G.StockId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				WHERE G.POrderId='$POrderId' AND K.Estate=0",$link_id));
			$llQty=$CheckllQty["llQty"];
			if($blQty!=$llQty){//领料完毕
				$LockRemark.="领料异常！$blQty<>$llQty";
				}
			}
	    $LockRemark.=$TestStandardSign==0?"标准图未上传或未通过,禁止出货":"";
		if($LockRemark!=""){
		    $disable="disabled";
		       }
		 else  $disable="";
		
		$eCode=$eCode.$gxQty."/".$scQty;		
		
		$ship_list_wait[] = array("$no","$OrderPO","$POrderId","$cName","$Price","$Qty","$Amount","$OrderDate","$LockRemark","$ProductId");
		$no++;
		$i++;		
		}while ($myRow = mysql_fetch_array($myResult));
	}

	echo json_encode($ship_list_wait);
	
?>