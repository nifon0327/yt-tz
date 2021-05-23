<?php 
//可备料
if ($info[1]==1) {
	include "order_ybl_read.php";	
} else {
$cztest = (true);
$chenLZ = ($LoginNumber == "10259" ||$LoginNumber == "10200" || $LoginNumber == "10341"|| $LoginNumber == "11965")?true: false;
		include "../../basic/downloadFileIP.php";
	$path = $_SERVER["DOCUMENT_ROOT"];
	include_once("$path/public/kqClass/Kq_dailyItem.php");
	$willShow = false;
		//echo $mySql;
		$eachStock = array();
		$ProductArray=array();
	
		$curDate=date("Y-m-d");
		$nextWeekDate=date("Y-m-d",strtotime("$curDate  +7day"));
		$nexNextWeekDate = date("Y-m-d",strtotime("$curDate  +14day"));
		$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$nextWeekDate',1) AS NextWeek, YEARWEEK('$curDate',1) AS ThisWeek, YEARWEEK('$nexNextWeekDate',1) AS nextNextWeek",$link_id));
		$nextWeek=$dateResult["NextWeek"];
		$thisWeek = $dateResult["ThisWeek"];
		$nextNextWeek = $dateResult["nextNextWeek"];	
		$SearchRows = " AND YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) < $nextNextWeek";
		//$SearchRows = '';
		$myOrderSql="SELECT M.CompanyId,S.OrderPO,M.OrderDate,M.ClientOrder,M.Operator,S.Id,S.POrderId,S.ProductId,S.Qty,S.Price,S.PackRemark,S.cgRemark,S.sgRemark
,S.DeliveryDate,S.ShipType,S.Estate,S.Locks,P.cName,P.eCode,P.Weight,P.TestStandard,P.pRemark,P.bjRemark,U.Name AS Unit,PI.PI,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime,E.Type ,C.CompanyId, C.Forshort, YEARWEEK(substring(IFNULL(PI.Leadtime,PL.Leadtime),1,10),1) AS Weeks
		FROM $DataIn.yw1_ordersheet S 
		LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
		LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId
		LEFT JOIN $DataPublic.packingunit U ON U.Id=P.PackingUnit 
		LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
		Left Join $DataIn.yw3_pileadtime PL On PL.POrderId = S.POrderId
		LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId  
		LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
		WHERE S.scFrom>0 AND S.Estate=1 
		$SearchRows
		GROUP BY S.POrderId ORDER BY Weeks,S.ProductId,S.Id";
		
		$myOrderResult = mysql_query($myOrderSql);
		$rowCount = $OverTotalQty = $totalQty = 0;
		while($myOrderRow = mysql_fetch_assoc($myOrderResult))
		{
			$blId=$myOrderRow["Id"];
			$OrderPO=($myOrderRow["OrderPO"]);
			$POrderId=$myOrderRow["POrderId"];
			//备料主单信息
			$cName=$myOrderRow["cName"];
			$TestStandard=$myOrderRow["TestStandard"];
			$ProductId=$myOrderRow["ProductId"];
			$Qty=$myOrderRow["Qty"];
			$Leadtime = $myOrderRow['Leadtime'];
			if($Leadtime == ""){
				continue;
			}

						
			// if($myOrderRow['Weeks'] == ""){
			// 	continue;
			// }

			$piDate = str_replace("*", "", $Leadtime);
			$Leadtime= substr($Leadtime, 5, 5);
			$OrderDate=$myOrderRow["OrderDate"];
			$productName = $myOrderRow["cName"];
			$companyId = $myOrderRow["CompanyId"];
			$companyShort = $myOrderRow["Forshort"];
			$weight = $myOrderRow["Weight"];
			
			$eCode = $myOrderRow["eCode"];
					   
					   $AppFilePath="http://www.middlecloud.com/download/teststandard/T" .$ProductId.".jpg";
					    $Weight=(float)$weight;
                    $WeightSTR="";
                    $productId=$ProductId;
                      include "../../model/subprogram/weightCalculate.php";
                      if ($Weight>0){
	                       $extraWeight=$extraWeight == "error"?"":$extraWeight+($Weight*$boxPcs); 
	                       $WeightSTR=$Weight>0?"$eCode|$Weight|$boxPcs|$extraWeight":"";
                      }
			$Unit = $myOrderRow["Unit"];
			
			$R_EType = $myOrderRow["Type"]==2?2:0;
			
			$hasLine = "";
			$line = "";
			$missionPartSql = "Select B.GroupName, C.Name,A.DateTime From $DataIn.sc1_mission A
							   Left Join $DataIn.staffgroup B On B.Id = A.Operator
							   Left Join $DataPublic.staffmain C On B.GroupLeader = C.Number
							   Where A.POrderId = '$POrderId'
							   And B.Estate = '1' Limit 1";
			$missionPartResult = mysql_query($missionPartSql);
			while($missionRow = mysql_fetch_assoc($missionPartResult))
			{
				$line = $missionRow["GroupName"];
				//$name = $missionRow["Name"];
				$timeFp = $missionRow["DateTime"];
				$hasLine = str_replace("组装", "", $line);
				
			}			
			
			//计算pi为第几周
			//echo "SELECT YEARWEEK('$piDate',1) AS Week";
			$piWeekResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$piDate',1) AS Week",$link_id));
			$piWeek = $piWeekResult["Week"];
			//是否已存在有可备料订单
			//$ProductArray[]=$ProductId;
			
			$isBlLockState = "no";
			if($piWeek == $nextNextWeek)
			{
				$isBlLockSql = mysql_query("Select Locks From $DataIn.yw9_blunlock Where WeekName = '$piWeek'");
				if(mysql_num_rows($isBlLockSql)==0)
				{
					$isBlLockState = "yes";
				}
				else
				{
					$isBlLockResult = mysql_fetch_assoc($isBlLockSql);
					$blLockState = $isBlLockResult["Locks"];
					if($blLockState == "1")
					{
						$isBlLockState = "yes";
					}
				}
			}
			
			//检查订单备料情况
			$CheckblState="
				SELECT SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1, SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2, SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,SUM(IF(GL.Id>0,1,0)) AS  Locks,SUM(IFNULL(L.llEstate,0)) AS llEstate  
				FROM $DataIn.cg1_stocksheet G 
				LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
				LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId 
				LEFT JOIN $DataIn.cg1_lockstock GL ON G.StockId=GL.StockId  AND GL.Locks=0 
				LEFT JOIN ( 
				    SELECT L.StockId,SUM(L.Qty) AS Qty,SUM(IF(L.Estate=1,1,0)) AS llEstate 
				    FROM  $DataIn.ck5_llsheet L 
				    WHERE  L.POrderId='$POrderId'  GROUP BY L.StockId 
				  )L ON L.StockId=G.StockId 
				WHERE G.POrderId='$POrderId' AND ST.mainType<2";
			
			$stockHead = mysql_query($CheckblState);
			if($mainRows = mysql_fetch_assoc($stockHead))
			{
			
				$mainBlQty = $mainRows["blQty"];
				$mainLlQty = $mainRows["llQty"];
				
				$R_K1 = $mainRows["K1"];
				$R_K2 = $mainRows["K2"];
				$R_blQty = $mainRows["blQty"];
				$R_llQty = $mainRows["llQty"];
				$R_Locks = $mainRows["Locks"];
				$R_llEstate = $mainRows["llEstate"];
				
				  $FromWebPage=$R_blQty==$R_llQty?"LBL":"KBL";
	             include "../../admin/order_datetime.php";
				 
				  $BlDate=$R_blQty==$R_llQty?$lbl_Date:$kbl_Date;
				  
				if ($R_blQty==$R_llQty)
				{
					if ($R_llEstate==0)
					{
						continue;
					} 
			    }
				else
				{
					
			        //是否已存在有可备料订单
				    if(in_array($ProductId, $ProductArray)) 
				    {
				    	continue;
				    }
				    
					
			        if ($R_EType==2) 
			        {
			        continue;}
			        
					if ($R_K1>=$R_K2 &&  $R_blQty!=$R_llQty && $R_Locks==0)
					{
					    $ProductArray[] = $ProductId;
					}
					else
					{
						//$ProductArray[] = $ProductId;
						continue;
					}
			    }
			  
						
				//echo "$productId bl:$mainBlQty ll:$mainLlQty <br>";
				if($TestStandard == "1")
				{
					$checkteststandard=mysql_query("SELECT Type FROM $DataIn.yw2_orderteststandard WHERE POrderId='$POrderId' AND Type='9' ORDER BY Id Limit 1",$link_id);
					if($checkteststandardRow = mysql_fetch_array($checkteststandard))
					{
						$TestStandard = 3;
					}
				}
			
				$canLlState = "yes";
				if($mainBlQty != $mainLlQty)
				{
					$canLlState = "no";
					continue;
				}
				else
				{
					if($hasLine == "")
					{
						continue;
					}
				}
				
			/*
				$boxSql = "SELECT D.Spec,A.Relation   
						   FROM $DataIn.pands A,$DataIn.stuffdata D 
						   where A.ProductId='$ProductId' 
						   AND D.TypeId = '9040' 
						   and D.StuffId=A.StuffId 
						   ORDER BY A.Id";	
				$boxResult = mysql_query($boxSql);
				$boxRow = mysql_fetch_assoc($boxResult);
				$relation = explode("/", $boxRow["Relation"]);
				$boxPcs = ($relation[1] == "")?"-":$relation[1];
			
				$bl_cycleIpad = "";
				*/
				include"../../admin/order_date.php";
				//如果超过30天
				//$AskDay=AskDay($OrderDate);
			
				//$OrderDate=CountDays($OrderDate,0);
				//加急订单锁定操作，整单锁和单个配件锁都不能备料
				/*
				$Lock_Result=mysql_query("SELECT POrderId FROM $DataIn.yw2_orderexpress   WHERE POrderId='$POrderId' AND Type='2'
                                  					    UNION ALL
                                  					    SELECT POrderId FROM (SELECT LEFT(GL.StockId,12) AS POrderId,GL.Locks 
                                  					    FROM $DataIn.cg1_lockstock GL,$DataIn.cg1_stocksheet G 
                                  					    WHERE GL.Locks=0 
                                  					    AND GL.StockId=G.StockId GROUP BY POrderId) K 
                                  					    WHERE K.POrderId='$POrderId'");
			
				$isLockState = "no";		
			    if (mysql_num_rows($Lock_Result) > 0) 
				{
   	            	$isLockState = "yes";	         
   				}           
            */
   				/////
				/*  *         
   				$checkTasksQty=mysql_query("SELECT Qty AS TasksQty FROM $DataIn.sc3_printtasks WHERE POrderId='$POrderId' AND (CodeType=1 OR CodeType=2 OR CodeType=4)",$link_id);
   				if (mysql_num_rows($checkTasksQty)>0)
   				{
					$TasksQty=mysql_result($checkTasksQty,0,"TasksQty");
				}
				else
				{
					$TasksQty=0;
				}

           */
		
				//$bl_cycleIpad = ($bl_cycleIpad == "0")?"当天":$bl_cycleIpad."day(s)";
				$bgColor = $thisWeek > $piWeek ? "#FF0000":"";
				$OverTotalQty += $thisWeek > $piWeek ? $Qty : 0;
				$totalQty += $Qty;
				$piWeek = $piWeek >0 ? substr($piWeek,4,2) : "";
				 $odDays=(strtotime($curDate)-strtotime($OrderDate))/3600/24;
                    include "order/order_TestStandard.php";
					$BlDate = GetDateTimeOutString($timeFp==NULL?$BlDate:$timeFp,'');
					if ($willShow==false) {
						if ((strtotime($curDate)-strtotime($timeFp))/60>3) {
							$willShow=true;
						}
					}
					$rowColor = $onHome == 1?"F6F6F6":"";
			 $tempArray=array(
                      "Id"=>"$POrderId","line"=>"$hasLine",
                      "RowSet"=>array("bgColor"=>"$rowColor"),
                       "weeks"=>array("Text"=>"$piWeek","bg"=>"$bgColor","iIcon"=>"$Locks"),
                      "Title"=>array("Text"=>"$cName","Color"=>"$TestStandardColor"),
                      "Col1"=> array("Text"=>"$odDays"."d","Color"=>"#358FC1"),
					  "Col2"=> array("Text"=>"$companyShort","Color"=>"#358FC1"),"Col3"=> array("Text"=>"$OrderPO"),
                      "Col4"=>array("Text"=>"$Qty","bgColor"=>"$FactualQty_Color"),
                      "Col5"=>array("Text"=>"$BlDate","Color"=>"#858888"),"icon4"=>"scdj_11"
                      //"Remark"=>array("Text"=>"$Remark"),"icon4"=>"scdj_11",
                        //"rTopTitle"=>array("Text"=>"$odDays"."d","Color"=>"#358FC1"),
                       
                   );
				   $rowCount++;
				   $jsonArray[]=array("Tag"=>"data","data"=>$tempArray,"CellID"=>"dat1","Args"=>"$POrderId","load"=>"",
				   "onTap"=>array("hidden"=>"1","shrink"=>"UpAccessory_blue","value"=>"1","Args"=>"$POrderId|$ProductId"),"List"=>array(),"Swap"=>$chenLZ?array("Right"=>"FF0000-取消分配"):array(),"sbID"=>"acce11",
				   "Tap"=>"0",
				   "TapImg"=>array("File"=>"$AppFilePath","Args"=>"$WeightSTR")
				   );

			}
		}
		 $tempArray2= array();
		 $totalQty = number_format($totalQty);
		 $dblList = $jsonArray;
		 $OverTotalQty = $OverTotalQty >0 ? number_format($OverTotalQty):"";
		  //$totalQty=$rowCount>0?"$totalQty($rowCount)":$totalQty;
        $tempArray = array("Title"=>array("Text"=>"总计","FontSize"=>"14","Bold"=>"1"),"Col2"=>array("Text"=>"$OverTotalQty","Color"=>"#FF0000","FontSize"=>"14","Frame"=>"115,10,70,15"),"Col3"=>array("Text"=>$rowCount>0?"$totalQty($rowCount)":$totalQty,"FontSize"=>"14"));
		$tempArray2[] = array("Tag"=>"total","CellID"=>"total","data"=>$tempArray);
		array_splice($jsonArray,0,0,$tempArray2);
	
	//$CHECK_TODAY = date("Y-m-d");
	$QEstateCt = 0;
	$yblSql = mysql_query("select count(1) as QEstate from (
SELECT 1 as aaa
FROM $DataIn.ck5_llsheet S 
LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.StockId
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=G.POrderId
LEFT JOIN $DataIn.sc1_mission A on A.POrderId =G.POrderId 
Left join $DataIn.ck5_llconfirm Q on Q.POrderId=Y.POrderId
WHERE  (Q.Estate is NULL or Q.Estate!=0) AND S.Estate=0 and S.Date='$curDate' and A.Id is not null AND G.POrderId>0 Group BY G.POrderId) A");
if ($yblSqlRow = mysql_fetch_assoc($yblSql)) {
	$QEstateCt = $yblSqlRow["QEstate"];
	
}

	
		$jsonArray = array("cellList"=>$jsonArray,$cztest?"Segment":"-"=>array("Segmented"=>array("待备料($rowCount)","已备料($QEstateCt)"),"SegmentedId"=>array("0","1"),"SegmentIndex"=>"0"));
		//echo json_encode($eachStock);
		//print_r($eachStock);
}
?>