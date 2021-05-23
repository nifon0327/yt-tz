<?php
	
	include_once "../../basic/parameter.inc";
	include_once "../../model/modelfunction.php";
	
	$companyId = $_POST["companyId"];
	//$companyId = "1002";
	$typeId = $_POST["typeId"];
	//$typeId = "8022";
	$Login_P_Number = $_POST["operator"];
	//$Login_P_Number = "11008";
	
	$mySql = "SELECT G.POrderId,G.OrderQty,Y.Estate,P.cName,Y.OrderPO,Y.Qty AS mainQty,P.TestStandard,P.ProductId  
			  FROM $DataIn.ck5_llsheet S 
			  LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
			  LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
			  LEFT JOIN $DataIn.yw1_ordermain W  ON W.OrderNumber=Y.OrderNumber
			  LEFT JOIN $DataIn.productdata P ON P.ProductId=Y.ProductId
			  WHERE 1 
			  And S.Estate = 1
			  And W.CompanyId='$companyId' 
			  ANd P.TypeId = '$typeId'
			  AND G.POrderId > 0 Group BY G.POrderId ORDER BY G.POrderId DESC";
	  
	$confirmList = array();
	$comfirnResult = mysql_query($mySql);
	while($mainRows = mysql_fetch_assoc($comfirnResult))
	{
		$POrderId=$mainRows["POrderId"];
		$OrderPO=$mainRows["OrderPO"];
		$mainQty=$mainRows["mainQty"];
		$cName=$mainRows["cName"];
		$ProductId=$mainRows["ProductId"];
		$TestStandard=$mainRows["TestStandard"];
		$Estate=$mainRows["Estate"];
		$orderQty=$mainRows["mainQty"];
		
		$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' AND Type=2 LIMIT 1",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress))
		{
		   //不显示领料信息
		}
        else
        {		  
		   	//==============================判断
		   	$ll_Result=mysql_query("SELECT count(A.StockId) AS stockNum,SUM(A.Qty) AS stockQty FROM(
		              			   	SELECT S.StockId,SUM(S.Qty)AS Qty 
		              			   	FROM $DataIn.ck5_llsheet S
		              			   	LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
		              			   	WHERE G.POrderId='$POrderId' GROUP BY S.StockId )A",$link_id);
		    if($ll_Row=mysql_fetch_array($ll_Result))
		    {
		   		$ll_Num=$ll_Row["stockNum"];
				$ll_Qty=$ll_Row["stockQty"];
			}  
			  
			$cg_Result=mysql_query("SELECT COUNT(G.StockId) AS stockNum,SUM(G.OrderQty) AS stockQty
			           				FROM $DataIn.cg1_stocksheet G
			           				LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
			           				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
			           				LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
			           				WHERE G.POrderId='$POrderId' AND T.mainType<2",$link_id);
			if($cg_Row=mysql_fetch_array($cg_Result))
			{
			    $cg_Num=$cg_Row["stockNum"];
			    $cg_Qty=$cg_Row["stockQty"];
			}
			
			$stockState = "no"; ////有配件未领
			if($ll_Num==$cg_Num)
			{			         
				if($ll_Qty==$cg_Qty)
				{
					//$poColor="bgcolor='greenB'";//各配件都领且料领完
					$stockState = "finish";
				}
				else
				{
					//$poColor="bgcolor='#FF9900'";//各配件都领，但没领完
					$stockState = "nofinish";
				}
			}
						  
           //=================================
           
           $subMySql = "SELECT G.OrderQty,G.StockId,K.tStockQty,D.StuffId,D.StuffCname,D.Picture,F.Remark,M.Name,P.Forshort,U.Name AS UnitName 
									   FROM $DataIn.cg1_stocksheet G 
									   LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
									   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
									   LEFT JOIN $DataPublic.staffmain M ON M.Number=G.BuyerId 
									   LEFT JOIN $DataIn.trade_object P ON P.CompanyId=G.CompanyId 
									   LEFT JOIN $DataIn.base_mposition F ON F.Id=D.SendFloor
									   LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
									   LEFT JOIN $DataPublic.stuffunit U ON U.Id=D.Unit
									   WHERE G.POrderId='$POrderId' 
									   AND T.mainType<2 
									   ORDER BY G.StockId";
           
           $checkStockSql=mysql_query($subMySql, $link_id);
		   $subList = array();
		   while($checkStockRow=mysql_fetch_array($checkStockSql))
		   {
			   // $checkidValue=$checkStockRow["Id"];
			   $Name=$checkStockRow["Name"];
			   $Forshort=$checkStockRow["Forshort"];
			   $StockId=$checkStockRow["StockId"];
			   $StuffId=$checkStockRow["StuffId"];
			   $StuffCname=$checkStockRow["StuffCname"];
			   $otherCname=$StuffCname;
			   $Picture=$checkStockRow["Picture"];
			   $tStockQty=$checkStockRow["tStockQty"];
			   $sOrderQty=$checkStockRow["OrderQty"];
			   $Remark=$checkStockRow["Remark"];
			   //检查是否有图片
			   //备料人
			   $BlmanResult=mysql_query("SELECT A.Name AS blMan,S.Locks
					   					 FROM $DataIn.yw9_blmain M
					   					 LEFT JOIN $DataIn.ck5_llsheet S ON S.Pid=M.Id 
					   					 LEFT JOIN $DataPublic.staffmain A ON A.Number=M.Operator
					   					 WHERE S.StockId='$StockId'",$link_id);
			   $blMan="";$Locks="";
			   if($blManRow=mysql_fetch_array($BlmanResult))
			   {
			   		$blMan=$blManRow["blMan"]; 
					$Locks=$blManRow["Locks"];
			   }
			   //本次领料数
			   $UnionSTR3=mysql_query("SELECT SUM(Qty) AS thisQty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' AND Estate='1'",$link_id);
			   $thisQty=mysql_result($UnionSTR3,0,"thisQty");
			   $thisQty=$thisQty==""?0:$thisQty;
			   //已备料总数
			   $UnionSTR4=mysql_query("SELECT SUM(Qty) AS llQty FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' AND Estate='0'",$link_id);
			   $llQty=mysql_result($UnionSTR4,0,"llQty");
			   $llQty=$llQty==""?0:$llQty;
			   if($llQty>$sOrderQty)
			   {//领料总数大于订单数,提示出错
				   $llBgColor="class='redB'";
			   }
			   else
			   {
				   if($llQty==$sOrderQty)
				   {//刚好全领，绿色
					   $llBgColor="class='greenB'";
							 //$k++;
				    }
				    else
				    {//未领完
					 	$llBgColor="";
					}
			    }
			   //确认操作
			   $upIMG="&nbsp;";$upImgclick="";
			   $permissonCheck = "0";
			   $RemainQty=$sOrderQty-$llQty;//未领料数
			   $scnameTemp=preg_replace('[\"|“|”|’|\']', '',$otherCname);
			   $msgStr="需求单流水号:$StockId|配件名称:$scnameTemp|备料数量:$thisQty";
			   if($JobId==14 || $Login_P_Number=='10218')
			   {//登陆员工是仓管备料员/胡家菊
				//$JobId==34 || $Login_P_Number=='10218'
				   if($thisQty!=0)
				   {
					       $permissonCheck = "del";
				   }
			   }
			   else
			   {
				   if($thisQty!=0)
				   {
					   if($thisQty<=$RemainQty)
					   {
				           $permissonCheck = "pass";
				       }
					   else
					   {
						   $permissonCheck = "over"; 
					   }
				   }
	           }
			   
			   $subList[] = array("StuffId"=>"$StuffId", "StuffName"=>"$StuffCname", "StockId"=>"$StockId", "blOperator"=>"$blMan", "OrderQry"=>"$sOrderQty", "llQty"=>"$llQty", "qty"=>"$thisQty", "picture"=>"$Picture", "permissonCheck"=>"$permissonCheck");
			   
		}
	}
        
        $confirmList[] = array(array("OrderPO"=>"$OrderPO", "ProductName"=>"$cName", "OrderQty"=>"$orderQty", "TestStandard"=>"$TestStandard", "StockState"=>"$stockState"), $subList);
        
	}
	
	echo json_encode($confirmList);
	
?>