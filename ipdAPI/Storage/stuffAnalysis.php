<?php
	
	include_once "../../basic/parameter.inc";
	$StuffId = $_POST["stuffId"];
	//$StuffId = "121345";
	//exit();
	$CheckSql=mysql_query("SELECT S.dStockQty,S.tStockQty,S.oStockQty,S.mStockQty,D.StuffId,D.StuffCname 
						   FROM $DataIn.ck9_stocksheet S,$DataIn.stuffdata D 
						   WHERE D.StuffId='$StuffId' AND S.StuffId=D.stuffId",$link_id);
	
	if($CheckRow = mysql_fetch_array($CheckSql))	
	{
		$dStockQty=$CheckRow["dStockQty"];
		$tStockQty=$CheckRow["tStockQty"];
		$oStockQty=$CheckRow["oStockQty"];
		$StuffId=$CheckRow["StuffId"];
		$mStockQty=$CheckRow["mStockQty"];
		$StuffCname=$CheckRow["StuffCname"];
		$thisDate=date("Y-m-d");
	}
	
	$stuffState = array("$StuffCname", "初始库存:$dStockQty", "最低库存:$mStockQty", "报表日期:$thisDate");
	
	$UnionSTR="SELECT M.OrderDate AS Date,concat('1') AS Sign,SUM(G.OrderQty) AS Qty 
			   FROM $DataIn.cg1_stocksheet G
			   LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=G.POrderId
			   LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber
			   WHERE G.StuffId='$StuffId' 
			   AND G.POrderId!='' GROUP BY M.OrderDate";

	//采购数据（包括已下采购单和没下采购单）
	$UnionSTR.="
				UNION ALL
				SELECT M.OrderDate AS Date,concat('2') AS Sign,SUM(G.FactualQty+G.AddQty) AS Qty 
				FROM $DataIn.cg1_stocksheet G,$DataIn.yw1_ordermain M,$DataIn.yw1_ordersheet S
				WHERE S.POrderId=G.POrderId AND S.OrderNumber=M.OrderNumber AND G.StuffId='$StuffId' GROUP BY M.OrderDate";

    //已下采购单的特采数据
	$UnionSTR.="
				UNION ALL
				SELECT M.Date,concat('3') AS Sign,SUM(S.FactualQty+S.AddQty) AS Qty 
				FROM $DataIn.cg1_stocksheet S,$DataIn.cg1_stockmain M 
				WHERE S.StuffId='$StuffId' 
				AND S.POrderId='' 
				AND S.FactualQty>0 	
				AND M.Id=S.Mid GROUP BY M.Date";

	//未下采购单的特采数据
	$UnionSTR.="
				UNION ALL
				SELECT concat('0000-00-00') AS Date,concat('3') AS Sign,IFNULL(SUM(FactualQty+AddQty),0) AS Qty 
				FROM $DataIn.cg1_stocksheet 
				WHERE Mid=0 
				AND StuffId='$StuffId' 
				AND POrderId=''";

    //入库数据
	$UnionSTR.="
				UNION ALL
				SELECT M.Date,concat('4') AS Sign,SUM(R.Qty) AS Qty 
				FROM $DataIn.ck1_rksheet R 
				LEFT JOIN $DataIn.ck1_rkmain M ON R.Mid=M.Id 
				WHERE R.StuffId='$StuffId' GROUP BY M.Date";


    //备品转入数据
	$UnionSTR.="
				UNION ALL
				SELECT Date,concat('5') AS Sign,SUM(Qty) AS Qty 
				FROM $DataIn.ck7_bprk 
				WHERE StuffId='$StuffId' GROUP BY Date";

	//领料数据
	$UnionSTR.="
				UNION ALL
				SELECT A.Date,concat('6') AS Sign,SUM(A.Qty) AS Qty FROM (
				SELECT IFNULL(DATE_FORMAT(M.Date,'%Y-%m-%d'),DATE_FORMAT(B.Date,'%Y-%m-%d')) AS Date,S.Qty 
				FROM $DataIn.ck5_llsheet S 
				LEFT JOIN $DataIn.ck5_llmain M ON S.Mid=M.Id 
				LEFT JOIN  $DataIn.yw9_blmain B ON S.Pid=B.Id 
				WHERE S.StuffId='$StuffId') A GROUP BY  DATE_FORMAT(A.Date,'%Y-%m-%d')";

    //已出货数据
	$UnionSTR.="
				UNION ALL
				SELECT M.Date,concat('7') AS Sign,SUM(S.Qty) AS Qty 
				FROM $DataIn.ck5_llsheet S LEFT JOIN $DataIn.ck5_llmain M ON S.Mid=M.Id 
				LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId 
				LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId 
				WHERE S.StuffId='$StuffId' 
				AND Y.Estate=0 GROUP BY M.Date";

    //报废数据,只有审核通过的才算 modify by zx 2010-11-30
	$UnionSTR.="
				UNION ALL
				SELECT Date,concat('8') AS Sign,SUM(Qty) AS Qty 
				FROM $DataIn.ck8_bfsheet 
				WHERE Estate=0 
				AND StuffId='$StuffId' GROUP BY Date";

    //退换数据
	$UnionSTR.="
				UNION ALL
				SELECT M.Date,concat('9') AS Sign,SUM(S.Qty) AS Qty 
				FROM $DataIn.ck2_thsheet S 
				LEFT JOIN $DataIn.ck2_thmain M ON S.Mid=M.Id 
				WHERE S.StuffId='$StuffId' GROUP BY M.Date";

    //补仓数据
	$UnionSTR.="
				UNION ALL
				SELECT M.Date,concat('10') AS Sign,SUM(S.Qty) AS Qty 
				FROM $DataIn.ck3_bcsheet S 
				LEFT JOIN $DataIn.ck3_bcmain M ON S.Mid=M.Id 
				WHERE S.StuffId='$StuffId' GROUP BY M.Date";
				
		
	$result = mysql_query($UnionSTR,$link_id);
	
	$DateTemp=array();
	$QtyTemp=array();
	$SignTemp=array();
	
	$analysisData = array();
	$originalData = array();
	
	$sum1=0;	
	$sum2=0;	
	$sum3=0;	
	$sum4=0;	
	$sum5=0;
	$sum6=0;	
	$sum7=0;	
	$sum8=0;	
	$sum9=0;  
	$sum10=0;
	
	if($myrow = mysql_fetch_array($result))
	{
		do
		{
			$Qty= $myrow["Qty"];
			$Sign= $myrow["Sign"];
			if($myrow["Date"]=="")
			{
			  $Date="0000-00-00";
			}
			else
			{
				$Date=substr($myrow["Date"],0,10);
			}
		
			if($Qty>0 or $Qty<0)
			{
				$DateTemp[]=$Date;
				$QtyTemp[]=$Qty;
				$SignTemp[]=$Sign;	
				
				$hasSame = false;
				for($i=0; $i<count($originalData); $i++)
				{
					$single = $originalData[$i];
					if($single[0] == $Date)
					{
						$originalData[$i][1][] = array($Qty, $Sign);
						$hasSame = true;
						break;
					}
				}
				
				if(!$hasSame)
				{
					$dataArray = array();
					$dataArray[] = array($Qty, $Sign);
					$originalData[] = array($Date,$dataArray);
				}
				
			}
		}while ($myrow = mysql_fetch_array($result));		
	}
	
	array_multisort($originalData, SORT_ASC);
	$c2TEMP=$dStockQty;//当天在库,初始值为初始库存
	$c3TEMP=$dStockQty;//当天可用库存,初始值为初始库存
	
	//print_r($originalData);
	
	for($j=0; $j<count($originalData); $j++)
	{
		$analysisQueue = array();
		$number = $j+1;
		$analysisQueue[] = "$number";
		$analysisQueue[] = $originalData[$j][0];
		
		for($k=1; $k<13; $k++)
		{
			if($k != 11 && $k != 12)
			{
				$hasData = false;
				for($z=0; $z< count($originalData[$j][1]); $z++)
				{
					$data = $originalData[$j][1][$z];
					if($data[1] == $k)
					{	
						$tempQty = $data[0];
						$analysisQueue[] = "$tempQty";
						$hasData = true;
						switch($k)
						{
							case 1://订单数量
								$c3TEMP=$c3TEMP-$tempQty;$sum1=$sum1+$tempQty;
							break;
							case 2://采购数量
								$sum2=$sum2+$tempQty;	$c3TEMP=$c3TEMP+$tempQty;
							break;
							case 3://特采数量
								$sum3=$sum3+$tempQty;	$c3TEMP=$c3TEMP+$tempQty;
							break;
							case 4://入库数量
								$c2TEMP=$c2TEMP+$tempQty;	$sum4=$sum4+$tempQty;
							break;
							case 5://备品转入
								$c2TEMP=$c2TEMP+$tempQty;	$sum5=$sum5+$tempQty;	$c3TEMP=$c3TEMP+$tempQty;
							break;
							case 6://领料数量
								$c2TEMP=$c2TEMP-$tempQty;	$sum6=$sum6+$tempQty;
							break;
							case 7: //已出货数量
								$sum7=$sum7+$tempQty;
							break;
							case 8://报废数量
								$c2TEMP=$c2TEMP-$tempQty;	$sum8=$sum8+$tempQty; $c3TEMP=$c3TEMP-$tempQty;
							break;
							case 9://退换数量
								$c2TEMP=$c2TEMP-$tempQty;	$sum9=$sum9+$tempQty;
							break;
							case 10://补仓数量
								$c2TEMP=$c2TEMP+$tempQty;	$sum10=$sum10+$tempQty;	
								$c3TEMP=$c3TEMP>=0?$c3TEMP:0;
							break;
						}
						break;
					}
				}
				
				if(!$hasData)				
				{
					$analysisQueue[] = "";
				}	
			}
			else if($k == 11)
			{
				$analysisQueue[] = ($c2TEMP=="")?"0":"$c2TEMP";
			}
			else if($k == 12)
			{
				$analysisQueue[] = ($c3Row=="")?"0":"$c3TEMP";
			}
		}
		
		$analysisData[] = $analysisQueue;
		
	}
	
	$totle = array("合计", "", "$sum1", "$sum2", "$sum3", "$sum4", "$sum5", "$sum6", "$sum7", "$sum8" ,"$sum9" ,"$sum10", "$c2TEMP", "$c3TEMP");
	
	//在库
	$tState = ($tStockQty==$c2TEMP && $tStockQty>=0)?"正确":"不正确";
	$tArray = array("在库", "$tStockQty", "$c2TEMP", "$tState");
	
	//可用库存
	$orderState = ($oStockQty==$c3TEMP)?"正确":"不正确";
	$orderArray = array("可用库存", "$oStockQty", "$c3TEMP", "$orderState");

	
	echo json_encode(array($analysisData, $totle, $tArray, $orderArray, $stuffState));
?>