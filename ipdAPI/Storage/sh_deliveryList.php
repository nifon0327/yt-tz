<?php
	
	include_once "../../basic/parameter.inc";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once('../../FactoryCheck/FactoryClass/AttendanceDatetype.php');
	
	$floor = $_POST["floor"];	
	//$floor = "6";
	if($floor == "3")
	{
		$searchRow = " And (M.Floor = '$floor' or M.Floor = 0)";
	}
	else if($floor == "6")
	{
		//$floor = "6";
		$searchRow = " And M.Floor in ('6', '12')";
	}
	
	$checkNumSql = mysql_query("SELECT M.BillNumber,M.Date,M.CompanyId,P.Forshort, M.Id, SUM(S.Qty) as Qty
				 				FROM $DataIn.gys_shmain M 
								LEFT JOIN $DataIn.gys_shsheet S ON S.Mid=M.Id
								LEFT JOIN $DataIn.trade_object P ON M.CompanyId=P.CompanyId
								WHERE S.Estate = 1
								$searchRow
								GROUP BY S.Mid 
								ORDER BY M.Date",$link_id);							
	$gysList = array();
	
	while($gysRows = mysql_fetch_assoc($checkNumSql))
	{
		$billNumber = $gysRows["BillNumber"];
		$billDate = $gysRows["Date"];
		$billDate = substr($billDate, 0, 16);
		
		/************************验厂过滤时间日期***************************/
		if($factoryCheck == 'on'){
			$staffNumberSql = mysql_query("SELECT Number FROM $DataPublic.staffmain WHERE JobId = 14 AND GroupId = 701 Limit 1");
			$staffNumberResult = mysql_fetch_assoc($staffNumberSql);
			/************加入过滤***************/
	        $Number = $staffNumberResult['Number'];
	        $sheet = new WorkScheduleSheet($Number, $Date, $attendanceTime['start'], $attendanceTime['end']);
	        $datetypeModle = new AttendanceDatetype($DataIn, $DataPublic, $link_id);
	        $datetype = $datetypeModle->getDatetype($Number, $Date, $sheet);
	        if($datetype['morning'] != 'G' && $datetype['afternoon'] != 'G'){
	            continue;
	        }
	       	$billDate = substr($billDate, 0, 10);
		}
		/****************************************************************/
		
		$CompanyId = $gysRows["CompanyId"];
	    $forShort = $gysRows["Forshort"];
	    $totleBillQty = $gysRows["Qty"];
	    $MId = $gysRows["Id"];
		
		//$billCount = 0;
			
		$mySql="SELECT M.CompanyId, S.Id,S.Mid,S.StockId,S.Qty,S.StuffId,S.SendSign,D.StuffCname, D.TypeId,D.Picture,(G.AddQty+G.FactualQty) AS cgQty,M.Date,G.POrderId,Y.OrderPO,Y.ProductId,Y.Qty as PQty,Y.PackRemark,Y.sgRemark, PI.Leadtime, G.DeliveryDate
                FROM $DataIn.gys_shsheet S
                LEFT JOIN $DataIn.gys_shmain M ON S.Mid = M.Id
                LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId = S.StockId 
                LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId = G.POrderId
                LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=Y.Id 
                LEFT JOIN $DataIn.stuffdata D ON D.StuffId = S.StuffId
                WHERE S.Estate = 1
                And M.Id='$MId'
                ORDER BY S.Id";
		
         $subListResult = mysql_query($mySql);
         
         if(mysql_num_rows($subListResult) >= 1)
         {
         	$stuffIdChk = "";
			$sameStuffCount = 0;
         	$detailList = array();
           	while($subListRows = mysql_fetch_assoc($subListResult))
           	{
	           	$Id = $subListRows["Id"];
	           	$StockId = $subListRows["StockId"];
	           	$realStorckIdHolder = $subListRows["POrderId"];
	           	$StuffCname = $subListRows["StuffCname"];
	           	$cgQty = $subListRows["cgQty"];
	           	$Qty = $subListRows["Qty"];
	           	$SendSign = $subListRows["SendSign"];
	           	$StuffId = $subListRows["StuffId"];
	           	$POrderId=$subListRows["POrderId"];
	           	$TypeId = $subListRows["TypeId"];
	           	$piDate = str_replace("*", "", $subListRows["DeliveryDate"]);
			   	$piDate = date("Y-m-d", strtotime($piDate));
			   	$piWeekResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$piDate',1) AS Week",$link_id));
			   	$leadTime = $piWeekResult["Week"];
	           	$LockRemark = "no";
	           	$SignString="";
	           	
	           	$sProperty = "0";
	           	$stuffPropertySql = mysql_query("Select Property From $DataIn.stuffproperty Where StuffId = $StuffId and Property = 1");
	           	if($stuffPropertyRow = mysql_fetch_assoc($stuffPropertySql)){
	           		$sProperty = $stuffPropertyRow['Property'];
	           	}
	           	
	           	//历史订单
	           	$CheckGSql=mysql_query("SELECT IFNULL(SUM(FactualQty+AddQty),0) AS cgQty FROM $DataIn.cg1_stocksheet WHERE StuffId='$StuffId'",$link_id);
	           	$historyOrderRow = mysql_fetch_assoc($CheckGSql);
	           	$historyOrder = $historyOrderRow["cgQty"];
	           	
	           	if($stuffIdChk == "" || $stuffIdChk == $StuffId)
				{
					if($stuffIdChk == "")
					{
						$stuffIdChk = $StuffId;
					}
					$sameStuffCount += $Qty;
				}
				else if($stuffIdChk != $StuffId)
				{	
					$sameCountQty = intval($sameStuffCount);
					$sameCountQty = number_format($sameCountQty);
					$detailList[] = array("stuffIdChk"=>"$stuffIdChk", "sameCountQty"=>"$sameCountQty", "totle"=>"totle");
					$stuffIdChk = $StuffId;
					$sameStuffCount = $Qty;
				}
	           	
	           	//if ($SendSign==1) // SendSign: 0送货，1补货, 2备品 
	           	switch ($SendSign)
	           	{
					case 1:
						$thSql=mysql_query("SELECT SUM( S.Qty ) AS thQty  FROM $DataIn.ck2_thmain M  
											LEFT JOIN $DataIn.ck2_thsheet S ON S.Mid = M.Id
											WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);						   				$thQty=mysql_result($thSql,0,"thQty");
				
						//补货的数量 add by zx 2011-04-27
						$bcSql=mysql_query("SELECT SUM( S.Qty ) AS bcQty  FROM $DataIn.ck3_bcmain M 
											LEFT JOIN $DataIn.ck3_bcsheet S ON S.Mid = M.Id
											WHERE M.CompanyId = '$CompanyId' AND S.StuffId = '$StuffId' ",$link_id);
						$bcQty=mysql_result($bcSql,0,"bcQty");	
						$cgQty=$thQty-$bcQty;
						$noQty=$cgQty;
						$SignString="(补货)";
						$StockId="本次补货";
					break;
					case 2:
						$cgQty=0;
						$noQty=0;
						$SignString="(备品)";
						$StockId="本次备品";
					break;
					default :
						$rkTemp=mysql_query("SELECT IFNULL(SUM(R.Qty),0) AS Qty FROM $DataIn.ck1_rksheet R 
											 LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=R.StockId
											 WHERE R.StockId='$StockId'",$link_id);
						$rkQty=mysql_result($rkTemp,0,"Qty");	//收货总数
						$noQty=$cgQty-$rkQty;				
					break;
				}   
			
				if($noQty<=0  && $SendSign!=2)
				{   //当前已全部入库，则显示，入库数量
					$LockRemark="错误，请通知供应商:该需求单已全部入库，请核查该送货单！";
					$czSign=0;
				}
				else 
				{
					if($noQty<$Qty && $SendSign!=2)
					{  //当前送货量比未送货量还大,则强行要改
			    		$czSign=0;
			    		$LockRemark="错误，请通知供应商:本次送货的数量多于未送货的总数，送货数量需更新！";
			    	}
			    	else 
			    	{
					//$QtyStr="<input name='QTY[$i]' type='text' id='QTY$i' value='$Qty' size='9' class='QtyRight' onfocus='toTempValue(this);this.select()' onBlur='Indepot(this,$noQty)'>";
					//不在公司内修改，直接通知供应商修改
						$QtyStr=$Qty;
					}
				}
			
				$Picture = $subListRows["Picture"];
								
				switch($Picture)
				{
					case 0:
					{
						$LockRemark = "无配件标准图";
					}
					break;
					case 2:
					{
						$LockRemark = "审核中";
					}
					break;
					case 3:
					{
						$LockRemark = "需更新标准图";
					}
					break;
					case 4:
					{
						$LockRemark = "审核退回修改";
					}
					break;
				}

				$remarkSql=mysql_query("SELECT Remark FROM $DataIn.ck6_shremark WHERE ShId='$Id' LIMIT 1",$link_id);
				$Remark="";
				if($remarkRow=mysql_fetch_array($remarkSql))
				{
             		$Remark=$remarkRow["Remark"];
             	}
             	
             	//检查是否订单中最后一个需备料的配件
             	$isLastBgColor = "0";
			 	if(!$POrderId == "")
			 	{
			 		$FromPageName="sh";
					include "../../model/subprogram/stuff_blcheck.php";
				}
				else
				{
					$LastBgColor = "";
				}
			
				if($LastBgColor != "")
				{
					$isLastBgColor = "1";
				}
             	
             	
             	$cgQty = intval($cgQty);
             	$cgQtyCount = number_format($cgQty);
             	
             	$noQty = intval($noQty);
             	$noQtyCount = number_format($noQty);
             	
             	$Qty = intval($Qty);
             	//$billCount += $Qty;
             	$QtyCount = number_format($Qty);
             	
             	$historyOrder = intval($historyOrder);
             	$historyOrderCount = number_format($historyOrder);
                //$LastBgColor = str_replace("#", "", $LastBgColor);    	                 
             	$detailList[]= array("stockId"=>"$StockId", "stuffName"=>"$StuffCname", "cgQtyCount"=>"$cgQtyCount", "noQtyCount"=>"$noQtyCount", "qtyCount"=>"$QtyCount", "note"=>"$Remark", "Id"=>"$Id", "lockRemark"=>"$LockRemark", "picture"=>"$Picture", "stuffId"=>"$StuffId", "isLast"=>"$isLastBgColor", "history"=>"$historyOrderCount", "typeId"=>"$TypeId", "leadTime"=>"$leadTime", "piDate"=>$piDate, "property" => "$sProperty", "lastBackColor"=>"$LastBgColor");
			
            }
           	
           	$sameCountQty = intval($sameStuffCount);
			$sameCountQty = number_format($sameCountQty);
            $detailList[] = array("stuffIdChk"=>"$stuffIdChk", "sameCountQty"=>"$sameCountQty", "totle"=>"totle");
           	
            $gysList[] = array(array("$billNumber", "$billDate", "$CompanyId", "$forShort", "$totleBillQty"), $detailList);
        } 
    }
    
    echo json_encode($gysList);
	
?>