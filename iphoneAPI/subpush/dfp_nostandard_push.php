<?php   
//待分配产品标准图未上传的推送功能
//-- 12066
//  $userIdSTR=$CheckRow["Number"];
 
include "d:/website/mc/basic/parameter.inc";
				
$msgArray=array();
 $userIdSTR="12066,11965";$DateTime=date("Y-m-d H:i:s");
  $userinfo="1";   $bundleId="DailyManagement";
 	$allForName = "";
		$hasCount=0;
	//
	$mySql="SELECT A.*,C.Forshort,P.cName,P.TestStandard  FROM
			(
				SELECT S.Id,S.Qty,S.ProductId,M.CompanyId,S.POrderId,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty, SUM(L.Estate) as llEstate,Count(S.POrderId) as count
						FROM $DataIn.yw1_ordersheet S
						INNER JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
						LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
						INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
						INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						INNER JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						LEFT JOIN (
									 SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(L.Estate) as Estate
									 FROM $DataIn.yw1_ordersheet S 
									 LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
									 LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
									 WHERE  S.scFrom>0 AND S.Estate=1 GROUP BY L.StockId
								 ) L ON L.StockId=G.StockId
						WHERE S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  GROUP BY S.POrderId 
						) A 
                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId=A.CompanyId  
			            LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=A.Id 
			            LEFT Join $DataIn.yw3_pileadtime PIL On PIL.POrderId = A.POrderId
                        LEFT JOIN $DataIn.productdata P ON P.ProductId=A.ProductId
					WHERE A.blQty=A.llQty ORDER BY C.Forshort , PI.Leadtime, A.Qty Desc ";
	
	$registrListResult = mysql_query($mySql);
	while($registrRow = mysql_fetch_assoc($registrListResult))
	{
		$lockState = "";
		$POrderId = $registrRow["POrderId"];
		$companyShort = $registrRow["Forshort"];
		$productName = $registrRow["cName"];
		$TestStandardIpad = $registrRow["TestStandard"];
		$llItemCountResult = mysql_query("Select Count(*) as count From $DataIn.ck5_llsheet Where POrderId = '$POrderId'");
		$llItemCountRow = mysql_fetch_assoc($llItemCountResult);
		$llItemCount = $llItemCountRow["count"];
		$llEstate = $registrRow["llEstate"];
		$count = $registrRow["count"];
		if($llEstate == "" || $llItemCount<$count){
			continue;
		} else if($llEstate == 0){
			continue;
		} else if($llEstate > 0 && $llItemCount >= $count){
		}
		//
		else{continue;}
		$mission  = "";
		$missionQeury = mysql_query("Select B.GroupName From $DataIn.sc1_mission A
							   		INNER Join $DataIn.staffgroup B On B.Id = A.Operator
							   		Where A.POrderId = '$POrderId' And B.Estate = '1' Limit 1");
		if($missionResult = mysql_fetch_assoc($missionQeury)){$mission = $missionResult["GroupName"];}
		//GroupNameLimit
		if ($mission != "") {continue; }
		//处理标准图
		if($TestStandardIpad == "1"){
			$checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id Limit 1",$link_id);
			if($checkteststandardRow = mysql_fetch_array($checkteststandard)){
				$TestStandardIpad = 3;
			}
		}
		
		if ((int)$TestStandardIpad != 1) {
			$pushEstate = 0;
			$checkPushedSql = mysql_query("select Estate from $DataIn.push_dfp_nopic where POrderId='$POrderId'");
			if ($checkPushedRow = mysql_fetch_assoc($checkPushedSql)) {
				$pushEstate = $checkPushedRow["Estate"]; 
			}
			if ($pushEstate == 0) {
				mysql_query("replace into $DataIn.push_dfp_nopic (Id,POrderId,Estate,DateTime) values (null,'$POrderId',1,'$DateTime')");
				$allForName = "$companyShort"."的"."\"".$productName."\";";
				$message = $allForName."已达待分配状态，但未上传标准图或标准图需要更改。";
				include "d:/website/mc/iphoneAPI/push_apple.php";
			}
		}
	}		

?> 