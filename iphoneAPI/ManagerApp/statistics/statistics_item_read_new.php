<?php 
//统计数据
$ReadAccessSign=2;
include "user_access.php";  //用户权限

$dataArray=array(); 
$rowHeight=45;

$colorNameText 			= "Text";
$colorNameTextTitle 	= "TextTitle";
$colorNameTextNeg 		= "TextNeg";
$colorNameTextPos 		= "TextPos";
$colorNameTextPerc 		= "TextPercent";
$colorNameTextNormal 	= "TextNormal";
$colorNameTextDesc 		= "TextDesc";
$colorNameTextDesc1		= "TextDesc1";
$colorNameTextTag 		= "TagText";
$colorNameBGPositive 	= "BGPositive";
$colorNameBGNegtive 	= "BGNegtive";
$colorNameBGDesc		= "BGDesc";

$colorList = array(
	$colorNameText		=> "#3C2F4F",
	$colorNameTextTitle	=> "#3C2F4F",
	$colorNameTextNeg	=> "#FF0000",//"#BF071C",
	$colorNameTextPos	=> "#1ECE6D",
	$colorNameTextPerc	=> "#1ECE6D",
	$colorNameTextNormal => "#2C97DE",
	$colorNameTextDesc 	=> "#88B9D7",
	$colorNameTextDesc1 => "#FFFFFF",
	$colorNameTextTag	=> "#FFFFFF",
	$colorNameBGPositive => "#86B9D8",
	$colorNameBGNegtive	=> "#FF0000",//"#FAA8A4",
	$colorNameBGDesc	=> "#FFFFFF",
);


$thisYear = date("Y");
$thisMonth = strtoupper(date("M"));
$lastMonth = strtoupper(date("M", mktime(0, 0, 0, date("m"),date("d") - date("t"), date("Y")))); 

$keyChartId = "id";
$keyColor = "color";
$keyValue = "value";

switch($NextPage){
    case 1:  
        if (in_array("120",$itemArray)) {
            //现金结存
            include "../../desk/subtask/subtask-120.php";
            
            //RMB >> $SumAmount1            
            //USD >> $SumAmount2            
            //others... >> $SumTotal - ($SumAmount1 + $SumAmount2)
            $keyOther = "other";
            $keyRMB = "rmb";
            $keyUSD = "usd";
            
            $colorRMB = "#3A90BF";
            $colorUSD = "#88B9D7";
            $colorOther = "#D0E3EF";
            
            $chartTitle = array(
            	"Title" => array(
            		"Text" => "现金结余",
            		"Color" => $colorNameTextTitle,
            	)
            );
            
            $chartDesc = array(
            	"Data" => array(
            		$keyRMB => array(
            			"Text"	=> "RMB",
            			"Color"	=> $colorNameTextDesc1,
            			"BGColor" => $colorRMB
            		),
            		$keyUSD => array(
            			"Text"	=> "USD",
            			"Color"	=> $colorNameTextDesc1,
            			"BGColor" => $colorUSD
            		),
            		$keyOther => array(
            			"Text"	=> "•••",
            			"Color"	=> $colorNameTextDesc1,
            			"BGColor" => $colorOther
            		)
            	)
            );
            
/*             echo "123<br>$USDRate<br>"; */
			$chart = array(
            	"Title"	=> $chartTitle,
            	"Data"	=> array(
            		array(
            			$keyChartId => $keyRMB,
            			$keyColor	=> $colorRMB,
            			$keyValue	=> $SumAmount1,
            		),
            		array(
            			$keyChartId => $keyUSD,
            			$keyColor	=> $colorUSD,
            			$keyValue	=> $SumAmount2 * $USDRate,
            		),
            		array(
            			$keyChartId => $keyOther,
            			$keyColor	=> $colorOther,
            			$keyValue	=> $SumTotal - ($SumAmount1 + $SumAmount2* $USDRate),
            		),
            	),
            	"Desc"	=> $chartDesc,
            );
            
            $list = array(
            	"Col1"	=> array(
            		"Text" => "¥".number_format($SumAmount1),
            	), 
            	"Col2"	=> array(
            		"Text" => "$".number_format($SumAmount2),
            	),
            	"Col3"	=> array(
            		"Text" => "¥".number_format($SumTotal - ($SumAmount1 + ($SumAmount2 * $USDRate))),
            	),
            	"Col4"	=> array(
            		"Text" => "¥".number_format($SumTotal),
            	)
            );
            
            $layout = array(
            	"Col1" => array(
            		"Frame" => "210, 22, 85, 15",
            		"Align"	=> "R"
            	),
            	"Col2" => array(
            		"Frame" => "210, 42, 85, 15",
            		"Align"	=> "R"
            	),
            	"Col3" => array(
            		"Frame" => "210, 62, 85, 15",
            		"Align"	=> "R"
            	),
            	"Col4" => array(
            		"Frame" => "210, 98, 85, 15",
            		"Align"	=> "R"
            	),
            );
            
            $dataArray[] = array(
	            "View"		=> "PieChart",
	            "ModuleId"	=> "120",
	            "RowSet"	=> array(
	            	"Separator" => "0",
	            	"Height" => "128	"
	            ),
	            "ChartData"	=> $chart,
	            "ListData"	=> $list,
	            "Layout"	=> $layout,
	         );
	         
        }
      
        $otherNoPayData = array();
		if (in_array("121", $itemArray) || in_array("227", $itemArray)) {
		
			//审核通过未结付总额
			if (in_array("121", $itemArray)) {
				include "../../desk/subtask/subtask-121.php";//$noPayRMB
			}
			
			//全部货款
			$Result1183 = mysql_fetch_array(mysql_query("
					SELECT SUM(S.Amount*D.Rate) AS NoPay 
					FROM $DataIn.cw1_fkoutsheet S
					LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
					LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency
			        WHERE S.Estate=3 ", $link_id));
			$FK_NoPay = $Result1183["NoPay"] == "" ? 0 : $Result1183["NoPay"];
			
			 
			 $djResult1183=mysql_fetch_array(mysql_query("SELECT SUM(IFNULL(A.Amount,0)) AS Amount FROM (
			        SELECT SUM(S.Amount*C.Rate) AS Amount   
					FROM cw2_fkdjsheet S 
					INNER JOIN trade_object P ON P.CompanyId=S.CompanyId
					INNER JOIN currencydata C ON C.Id=P.Currency
					WHERE S.Did='0' AND S.Estate=0 
			   UNION ALL 
					SELECT IFNULL(SUM(T.Amount*C.Rate),0) AS Amount  
					FROM $DataIn.cw15_gyskksheet T 
					LEFT JOIN $DataIn.cw15_gyskkmain M ON T.Mid=M.Id 
					LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId
					LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency WHERE 1 AND M.Estate=0 AND T.Kid=0
			   UNION ALL 
			        SELECT IFNULL(SUM(M.OutAmount),0) AS Amount  FROM $DataIn.cw16_modelfee M  WHERE M.Estate=3 
			   UNION ALL 
			        SELECT SUM(M.Taxamount ) AS Amount FROM $DataIn.cw14_mdtaxmain M WHERE  M.Estate=3
					)A ", $link_id));
					
			$dj_Payed = $djResult1183["Amount"] == "" ? 0 : $djResult1183["Amount"];
			$FK_NoPay -= $dj_Payed;
			
			 
			
			//未付貨款
			
			$notPaySql = "SELECT 
							SUM(CASE WHEN N.GysPayMode = 0 THEN 
								CASE WHEN N.MonthDiff > 1
							        THEN N.Amount ELSE 0 END
							     WHEN N.GysPayMode = 1 THEN
								CASE WHEN N.MonthDiff > -1
							        THEN N.Amount ELSE 0 END
							     WHEN N.GysPayMode = 2 THEN
								CASE WHEN N.MonthDiff > 2
							        THEN N.Amount ELSE 0 END
							     WHEN N.GysPayMode = 8 THEN  
								CASE WHEN N.MonthDiff >  -1
							        THEN N.Amount ELSE 0 END
							     ELSE 0
							END) AS NotPay
							FROM(
							SELECT S.Month, SUM(S.Amount* C.Rate) AS Amount ,SUM(S.Amount) AS NoCAmount,C.preChar, 
							P.PayMode, P.GysPayMode, P.Prepayment, S.CompanyId,
							 PERIOD_DIFF(DATE_FORMAT(CURDATE(), '%Y%m'), REPLACE(S.Month, '-', '')) AS MonthDiff
							FROM $DataIn.cw1_fkoutsheet S
							LEFT JOIN $DataIn.trade_object P ON P.CompanyId = S.CompanyId
							LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency 
							WHERE S.Estate = 3 AND  Amount > 0 
							GROUP BY S.CompanyId, S.Month ORDER BY S.CompanyId) AS N";
					
			$notPayResult = mysql_fetch_array(mysql_query($notPaySql, $link_id));
			$notPayAmount = $notPayResult["NotPay"];
			
			//未付
			if (in_array("227", $itemArray)) {		
				
	            $keyNotPay = "notPay";
	            $keyDownPay = "downPay";
	            
	            $colorNotPay = "#C8DFED";
	            $colorDownPay = "#FF0000";
	            
	            $percent = ($notPayAmount / $FK_NoPay) * 100;
	            
	            $chartTitle = array(
	            	"Title" => array(
	            		"Text" => "应付",
	            		"Color" => $colorNameTextTitle,
	            	),
	            	"SubTitle" => array(
	            		"Text" => sprintf("%.0f%%", $percent),
	            		"Color" => $colorDownPay,
	            	)
	            );
	            
	            $chart = array(
	            	"Title"	=> $chartTitle,
	            	"Data"	=> array(
	            		array(
	            			$keyChartId	=> $keyDownPay,
	            			$keyColor	=> $colorDownPay,
	            			$keyValue	=> $percent,
	            		),
	            		array(
	            			$keyChartId	=> $keyNotPay,
	            			$keyColor	=> $colorNotPay,
	            			$keyValue	=> 100 - $percent,
	            		),
	            	),
	            	"Desc"	=> array(),
	            );
	            
	            
	            $list = array(
	            	"Col1"	=> array(
	            		"Text" => "¥".number_format($notPayAmount),
	            		"Color" => $colorNameTextNeg,
	            	), 
	            	"Col2"	=> array(
	            		"Text" => "¥".number_format($FK_NoPay),
	            	),
	            );
	            
	            $layout = array(
	            	"Col1" => array(
	            		"Frame" => "210, 25, 85, 15",
	            		"Align"	=> "R",
	            	),
	            	"Col2" => array(
	            		"Frame" => "210, 50, 85, 15",
	            		"Align"	=> "R",
	            	),
	            );
	            
				$dataArray[] = array(
					"View"		=> "DonutChart",
					"ModuleId"	=> "11830",
					"onTap"		=> array(
						"Title" => "应付",
						"Value" => "1",
						"Tag" => "multi",
						"Args" => ""
					),
					"RowSet"	=> array(
						"Separator" => "0.5",
						"Height" => "90"
					),
		            "ChartData"	=> $chart,
		            "ListData"	=> $list,
		            "Layout"	=> $layout,
				);
		
			}
		
			//其它未付
			if (in_array("121", $itemArray)) {
						        
				$SumTotalValue = number_format($noPayRMB - $FK_NoPay);
				
				$otherNoPayData["View"] = "List";
				$otherNoPayData["ModuleId"] = "121";
				$otherNoPayData["RowSet"] = array(
					"Separator" => "0.5",
					"Height" => "$rowHeight",
				);
				
				$otherNoPayData["ChartData"] = array();
				$otherNoPayData["ListData"] = array(
					"Title" => array(
						"Text" => "其他未付",
						"Color" => $colorNameTextTitle,
					),
	            	"Col1"	=> array(
	            		"Text" => "¥".$SumTotalValue,
	            	), 
				);
				$otherNoPayData["Layout"] = array(
	            	"Title" => array(
	            		"Frame" => "0, 0, 105, $rowHeight",
	            		"Align"	=> "M",
	            	),
	            	"Col1" => array(
	            		"Frame" => "210, 0, 85, $rowHeight",
	            		"Align"	=> "R",
	            	),
				);
			}
		}


         if (in_array("122", $itemArray)) {
         
            //未收客户货款总额
            include "../../desk/subtask/subtask-122.php";
            $SumTotalValue = number_format($GatheringSUM);
            
            $sqlStr = "SELECT IFNULL(SUM(R.Amount * D.Rate),0) FK_JY 
						FROM $DataIn.cw6_advancesreceived R
						LEFT JOIN $DataIn.trade_object C ON C.CompanyId = R.CompanyId
						LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
						WHERE Mid='0'
						AND EXISTS (
							SELECT S.CompanyId FROM $DataIn.ch1_shipmain S 
						        	WHERE S.Estate = 0 AND S.cwSign IN (1,2) AND S.CompanyId = R.CompanyId 
					        UNION ALL
					        SELECT M.CompanyId FROM $DataIn.cw6_advancesreceived M 
						        	WHERE M.Mid=0 AND M.CompanyId = R.CompanyId)";
			$prePayRow = mysql_fetch_array(mysql_query($sqlStr, $link_id));
			$prePayAmount = $prePayRow["FK_JY"];
			
			//读取未收货款
			$unreceiveCount = 0;
			$ShipResult = mysql_query("SELECT * FROM 
											(SELECT M.CompanyId, D.Rate, C.PayMode, C.Forshort
											FROM $DataIn.ch1_shipmain M
											LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
											LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
											LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
											WHERE M.Estate =0 AND M.cwSign IN (1,2) GROUP BY M.CompanyId 
												UNION ALL 
											SELECT M.CompanyId, D.Rate, C.PayMode, C.Forshort
											FROM $DataIn.cw6_advancesreceived M
											LEFT JOIN $DataIn.trade_object C ON  M.CompanyId=C.CompanyId
											LEFT JOIN $DataPublic.currencydata D   ON C.Currency=D.Id
											WHERE M.Mid=0 GROUP BY CompanyId) A  
										GROUP BY A.CompanyId", $link_id);	

			if ($ShipRow = mysql_fetch_array($ShipResult)) {
				do {
					$CompanyId = $ShipRow["CompanyId"];
					$Rate = $ShipRow["Rate"];
					$PayMode = $ShipRow["PayMode"];
					$CompanyName = $ShipRow["Forshort"];
					$companyAmount = 0;

				    $MonthResult= mysql_query("SELECT SUM(S.Price*S.Qty*M.Sign) AS Amount, DATE_FORMAT(M.Date,'%Y-%m') AS Month,D.preChar, C.PayMode,
												PERIOD_DIFF(DATE_FORMAT(CURDATE(), '%Y%m'), DATE_FORMAT(M.Date,'%Y%m')) AS MonthDiff
										        FROM $DataIn.ch1_shipmain M
										        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
										        LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
										        LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
										        WHERE M.Estate=0 AND M.cwSign IN (1,2) AND M.CompanyId='$CompanyId' 
										        GROUP BY DATE_FORMAT(M.Date,'%Y-%m') ORDER BY M.Date", $link_id);

			        
			        while($MonthRow = mysql_fetch_array($MonthResult)) {
			        	
				        $monthDiff = $MonthRow["MonthDiff"];
				        $compareMonthDiff = 0;
				        $PayMode = $MonthRow["PayMode"];
				        switch ($PayMode) {
					        case 7:
					        	$compareMonthDiff = 1;
					        break;
					        case 1:
					        	$compareMonthDiff = -1;
					        break;
					        case 3:
					        	$compareMonthDiff = -1;
					        break;
					        case 2:
					        	$compareMonthDiff = 0;
					        break;
					        case 4:
					        	$compareMonthDiff = -1;
					        break;
					        case 5:
					        	$compareMonthDiff = 1;
								//$compareMonthDiff = 0;
					        break;
					        case 8:
					        	$compareMonthDiff = 6;
					        break;
					        case 6:
					        	$compareMonthDiff = -1;
					        break;
					           case 7:
					        	$compareMonthDiff = 1;
					        break;
					        case 11:
					        	$compareMonthDiff = -1;
					        break;
					        case 12:
					        	$compareMonthDiff = 1;
					        break;
					        case 13:
					        case 15:
					        case 16:
					            $compareMonthDiff =1;
					          break;
					        case 17:
					        case 18:
					        	$compareMonthDiff =2;
					        break;
				        }
				        
				        if ($monthDiff > $compareMonthDiff) {
							$unreceiveCount += $MonthRow["Amount"] * $Rate;
							$companyAmount += $MonthRow["Amount"] * $Rate;					        
				        }
					}
					
/* 					echo $CompanyName."(".$CompanyId.") :: ".$companyAmount."<br>"; */
					
				} while ($ShipRow = mysql_fetch_array($ShipResult));
			}
            
            //未收
            $keyNotReceive = "notReceive";
            $keyPrePay = "prePay";
            
            $colorNotReceive = "#C8DFED";
            $colorPrePay = "#FF0000";
            
            $percent = $GatheringSUM>0?($unreceiveCount / $GatheringSUM) * 100:0;
            $colorDownPay=$colorDownPay==""?"":$colorDownPay;
            $chartTitle = array(
            	"Title" => array(
            		"Text" => "应收",
            		"Color" => $colorNameTextTitle,
            	),
            	"SubTitle" => array(
            		"Text" => sprintf("%.0f%%", $percent),
            		"Color" => $colorDownPay,
            	)
            );
            
            $chart = array(
            	"Title"	=> $chartTitle,
            	"Data"	=> array(
            		array(
            			$keyChartId	=> $keyPrePay,
            			$keyColor	=> $colorPrePay,
            			$keyValue	=> $percent,
            		),
            		array(
            			$keyChartId	=> $keyNotReceive,
            			$keyColor	=> $colorNotReceive,
            			$keyValue	=> 100 - $percent,
            		),
            	),
            	"Desc"	=> array(),
            );
            
            
            $list = array(
            	"Col1"	=> array(
            		"Text" => "¥".number_format($unreceiveCount),
            		"Color" => $colorNameTextNeg,
            	), 
            	"Col2"	=> array(
            		"Text" => "¥".number_format($GatheringSUM),
            	),
            );
            
            $layout = array(
            	"Col1" => array(
            		"Frame" => "210, 25, 85, 15",
            		"Align"	=> "R",
            	),
            	"Col2" => array(
            		"Frame" => "210, 50, 85, 15",
            		"Align"	=> "R",
            	),
            );
            
			$dataArray[] = array(
				"View"		=> "DonutChart",
				"ModuleId"	=> "122",
				"onTap"		=> array(
					"Title" => "应收",
					"Value" => "1",
					"Tag" => "multi",
					"Args" => ""
				),
				"RowSet"	=> array(
					"Separator" => "0.5",
					"Height" => "90"
				),
	            "ChartData"	=> $chart,
	            "ListData"	=> $list,
	            "Layout"	=> $layout,
			);
            
			//其他未付
			if(count($otherNoPayData) > 0) {
				$dataArray[] = $otherNoPayData;
			}
            
            //桌面结余
			if (in_array("120",$itemArray)) {
				
				$jyColor = ($jy > 0) ? $colorNameText : $colorNameTextNeg;
				$list = array(
	            	"Title"	=> array(
	            		"Text" => "桌面结余",
	            		"Color" => $colorNameTextTitle,
	            	), 
	            	"Col1"	=> array(
	            		"Text" => "¥".number_format($jy),
	            		"Color" => $jyColor,
	            	),
				);
				$layout = array(
	            	"Title" => array(
	            		"Frame" => "0, 0, 105, $rowHeight",
	            		"Align"	=> "M",
	            	),
	            	"Col1" => array(
	            		"Frame" => "210, 0, 85, $rowHeight",
	            		"Align"	=> "R",
	            	),
				);
				
				$dataArray[] = array(
					"View"		=> "List",
					"ModuleId"	=> "1221",
					"RowSet"	=> array(
						"Separator" => "0.5",
						"Height" => $rowHeight,
					),
		            "ChartData"	=> array(),
		            "ListData"	=> $list,
		            "Layout"	=> $layout,
				);
			}
        }
        
         if (in_array("125",$itemArray)){
			//损益表
			if ($jy == 0) {
				include "../../desk/subtask/subtask-122.php";		                   
			}  
			include "../../desk/subtask/subtask-125.php";
			
			$jyb = $jy - $lastMonthAmount;
/* 			echo ">> ".$jyb." = ".$jy." - ".$lastMonthAmount."<br>"; */

			$col1Tag = array(
				"Title" => $lastMonth,//date("M", strtotime ('-1 month', date('F'))),
				"Color" => $colorNameTextTag,
				"BGColor" => $colorNameBGPositive,
			);
		
			$list = array(
				"Title"	=> array(
            		"Text" => "损益表",
				),
            	/*
"Col1"	=> array(
            		"Text" => "¥".number_format($lastMonthAmount),
            		"Color" => ($lastMonthAmount > 0) ? $colorNameText : $colorNameTextNeg,
            		"Tag" => $col1Tag,
            	), 
*/
            	"Col1"	=> array(
            		"Text" 	=> "¥".number_format($jyb),
            		"Color" => ($jyb > 0) ? $colorNameText : $colorNameTextNeg,
            		"Tag"	=> $col1Tag,
            	), 
			);
			
			$layout = array(
            	"Title" => array(
            		"Frame" => "0, 0, 105, 85",
            		"Align"	=> "M",
            	),
            	"Col1" => array(
            		"Frame" => "125, 0, 170, $rowHeight",
            		"Align"	=> "R",
            	),
			);
				
			$dataArray[] = array(
				"View"		=> "List",
				"ModuleId"	=> "125",
				"onTap"		=> array(
					"Title" => "损益表",
					"Value" => "1",
					"Tag" => "multi",
					"Args"=>""
				),
				"RowSet"	=> array(
					"Separator" => "2",
					"Height" => $rowHeight,
				),
	            "ChartData"	=> array(),
	            "ListData"	=> $list,
	            "Layout"	=> $layout,
			);
		}
        
		$NextPage++; 
		if (count($dataArray)>0)  {
			$jsonArray = array(
				"Page" => "$NextPage",
				"GroupName" => "",
				"ColorList" => $colorList,
				"Data" => $dataArray,
			); 
			$dataArray = array();
			break;
		}
 
     case 2: 
            $color1Month = "#C8DFED";
	        $color1to3Month = "#E7F1F7";
	        $color3Month = "#FF0000";        
           if (in_array("107",$itemArray)) {
           
               	//在库
				//$NoCompanySTR=" P.CompanyId!='2166' ";
				/*
				$tStockResult = mysql_fetch_array(mysql_query("
						SELECT SUM(K.Qty-K.llQty) AS tStockQty,
						       SUM((K.Qty-K.llQty)*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount 
						FROM ck1_rksheet K 
						INNER JOIN ck1_rkmain M ON K.Mid=M.Id 
						INNER JOIN Stuffdata D ON D.StuffId=K.StuffId 
						INNER JOIN  trade_object P ON P.CompanyId=M.CompanyId 
						INNER JOIN  currencydata C ON C.Id = P.Currency
						WHERE K.llSign>0 ",$link_id));
						
						*/
				$tStockResult = mysql_fetch_array(mysql_query("
						SELECT SUM(K.tStockQty) as tStockQty,
	                       	   SUM(K.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS Amount
						FROM  ck9_stocksheet K
						LEFT JOIN  stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN  stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType 
						LEFT JOIN  bps B ON B.StuffId=D.StuffId 
						LEFT JOIN  trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN  currencydata C ON C.Id = P.Currency
						WHERE  K.tStockQty>0 AND TM.blSign=1 AND D.ComboxSign=0",$link_id));
				$SumQty=number_format($tStockResult["tStockQty"]); 
				$Sum_Qty=$tStockResult["tStockQty"];
				//$oStockQty=$tStockResult["oStockQty"]; 
				//$oAmount=$tStockResult["oAmount"]; 
				$SumTotal=number_format($tStockResult["Amount"]);
				$Sum_Total=$tStockResult["Amount"];
				
				//一個月以內、1~3個月、3個月以上
				/*
echo "SELECT SUM(A.tStockQty*D.Price*C.Rate) AS YearAmount, 
											SUM(CASE WHEN TIMESTAMPDIFF(MONTH,A.DTime,Now()) < 1 
											THEN A.tStockQty*D.Price*C.Rate ELSE 0 END) AS underOneMonth,
											SUM(CASE WHEN TIMESTAMPDIFF(MONTH,A.DTime,Now()) > 3 
											THEN A.tStockQty*D.Price*C.Rate ELSE 0 END) AS moreThreeMonth,
											SUM(CASE WHEN ((TIMESTAMPDIFF(MONTH,A.DTime,Now()) >= 1) 
												AND (TIMESTAMPDIFF(MONTH,A.DTime,Now()) <= 3))
											THEN A.tStockQty*D.Price*C.Rate ELSE 0 END) AS oneToThreeMonth
											FROM (
												SELECT S.StuffId,B.CompanyId,K.tStockQty,
												MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN $DataIn.bps B ON B.StuffId=K.StuffId   
												LEFT JOIN $DataIn.cg1_stocksheet S ON S.StuffId=K.StuffId
												LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
												LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
												LEFT JOIN $DataIn.yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber    
												WHERE  K.tStockQty>0  AND T.mainType<2  GROUP BY K.StuffId 
											)A 
											LEFT JOIN $DataIn.stuffdata D ON D.StuffId = A.StuffId
											LEFT JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId 
											LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency
											WHERE $NoCompanySTR"."<br>";
*/
				$monthQuery = mysql_query("SELECT SUM(A.tStockQty*A.Price*C.Rate) AS YearAmount, 
											SUM(CASE WHEN TIMESTAMPDIFF(MONTH,A.DTime,Now()) < 1 
											THEN A.tStockQty*A.Price*C.Rate ELSE 0 END) AS underOneMonth,
											SUM(CASE WHEN TIMESTAMPDIFF(MONTH,A.DTime,Now()) > 3 
											THEN A.tStockQty*A.Price*C.Rate ELSE 0 END) AS moreThreeMonth,
											SUM(CASE WHEN ((TIMESTAMPDIFF(MONTH,A.DTime,Now()) >= 1) 
												AND (TIMESTAMPDIFF(MONTH,A.DTime,Now()) <= 3))
											THEN A.tStockQty*A.Price*C.Rate ELSE 0 END) AS oneToThreeMonth
											FROM (
												SELECT S.StuffId,B.CompanyId,K.tStockQty,
												MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime,
												IF(D.CostPrice=0,D.Price,D.CostPrice) AS Price 
												FROM $DataIn.ck9_stocksheet K
												INNER JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												INNER JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												INNER JOIN stuffmaintype TM ON TM.Id=T.mainType 
												INNER JOIN $DataIn.bps B ON B.StuffId=K.StuffId   
												LEFT JOIN $DataIn.cg1_stocksheet S ON S.StuffId=K.StuffId
												LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
												LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
												LEFT JOIN $DataIn.yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber    
												WHERE  K.tStockQty>0  AND TM.blSign>0  AND D.ComboxSign=0  GROUP BY K.StuffId 
											)A 
											INNER JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId 
											INNER JOIN $DataPublic.currencydata C ON C.Id = P.Currency
											WHERE 1 ", $link_id);
											
				$monthResult= mysql_fetch_array($monthQuery);
				$key1Month = "underOneMonth";
	            $key1to3Month = "oneToThreeMonth";
	            $key3Month = "moreThreeMonth";
	           
	            
	            $value1Month = $monthResult["underOneMonth"];
	            $value1to3Month = $monthResult["oneToThreeMonth"];
	            $value3Month = $monthResult["moreThreeMonth"];
	            $valueTotal = $value1Month + $value1to3Month + $value3Month;
	            
	            $percent = sprintf("%.0f%%", ($value3Month / $valueTotal) * 100);
	            $chartTitle = array(
	            	"Title" => array(
	            		"Text" => "在库",
	            		"Color" => $colorNameTextTitle,
	            	),
	            	"SubTitle" => array(
	            		"Text" => $percent,
	            		"Color" => $color3Month,
	            	)
	            );
	            
	            $chart = array(
	            	"Title"	=> $chartTitle,
	            	"Data"	=> array(
	            		array(
	            			$keyChartId => $key3Month,
	            			$keyColor	=> $color3Month,
	            			$keyValue	=> $value3Month,
	            		),
	            		array(
	            			$keyChartId => $key1Month,
	            			$keyColor	=> $color1Month,
	            			$keyValue	=> $value1Month,
	            		),
	            		array(
	            			$keyChartId => $key1to3Month,
	            			$keyColor	=> $color1to3Month,
	            			$keyValue	=> $value1to3Month,
	            		),
	            	),
	            	"Desc"	=> array(),
	            );
				 			          
				//三个月以上未下采单
				$QtyResult= mysql_fetch_array(mysql_query("SELECT SUM(A.tStockQty) AS YearQty,SUM(A.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate) AS YearAmount
				FROM (
						SELECT S.StuffId,B.CompanyId,K.tStockQty,MAX(S.ywOrderDTime) AS DTime 
						FROM ck9_stocksheet K
						LEFT JOIN stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN stufftype T ON T.TypeId = D.TypeId 
                        LEFT JOIN stuffmaintype TM ON TM.Id = T.mainType
						LEFT JOIN bps B ON B.StuffId=K.StuffId   
						LEFT JOIN cg1_stocksheet S ON S.StuffId=K.StuffId
						WHERE  K.tStockQty>0  AND TM.blSign=1  AND D.ComboxSign=0 GROUP BY K.StuffId 
				)A 
				LEFT JOIN stuffdata D ON D.StuffId = A.StuffId
				LEFT JOIN trade_object P ON P.CompanyId=A.CompanyId 
				LEFT JOIN currencydata C ON C.Id = P.Currency
				WHERE TIMESTAMPDIFF(MONTH,A.DTime,Now())>=3",$link_id));//AND D.Estate>0  
			
				$SumQty_12=$QtyResult["YearQty"]>0?number_format($QtyResult["YearQty"]):""; 
				$SumTotal_12=$QtyResult["YearQty"]>0?number_format($QtyResult["YearAmount"]):"";
				
				$SepValue=$SumQty_12!=""?0:0.5;
/*
			 $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"107",
			             "onTap"=>array("Title"=>"在库","Value"=>"1","Tag"=>"ext","Args"=>""),
			             "RowSet"=>array("Separator"=>"$SepValue","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"在库","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#0000FF","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$SumTotal","Align"=>"R")
			          );
*/
			          
				//有订单需求的库存
				$lastYear = date("Y") - 1;
				$oStockResult = mysql_fetch_array(mysql_query("SELECT 
				SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty,X.OrderQty)) AS OrderQty,					SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate,X.OrderQty*IF(D.CostPrice=0,D.Price,D.CostPrice)*C.Rate)) AS OrderAmount  
						FROM (
						SELECT A.StuffId,SUM(IFNULL(A.OrderQty,0)) AS OrderQty FROM(
						            SELECT K.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty   
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN stuffmaintype TM ON TM.Id = T.mainType
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0  
						                        AND  G.ywOrderDTime>'$lastYear-01-01' 
												WHERE  K.tStockQty>0  AND TM.blSign=1  AND D.ComboxSign=0 Group by K.StuffId  
								   UNION ALL 
						                        SELECT K.StuffId,SUM(IFNULL(R.Qty*-1,0)) AS OrderQty  
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN stuffmaintype TM ON TM.Id = T.mainType
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.cgSign=0    
						                        AND  G.ywOrderDTime>'$lastYear-01-01'  
						                        LEFT JOIN $DataIn.ck5_llsheet R ON R.StockId=G.StockId 
												WHERE  K.tStockQty>0  AND TM.blSign=1  AND D.ComboxSign=0 Group by K.StuffId)A GROUP BY A.StuffId 
						)X 
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=X.StuffId  
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency 
						WHERE 1", $link_id));
            
				$oStockQty = $oStockResult["OrderQty"]; 
				$oAmount = $oStockResult["OrderAmount"]; 
				/*
				if ($oStockQty > 0) {
				     $oStockQty = number_format($oStockQty); 
				     $oAmount = number_format($oAmount); 
				
					$dataArray[]=array(
				            "View"=>"List",
				             "Id"=>"107",
				             "RowSet"=>array("Separator"=>"0","Height"=>"22"),
				             "Col_B"=>array("Title"=>"$oStockQty","Color"=>"#00A945","Align"=>"R","Margin"=>"0,-13,0,0"),
				             "Col_C"=>array("Title"=>"¥$oAmount","Color"=>"#000000","Margin"=>"0,-13,0,0","Align"=>"R")
				          );
				
				} 
				*/
				
				 if ($SumQty_12!="") {
				/*
					$dataArray[]=array(
				            "View"=>"List",
				             "Id"=>"107",
				             "RowSet"=>array("Separator"=>"0.5","Height"=>"22"),
				             "Col_B"=>array("Title"=>"$SumQty_12","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-13,0,0",
				                                       "DateIcon"=>array("Type"=>"4","Title"=>"3m")),
				             "Col_C"=>array("Title"=>"¥$SumTotal_12","Color"=>"#FF0000","Margin"=>"0,-13,0,0","Align"=>"R")
				          );
				*/
				}	   					
				
				$inWareHouseRowHeight = "112";
				//Col1, Col2 >> 三个月以上未下采单				
				$col1Tag = array(
					"Title" => "3mon",//date("M", strtotime ('-1 month', date('F'))),
					"Color" => $colorNameTextTag,
					"BGColor" => $colorNameBGPositive,
				);
				$list = array();
				if ($SumQty_12 != "") { 
					$list["Col5"] = array(
						"Text" => $SumQty_12,
						"Color" => $colorNameTextNeg,
						"Tag" => $col1Tag,
					);
					$list["Col6"] = array(
						"Text" => "¥".$SumTotal_12,
						"Color" => $colorNameTextNeg,
					);
				}
				else {
					$inWareHouseRowHeight = "70";
				}
				
				//Col3, Col4 >> 在库
/*
			 $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"107",
			             "onTap"=>array("Title"=>"在库","Value"=>"1","Tag"=>"ext","Args"=>""),
			             "RowSet"=>array("Separator"=>"$SepValue","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"在库","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#0000FF","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$SumTotal","Align"=>"R")
			          );
*/
				$keyInWarehouse1 = "Col3";
				$keyInWarehouse2 = "Col4";
				if ($SumQty_12 == "") {
					$keyInWarehouse1 = "Col1";
					$keyInWarehouse2 = "Col2";
				}
				
				$list["Col3"] = array(
					"Text" => $SumQty,
					"Color" => $colorNameTextNormal,
				);
				$list["Col4"] = array(
					"Text" => "¥".$SumTotal,
				);				
				
				//Col5, Col6 >> 有订单需求的库存
				/*
					$dataArray[]=array(
				            "View"=>"List",
				             "Id"=>"107",
				             "RowSet"=>array("Separator"=>"0","Height"=>"22"),
				             "Col_B"=>array("Title"=>"$oStockQty","Color"=>"#00A945","Align"=>"R","Margin"=>"0,-13,0,0"),
				             "Col_C"=>array("Title"=>"¥$oAmount","Color"=>"#000000","Margin"=>"0,-13,0,0","Align"=>"R")
				          );
				*/
				$keyOrder1 = "Col5";
				$keyOther2 = "Col6";
				if ($SumQty_12 == "") {
					$keyOrder1 = "Col3";
					$keyOther2 = "Col4";	
				}
				
				$oPercent = ($oAmount / $Sum_Total) * 100;
				$oStockQty = number_format($oStockQty); 
			    $oAmount = number_format($oAmount); 
				$list["Col1"] = array(
					"Text" => sprintf("%.0f%%", $oPercent),
					"Color" => $colorNameTextPerc,
				);
				$list["Col2"] = array(
					"Text" => "¥".$oAmount,
				);
				
	            $layout = array(
	            	"Col1" => array(
	            		"Frame" => "125, 25, 75, 15",
	            		"Align"	=> "R",
	            	),
	            	"Col2" => array(
	            		"Frame" => "210, 25, 85, 15",
	            		"Align"	=> "R",
	            	),
	            	"Col3" => array(
	            		"Frame" => "125, 50, 75, 15",
	            		"Align"	=> "R",
	            	),
	            	"Col4" => array(
	            		"Frame" => "210, 50, 85, 15",
	            		"Align"	=> "R",
	            	),
	            	"Col5" => array(
	            		"Frame" => "125, 75, 75, 15",
	            		"Align"	=> "R",
	            	),
	            	"Col6" => array(
	            		"Frame" => "210, 75, 85, 15",
	            		"Align"	=> "R",
	            	),
	            );
	            
	            $dataArray[] = array(
					"View"		=> "DonutChart",
					"ModuleId"	=> "107",
					"onTap"		=> array(
						"Title" => "在库",
						"Value" => "1",
						"Tag" => "ext",
						"Args" => ""
					),
					"RowSet"	=> array(
						"Separator" => "0.5",
						"Height" => $inWareHouseRowHeight,
					),
		            "ChartData"	=> $chart,
		            "ListData"	=> $list,
		            "Layout"	=> $layout,
				);
           }
          
           if (in_array("213",$itemArray)){
              
                     
                     $Result213 = mysql_fetch_array(mysql_query("SELECT SUM(B.Qty) AS blQty,SUM(B.Amount) AS Amount, SUM(CASE WHEN YEARWEEK(IFNULL(PI.Leadtime,PL.Leadtime),1) < YEARWEEK(CURDATE(),1)THEN B.Qty ELSE 0 END) AS WaitingTotal  
                     FROM (
                     	SELECT A.Id,A.POrderId,A.Qty,(A.Qty*A.Price*A.Rate) AS Amount 
                     	FROM (	
                     		SELECT S0.Id,S0.POrderId,S0.Qty,S0.Price,S0.Rate,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty 
                     		FROM (      
                     			SELECT S.Id,S.POrderId,S.Qty,S.Price,R.Rate,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
                     			FROM (
										SELECT S.Id,S.POrderId,S.Qty,S.Price,S.OrderNumber FROM $DataIn.yw1_ordersheet S
                                        WHERE 1 AND S.scFrom>0 AND S.Estate=1 
                                )S 
                                INNER JOIN $DataIn.yw1_ordermain M  ON M.OrderNumber=S.OrderNumber
                     			INNER JOIN $DataIn.trade_object O ON O.CompanyId=M.CompanyId
                     			INNER JOIN $DataIn.currencydata R ON R.Id=O.Currency
                     			INNER JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
                     			INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
                     			INNER JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
                     			LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
                                LEFT JOIN  $DataIn.stuffproperty T ON T.StuffId=G.StuffId AND T.Property='8'
                     			WHERE  ST.mainType<2 AND T.StuffId IS NULL                    		
                     			GROUP BY G.StockId 
                     		)S0 GROUP BY S0.POrderId 
                     	)A WHERE A.blQty=A.llQty  
                     		AND EXISTS (
                     		SELECT ST.mainType 
                     		FROM $DataIn.cg1_stocksheet G 
                     		INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
                     		INNER JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
                     		WHERE G.POrderId=A.POrderId AND ST.mainType=3
                     	)
                     )B 
                     LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=B.Id 
					 LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=B.POrderId",$link_id)); 
					 
                    $SumQty=number_format($Result213["blQty"]); 
			        $SumTotal=number_format($Result213["Amount"]);
			        
			        $sumQty = $Result213["blQty"];
			        $waitingQty = $Result213["WaitingTotal"];
			        //$waitingQty = $resultWaiting["WaitingTotal"];
			        $percent = $sumQty>0?($waitingQty / $sumQty) * 100:0;
			        
			        $keySum = "sum";
		            $keyWaiting = "waiting";
		            
		            $colorSum = "#C8DFED";
		            $colorWaiting = "#FF0000";
			        
			        $chartTitle = array(
		            	"Title" => array(
		            		"Text" => "待组装",
		            		"Color" => $colorNameTextTitle,
		            	),
		            	"SubTitle" => array(
		            		"Text" => sprintf("%.0f%%", $percent),
		            		"Color" => $color3Month,
		            	)
		            );
		            
		            $chart = array(
		            	"Title"	=> $chartTitle,
		            	"Data"	=> array(
		            		array(
		            			$keyChartId => $keyWaiting,
		            			$keyColor	=> $colorWaiting,
		            			$keyValue	=> $percent,
		            		),
		            		array(
		            			$keyChartId => $keySum,
		            			$keyColor	=> $colorSum,
		            			$keyValue	=> 100 - $percent,
		            		),
		            	),
		            	"Desc"	=> array(),
		            );
/*
                    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"213",
			             "onTap"=>array("Title"=>"待组装","Value"=>"1","Tag"=>"Production","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待组装","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#0000FF","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$SumTotal","Align"=>"R")
			          );
*/
				
					$list = array(
		            	"Col1"	=> array(
		            		"Text" => number_format($waitingQty),
		            		"Color" => (0 > 0) ? $colorNameText : $colorNameTextNeg,
		            	), 
		            	"Col2"	=> array(
		            		"Text" => $SumQty,
		            		"Color" => $colorNameTextNormal,
		            		"Tag" => array(
								"Title" => "today",//date("M", strtotime ('-1 month', date('F'))),
								"Color" => $colorNameTextTag,
								"BGColor" => $colorNameBGPositive,
		            		),
		            	), 
		            	"Col3"	=> array(
		            		"Text" => "¥$SumTotal",
		            	), 
					);

					$layout = array(
		            	"Col1" => array(
		            		"Frame" => "125, 25, 75, 15",
		            		"Align"	=> "R",
		            	),
		            	"Col2" => array(
		            		"Frame" => "125, 50, 75, 15",
		            		"Align"	=> "R",
		            	),
		            	"Col3" => array(
		            		"Frame" => "210, 50, 85, 15",
		            		"Align"	=> "R",
		            	),
		            );

			        $dataArray[] = array(
						"View"		=> "DonutChart",
						"ModuleId"	=> "213",
						"onTap"		=> array(
							"Title" => "待组装",
							"Value" => "1",
							"Tag" => "Production2",
							"Args" => ""
						),
						"RowSet"	=> array(
							"Separator" => "0.5",
							"Height" => "85",
						),
			            "ChartData"	=> $chart,
			            "ListData"	=> $list,
			            "Layout"	=> $layout,
					);
        }
       
        if (in_array("216",$itemArray)){        
             //待出金额
             
             $waitResult=mysql_fetch_array(mysql_query("SELECT  SUM(S.Qty) AS Qty,SUM( S.Price*S.Qty*D.Rate) AS Amount 
                     FROM $DataIn.yw1_ordermain M
                     LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
                     LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
                     LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
                     WHERE 1 and S.Estate>=2 ",$link_id));
                   
            $waitQtyValue=number_format(sprintf("%.0f",$waitResult["Qty"]));
            $waitAmountValue=number_format(sprintf("%.0f",$waitResult["Amount"]));
/*
             $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"216",
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "onTap"=>array("Title"=>"待出","Value"=>"1","Tag"=>"OrderExt","Args"=>""),//List0
			             "Col_A"=>array("Title"=>"待出","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$waitQtyValue","Color"=>"#0000FF","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$waitAmountValue","Align"=>"R")
			             );
*/
			             
			  //逾期待出    
			    $noshipResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount
			       FROM(
			              SELECT Max(T.Date) AS scDate,Y.Qty,Y.Price,Y.OrderNumber    
			              FROM $DataIn.yw1_ordersheet Y 
			    		  LEFT JOIN  $DataIn.sc1_cjtj  T ON  T.POrderId=Y.POrderId  
			    		  WHERE  Y.Estate>=2  GROUP BY Y.POrderId 
			    		)S 
						LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber 
						LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
						LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
						WHERE  TIMESTAMPDIFF( DAY, S.scDate , Now())>=5",$link_id));
						
				 $SumQty=number_format($noshipResult["Qty"]); 	
				 $SumTotal=number_format($noshipResult["Amount"]);
/*
	         $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"2160",
			             "RowSet"=>array("Separator"=>"2","Height"=>"22"),
			             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-13,0,0",
			                                         "DateIcon"=>array("Type"=>"4","Title"=>"5d")),
			             "Col_C"=>array("Title"=>"¥$SumTotal","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-13,0,0")
			          );
*/

        $thisDate=date("Y-m-d");
	    $overdate=date("Y-m-d",strtotime("$thisDate  -6 day"));
	    
		$waitResult=mysql_fetch_array(mysql_query("SELECT B.CompanyId,COUNT(*) AS Counts,SUM(B.rkQty-B.shipQty) AS tStockQty,
		               SUM(IF (B.overSign=1,B.rkQty-B.shipQty,0)) AS OverQty,SUM(OverSign) AS OverCounts,
		               SUM((B.rkQty-B.shipQty)*B.Price*D.Rate) AS Amount, 
		               SUM(IF(B.overSign=1,(B.rkQty-B.shipQty)*B.Price*D.Rate,0)) AS OverAmount   
				 FROM(
					SELECT A.CompanyId,A.POrderId,A.Qty,A.Price,A.rkQty,SUM(IFNULL(C.Qty,0)) AS shipQty,
					       IF(rkDate<'$overdate',1,0) AS OverSign  
					FROM (
					    SELECT M.CompanyId,S.POrderId,S.Qty,S.Price,SUM(R.Qty) AS rkQty,MAX(R.Date) AS rkDate   
					    FROM yw1_ordersheet S 
					    INNER JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber 
					    INNER JOIN yw1_orderrk R ON R.POrderId=S.POrderId 
					    WHERE S.Estate>0  GROUP BY S.POrderId 
					)A 
					LEFT JOIN ch1_shipsheet C ON C.POrderId=A.POrderId 
					GROUP BY A.POrderId
				) B 
				INNER JOIN  trade_object P ON P.CompanyId=B.CompanyId 
				INNER JOIN  currencydata D ON D.Id=P.Currency 
				LEFT  JOIN  staffmain M ON M.Number=Staff_Number 
				WHERE B.rkQty>B.shipQty ",$link_id));
				
				$waitQtyValue=number_format(sprintf("%.0f",$waitResult["tStockQty"]));
                $waitAmountValue=number_format(sprintf("%.0f",$waitResult["Amount"]));
                
                $SumQty=number_format($waitResult["OverQty"]); 	
				$SumTotal=number_format($waitResult["OverAmount"]);
            
				$col1Tag = array(
					"Title" => $thisMonth,
					"Color" => $colorNameTextTag,
					"BGColor" => $colorNameBGPositive,
				);
				
				$col3Tag = array(
					"Title" => "5Day",
					"Color" => $colorNameTextTag,
					"BGColor" => $colorNameBGPositive,
				);
			
				$list = array(
					"Title"	=> array(
	            		"Text" => "成品",
					),
	            	"Col1"	=> array(
	            		"Text" => $waitQtyValue,
	            		"Color" => $colorNameTextNormal,
/* 	            		"Tag" => $col1Tag, */
	            	), 
	            	"Col2"	=> array(
	            		"Text" => "¥".$waitAmountValue,
	            		"Color" => $colorNameText,
	            	), 
	            	"Col3"	=> array(
	            		"Text" => $SumQty,
	            		"Color" => $colorNameTextNeg,
	            		"Tag" => $col3Tag,
	            	), 
	            	"Col4"	=> array(
	            		"Text" => "¥".$SumTotal,
	            		"Color" => $colorNameTextNeg,
	            	), 
				);
				
				$layout = array(
	            	"Title" => array(
	            		"Frame" => "0, 0, 105, 85",
	            		"Align"	=> "M",
	            	),
	            	"Col1" => array(
	            		"Frame" => "125, 25, 75, 15",
	            		"Align"	=> "R",
	            	),
	            	"Col2" => array(
	            		"Frame" => "210, 25, 85, 15",
	            		"Align"	=> "R",
	            	),
	            	"Col3" => array(
	            		"Frame" => "125, 50, 75, 15",
	            		"Align"	=> "R",
	            	),
	            	"Col4" => array(
	            		"Frame" => "210, 50, 85, 15",
	            		"Align"	=> "R",
	            	),
				);
				
			    //"Id"=>"216",
				//"onTap"=>array("Title"=>"待出","Value"=>"1","Tag"=>"OrderExt","Args"=>""),//List0
				$dataArray[] = array(
					"View"		=> "List",
					"ModuleId"	=> "216",
					"onTap"		=> array(
						"Title" => "成品",
						"Value" => "1",
						"Tag" => "OrderExt",
						"Args"=>""
					),
					"RowSet"	=> array(
						"Separator" => "2",
						"Height" => 85,
					),
		            "ChartData"	=> array(),
		            "ListData"	=> $list,
		            "Layout"	=> $layout,
				);
        }
		
		$NextPage++; 
		if (count($dataArray)>0)  {
			$jsonArray = array(
				"Page" => $NextPage,
				"GroupName" => "",
				"Data" => $dataArray,
			); 
			$dataArray = array();
			break;
		}

     case 3: 
         $orderExtTag=versionToNumber($AppVersion)>=277?"OrderExt2":"OrderExt";//Created by 2014/08/29    
         if (in_array("123",$itemArray)){
         
	         //下單		
			//本月下单金额
			$month = date("Y-m");
			$InResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Price*S.Qty*D.Rate) AS Amount
														FROM $DataIn.yw1_ordermain M
														LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
														LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
														LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
														WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$month'", $link_id));
			$inQtyValue = number_format($InResult["Qty"]);
			$inAmountValue = number_format(sprintf("%.0f", $InResult["Amount"]));
/*
		   $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"210",
			             "DateIcon"=>array("Type"=>"2","Frame"=>"70,10,20,20"),
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "onTap"=>array("Title"=>"下单","Value"=>"1","Tag"=>"$orderExtTag","Args"=>""),
			             "Col_A"=>array("Title"=>"下单","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$inQtyValue","Color"=>"#0000FF","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$inAmountValue","Align"=>"R")
			          );        
*/
			//全年下单金额
			$year = date("Y");
			$yearResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Price*S.Qty*D.Rate) AS Amount
															FROM $DataIn.yw1_ordermain M
															LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
															LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
															LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
															WHERE DATE_FORMAT(M.OrderDate,'%Y')='$year'", $link_id));
			$yearQty = number_format($yearResult["Qty"]);
			$yearAmount = number_format(sprintf("%.0f", $yearResult["Amount"]));

/*
			 $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1232",
			              "DateIcon"=>array("Type"=>"1","Frame"=>"70,0,20,20"),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"25"),
			             "Col_B"=>array("Title"=>"$yearQty","Color"=>"#0000FF","Align"=>"R","Margin"=>"0,-10,0,0"),
			             "Col_C"=>array("Title"=>"¥$yearAmount","Align"=>"R","Margin"=>"0,-10,0,0")
			          );
*/
	        $col1Tag = array(
				"Title" => $thisMonth,
				"Color" => $colorNameTextTag,
				"BGColor" => $colorNameBGPositive,
			);
			
			$col3Tag = array(
				"Title" => $thisYear,
				"Color" => $colorNameTextTag,
				"BGColor" => $colorNameBGPositive,
			);
		
			$list = array(
				"Title"	=> array(
	        		"Text" => "下单",
				),
	        	"Col1"	=> array(
	        		"Text" => $inQtyValue,
	        		"Color" => $colorNameTextNormal,
	        		"Tag" => $col1Tag,
	        	), 
	        	"Col2"	=> array(
	        		"Text" => "¥".$inAmountValue,
	        		"Color" => $colorNameText,
	        	), 
	        	"Col3"	=> array(
	        		"Text" => $yearQty,
	        		"Color" => $colorNameTextNormal,
	        		"Tag" => $col3Tag,
	        	), 
	        	"Col4"	=> array(
	        		"Text" => "¥".$yearAmount,
	        		"Color" => $colorNameText,
	        	), 
			);
			
			$layout = array(
	        	"Title" => array(
	        		"Frame" => "0, 0, 105, 85",
	        		"Align"	=> "M",
	        	),
	        	"Col1" => array(
	        		"Frame" => "125, 25, 75, 15",
	        		"Align"	=> "R",
	        	),
	        	"Col2" => array(
	        		"Frame" => "210, 25, 85, 15",
	        		"Align"	=> "R",
	        	),
	        	"Col3" => array(
	        		"Frame" => "125, 50, 75, 15",
	        		"Align"	=> "R",
	        	),
	        	"Col4" => array(
	        		"Frame" => "210, 50, 85, 15",
	        		"Align"	=> "R",
	        	),
			);
			
		    //"Id"=>"210",
			//"onTap"=>array("Title"=>"下单","Value"=>"1","Tag"=>"$orderExtTag","Args"=>""),
			$dataArray[] = array(
				"View"		=> "List",
				"ModuleId"	=> "210",
				"onTap"		=> array(
					"Title" => "下单",
					"Value" => "1",
					"Tag" => "$orderExtTag",
					"Args"=>""
				),
				"RowSet"	=> array(
					"Separator" => "2",
					"Height" => 85,
				),
	            "ChartData"	=> array(),
	            "ListData"	=> $list,
	            "Layout"	=> $layout,
			);
  
            //本月出货总额
            $month = date("Y-m");			
			$ShipResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty  
								                            FROM $DataIn.ch1_shipmain M
								                            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
								                            WHERE  M.Estate='0' AND DATE_FORMAT(M.Date,'%Y-%m')='$month' 
								                            AND (S.Type=1 OR S.Type=3)", $link_id));
	        $shipQtyValue = number_format(sprintf("%.0f", $ShipResult["Qty"]));		
                            			
            $ShipResult = mysql_fetch_array(mysql_query("SELECT SUM( S.Price*S.Qty*M.Sign*D.Rate) AS Amount 
								                            FROM $DataIn.ch1_shipmain M
								                            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
								                            LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
								                            LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
								                            WHERE  M.Estate='0' AND DATE_FORMAT(M.Date,'%Y-%m')='$month' ", $link_id));
	        $shipAmountValue = number_format(sprintf("%.0f", $ShipResult["Amount"]));  
/*
            $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"2105",
			             "DateIcon"=>array("Type"=>"2","Frame"=>"70,10,20,20"),
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "onTap"=>array("Title"=>"出货","Value"=>"1","Tag"=>"$orderExtTag","Args"=>""),
			             "Col_A"=>array("Title"=>"出货","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$shipQtyValue","Color"=>"#0000FF","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$shipAmountValue","Align"=>"R")
			             );
*/
             
             //本年出货总额
             $year = date("Y");			
             $ShipResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty  
								                            FROM $DataIn.ch1_shipmain M
								                            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
								                            WHERE  M.Estate='0' AND YEAR(M.Date)='$year'
								                            AND (S.Type=1 OR S.Type=3)", $link_id));
			 $yearQty = number_format(sprintf("%.0f",$ShipResult["Qty"]));		
			
			 $ShipResult = mysql_fetch_array(mysql_query("SELECT SUM( S.Price*S.Qty*M.Sign*D.Rate) AS Amount 
								                            FROM $DataIn.ch1_shipmain M
								                            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
								                            LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
								                            LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
								                            WHERE  M.Estate='0' AND YEAR(M.Date)='$year' ",$link_id));
			 $yearAmount = number_format(sprintf("%.0f",$ShipResult["Amount"]));
                            
/*
              $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1232",
			              "DateIcon"=>array("Type"=>"1","Frame"=>"70,0,20,20"),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"25"),
			             "Col_B"=>array("Title"=>"$yearQty","Color"=>"#0000FF","Align"=>"R","Margin"=>"0,-10,0,0"),
			             "Col_C"=>array("Title"=>"¥$yearAmount","Align"=>"R","Margin"=>"0,-10,0,0")
			          );
*/
			$titleKey = "Title";
			$ValueKey = "Value";
	
			$perMonthOrderSql = "SELECT SUM(S.Price*S.Qty*D.Rate)/10000 AS Amount, DATE_FORMAT(M.OrderDate,'%Y-%m') AS Month
									FROM $DataIn.yw1_ordermain M
									LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
									LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
									LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
									GROUP BY DATE_FORMAT(M.OrderDate,'%Y-%m')";
			$perMonthOrderQuery = mysql_query($perMonthOrderSql, $link_id);
			
			$perMonthOrderData = array();
			if($orderRow = mysql_fetch_array($perMonthOrderQuery)) {
				do {
					$perMonthOrderData[] = array(
						$titleKey => $orderRow["Month"],
						$ValueKey => $orderRow["Amount"],
					);
				} while($orderRow = mysql_fetch_array($perMonthOrderQuery));
			}
			
			$perMonthShipSql = "SELECT SUM( S.Price*S.Qty*M.Sign*D.Rate)/10000 AS Amount, DATE_FORMAT(M.Date,'%Y-%m') AS Month
									FROM $DataIn.ch1_shipmain M
									LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
									LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
									LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
									WHERE  M.Estate='0'
									GROUP BY DATE_FORMAT(M.Date,'%Y-%m')";
			$perMonthShipQuery = mysql_query($perMonthShipSql, $link_id);
			
			$perMonthShipData = array();
			if($shipRow = mysql_fetch_array($perMonthShipQuery)) {
				do {
					$perMonthShipData[] = array(
						$titleKey => $shipRow["Month"],
						$ValueKey => $shipRow["Amount"],
					);
				} while($shipRow = mysql_fetch_array($perMonthShipQuery));
			}
			
			//讓2個資料筆數相同
			$orderMonthCount = count($perMonthOrderData);
			$shipMonthCount = count($perMonthShipData);
			if ($orderMonthCount > $shipMonthCount) {
				$perMonthOrderData = array_slice($perMonthOrderData, $orderMonthCount - $shipMonthCount);	
			}
			else if ($shipMonthCount > $orderMonthCount) {
				$perMonthShipData = array_slice($perMonthShipData, $shipMonthCount - $orderMonthCount);				
			}			

			$lastOrderData = end($perMonthOrderData);
			$lastShipData = end($perMonthShipData);	
			
			/*
			while ($lastOrderData[$titleKey] != $lastShipData[$titleKey]) {

				$lastOrderMonth = $lastOrderData[$titleKey];
				$lastShipMonth = $lastShipData[$titleKey];
				
				$interval = date_diff($lastOrderMonth, $lastShipMonth);
					//			echo $interval."<br>"; 
		  	   if ($interval->format('%a') > 0) {
					//last order month > last ship month
					$newMonthData = array(
						$titleKey	=> $lastOrderMonth,
						$ValueKey	=> "0",
					);
					array_push($perMonthShipData, "apple", "raspberry");					
				}
				else {
					//last ship month > last order month
					
				}
			}
			*/
		
			$keyOrder = "order";
            $keyShip = "ship";
            
            $colorOrder = "#DCEBF3";
            $colorShip = "#6CA9CE";
            
            $chartDesc = array(
            	"Data" => array(
            		$keyOrder => array(
            			"Text"	=> "下单: @".$ValueKey,
            			"Color"	=> $colorOrder,
            			"Unit" => "万",
            		),
            		$keyShip => array(
            			"Text"	=> "出货: @".$ValueKey,
            			"Color"	=> $colorShip,
            			"Unit" => "万",
            		),
            	),
            );
            
            $chart = array(
            	"Title"	=> array(),
            	"Data"	=> array(
            		array(
            			$keyChartId => $keyShip,
            			$keyColor	=> $colorShip,
            			$keyValue	=> $perMonthShipData,
            		),
            		array(
            			$keyChartId => $keyOrder,
            			$keyColor	=> $colorOrder,
            			$keyValue	=> $perMonthOrderData,
            		),
            	),
            	"Desc"	=> $chartDesc,
            );

	        $col1Tag = array(
				"Title" => $thisMonth,
				"Color" => $colorNameTextTag,
				"BGColor" => $colorNameBGPositive,
			);
			
			$col3Tag = array(
				"Title" => $thisYear,
				"Color" => $colorNameTextTag,
				"BGColor" => $colorNameBGPositive,
			);
		
			$list = array(
				"Title"	=> array(
	        		"Text" => "出货",
				),
	        	"Col1"	=> array(
	        		"Text" => $shipQtyValue,
	        		"Color" => $colorNameTextNormal,
	        		"Tag" => $col1Tag,
	        	), 
	        	"Col2"	=> array(
	        		"Text" => "¥".$shipAmountValue,
	        		"Color" => $colorNameText,
	        	), 
	        	"Col3"	=> array(
	        		"Text" => $yearQty,
	        		"Color" => $colorNameTextNormal,
	        		"Tag" => $col3Tag,
	        	), 
	        	"Col4"	=> array(
	        		"Text" => "¥".$yearAmount,
	        		"Color" => $colorNameText,
	        	), 
			);
			
			$layout = array(
	        	"Title" => array(
	        		"Frame" => "0, 0, 105, 85",
	        		"Align"	=> "M",
	        	),
	        	"Col1" => array(
	        		"Frame" => "125, 25, 75, 15",
	        		"Align"	=> "R",
	        	),
	        	"Col2" => array(
	        		"Frame" => "210, 25, 85, 15",
	        		"Align"	=> "R",
	        	),
	        	"Col3" => array(
	        		"Frame" => "125, 50, 75, 15",
	        		"Align"	=> "R",
	        	),
	        	"Col4" => array(
	        		"Frame" => "210, 50, 85, 15",
	        		"Align"	=> "R",
	        	),
			);
			
		    //"Id"=>"2105",
			//"onTap"=>array("Title"=>"出货","Value"=>"1","Tag"=>"$orderExtTag","Args"=>""),
			$dataArray[] = array(
				"View"		=> "LineChart",
				"ModuleId"	=> "2105",
				"onTap"		=> array(
					"Title" => "出货",
					"Value" => "1",
					"Tag" => "$orderExtTag",
					"Args"=> "",
				),
				"RowSet"	=> array(
					"Separator" => "2",
					"Height" => 285,
				),
	            "ChartData"	=> $chart,
	            "ListData"	=> $list,
	            "Layout"	=> $layout,
			);
         
            //未出货订单总额
            include "../../desk/subtask/subtask-123.php";
            $SumTotalValue = number_format($noshipAmount);
            $GrossProfit = number_format($GrossProfit); 
/*
            $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"110",
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "onTap"=>array("Title"=>"未出","Value"=>"1","Tag"=>"OrderOut2","Args"=>""),
			             "Col_A"=>array("Title"=>"未出","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$noshipQty1","Color"=>"#0000FF","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$SumTotalValue","Align"=>"R","AboveTitle"=>"$GrossProfitPcnt%(¥$GrossProfit)","AboveColor"=>"#00A945")
			          );
*/

		     //逾期未出     
		    $noshipResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Qty*S.Price*D.Rate) AS Amount,SUM(IF(S.Estate>1,S.Qty,0) ) AS WaitQty 
					FROM $DataIn.yw1_ordersheet S 
					LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
					LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber 
					LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
					LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
					WHERE 1 AND S.Estate>'0'  AND YEARWEEK(PI.Leadtime,1)<YEARWEEK(CURDATE(),1)",$link_id));
					
			 $SumQty=number_format($noshipResult["Qty"]); 	
			 $WaitQty=number_format($noshipResult["WaitQty"]); 		
			 $SumTotal=number_format($noshipResult["Amount"]);
/*
	        $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"110",
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"20"),
			             "Col_A"=>array("Title"=>"$WaitQty","Align"=>"L","Color"=>"#00A945","Margin"=>"40,-15,0,0"),
			             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-15,0,0"),
			             "Col_C"=>array("Title"=>"¥$SumTotal","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-15,0,0")
			          );
*/
	        $keySum = "sum";
            $keyNoShip = "noship";
            
            $colorSum = "#C8DFED";
            $colorNoShip = "#82E0AA";
	        
	        $chartTitle = array(
            	"Title" => array(
            		"Text" => "未出",
            		"Color" => $colorNameTextTitle,
            	),
            	"SubTitle" => array(
            		"Text" => sprintf("%.0f%%", $GrossProfitPcnt),
            		"Color" => $colorNameTextPerc,
            	)
            );
            
            $chart = array(
            	"Title"	=> $chartTitle,
            	"Data"	=> array(
            		array(
            			$keyChartId => $keyNoShip,
            			$keyColor	=> $colorNoShip,
            			$keyValue	=> $GrossProfitPcnt,
            		),
            		array(
            			$keyChartId => $keySum,
            			$keyColor	=> $colorSum,
            			$keyValue	=> 100 - $GrossProfitPcnt,
            		),
            	),
            	"Desc"	=> array(),
            );

			$list = array(
	        	"Col1"	=> array(
	        		"Text" => "¥".$GrossProfit,
	        		"Color" => $colorNameTextPos,
	        	), 
	        	"Col2"	=> array(
	        		"Text" => $noshipQty1,
	        		"Color" => $colorNameTextNormal,
	        	), 
	        	"Col3"	=> array(
	        		"Text" => "¥".$SumTotalValue,
	        		"Color" => $colorNameText,
	        	), 
	        	"Col4"	=> array(
	        		"Text" => $SumQty,
	        		"Color" => $colorNameTextNeg,
	        	), 
	        	"Col5"	=> array(
	        		"Text" => "¥".$SumTotal,
	        		"Color" => $colorNameTextNeg,
	        	), 
			);

			$layout = array(
				"Col1" => array(
					"Frame" => "210, 25, 85, 15",
					"Align"	=> "R",
				),
				"Col2" => array(
					"Frame" => "125, 50, 75, 15",
					"Align"	=> "R",
				),
				"Col3" => array(
					"Frame" => "210, 50, 85, 15",
					"Align"	=> "R",
				),
				"Col4" => array(
					"Frame" => "125, 75, 75, 15",
					"Align"	=> "R",
				),
				"Col5" => array(
					"Frame" => "210, 75, 85, 15",
					"Align"	=> "R",
				),
			);
			
			//"Id"=>"110",
			//"onTap"=>array("Title"=>"未出","Value"=>"1","Tag"=>"OrderOut2","Args"=>""),
			$dataArray[] = array(
				"View"		=> "DonutChart",
				"ModuleId"	=> "110",
				"onTap"		=> array(
					"Title" => "未出",
					"Value" => "1",
					"Tag" 	=> "OrderOut2",
					"Args" 	=> ""
				),
				"RowSet"	=> array(
					"Separator" => "0.5",
					"Height" => 112,
				),
				"ChartData"	=> $chart,
				"ListData"	=> $list,
				"Layout"	=> $layout,
			);
		
			$NextPage++; 
			if (count($dataArray) > 0)  {
				$jsonArray = array(
					"Page" => $NextPage,
					"GroupName" => "",
					"Data" => $dataArray,
				); 
				$dataArray = array();
			}
				break;
        }
	
		case 4:

	        if (in_array("198",$itemArray)) {
	        
				//本月报关总额
				include "../../desk/subtask/subtask-198.php";
				$SumTotalValue = number_format($BgAmount);
				$BgPre = $BgPre == "" ? 0 : $BgPre;
				
				/*
					$dataArray[]=array(
					"View"=>"List",
					"Id"=>"198",
					"DateIcon"=>array("Type"=>"2","Frame"=>"165,10,20,20"),
					"RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
					"onTap"=>array("Title"=>"报关","Value"=>"1","Tag"=>"OrderExt","Args"=>""),//"Tag"=>"shiped"
					"Col_A"=>array("Title"=>"报关","Align"=>"L"),
					"Col_C"=>array("Title"=>"¥$SumTotalValue","Align"=>"R","AboveTitle"=>"($BgPre%)")
					);
				*/
				
				//全年报关
				$year = date("Y");	
				$totalMyResult = mysql_fetch_array(mysql_query("SELECT  SUM(S.Qty*S.Price*D.Rate) AS Amount 
																	FROM $DataIn.ch1_shipmain M 
																	LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
																	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
																	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
																	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
																	WHERE 1 AND M.Estate='0'  
																	AND (S.Type=1 OR S.Type=3) 
																	AND YEAR(M.Date)='$year'   
																	AND P.ProductId>0 ", $link_id));
				$yearTotal = $totalMyResult["Amount"];
				
				$yearTotalSql = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Price*D.Rate) AS Amount 
																	FROM $DataIn.ch1_shipmain M 
																	LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
																	LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId  
																	LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
																	LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
																	LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
																	WHERE  M.Estate='0'
																	 AND (S.Type=1 OR S.Type=3) 
																	 AND P.ProductId>0 
																	 AND T.Type=1 
																	 AND YEAR(M.Date)='$year' ", $link_id));
				$yearAmount = number_format($yearTotalSql["Amount"]);
				$yearBgPre = $yearTotal == 0 ? 0 : sprintf("%.0f", ($yearTotalSql["Amount"] * 100 / $yearTotal));
				
				/*
					$dataArray[]=array(
					"View"=>"List",
					"Id"=>"198",
					"DateIcon"=>array("Type"=>"1","Frame"=>"165,10,20,20"),
					"RowSet"=>array("Separator"=>"0.5","Height"=>"35"),
					"Col_C"=>array("Title"=>"¥$yearAmount","Align"=>"R","AboveTitle"=>"($yearBgPre%)")
					);
				*/
				
				$keySum = "sum";
	            $keyDeclare = "declare";
	            
	            $colorSum = "#C8DFED";
	            $colorDecalre = "#82E0AA";
		        
		        $chartTitle = array(
	            	"Title" => array(
	            		"Text" => "报关",
	            		"Color" => $colorNameTextTitle,
	            	),
	            	"SubTitle" => array(
	            		"Text" => sprintf("%.0f%%", $yearBgPre),
	            		"Color" => $colorNameTextPerc,
	            	)
	            );
	            
	            $chart = array(
	            	"Title"	=> $chartTitle,
	            	"Data"	=> array(
	            		array(
	            			$keyChartId => $keyDeclare,
	            			$keyColor	=> $colorDecalre,
	            			$keyValue	=> $BgPre,
	            		),
	            		array(
	            			$keyChartId => $keySum,
	            			$keyColor	=> $colorSum,
	            			$keyValue	=> 100 - $BgPre,
	            		),
	            	),	            	
	            	"Desc"	=> array(),
	            );

		        $col1Tag = array(
					"Title" => $thisMonth,
					"Color" => $colorNameTextTag,
					"BGColor" => $colorNameBGPositive,
				);
				
				$col2Tag = array(
					"Title" => $thisYear,
					"Color" => $colorNameTextTag,
					"BGColor" => $colorNameBGPositive,
				);			
	
				$list = array(
		        	"Col1"	=> array(
		        		"Text" => "¥".$SumTotalValue,
		        		"Color" => $colorNameText,
		        		"Tag" => $col1Tag,
		        	), 
		        	"Col2"	=> array(
		        		"Text" => "¥".$yearAmount,
		        		"Color" => $colorNameText,
		        		"Tag" => $col2Tag,
		        	),
				);
	
				$layout = array(
					"Col1" => array(
						"Frame" => "125, 25, 170, 15",
						"Align"	=> "R",
					),
					"Col2" => array(
						"Frame" => "125, 50, 170, 15",
						"Align"	=> "R",
					),
				);
				
				//"Id"=>"198",
				//"onTap"=>array("Title"=>"报关","Value"=>"1","Tag"=>"OrderExt","Args"=>"")
				$dataArray[] = array(
					"View"		=> "DonutChart",
					"ModuleId"	=> "198",
					"onTap"		=> array(
						"Title" => "报关",
						"Value" => "1",
						"Tag" 	=> "OrderExt",
						"Args" 	=> ""
					),
					"RowSet"	=> array(
						"Separator" => "0.5",
						"Height" => 85,
					),
					"ChartData"	=> $chart,
					"ListData"	=> $list,
					"Layout"	=> $layout,
				);
			}
			
			//退税
			if (in_array("125",$itemArray)){
				$gysskResult = mysql_fetch_array(mysql_query("SELECT SUM(IFNULL(A.Amount,0)) AS Amount FROM(
																SELECT SUM(S.Amount*D.Rate) AS Amount
																FROM $DataIn.cw2_gyssksheet S 
																LEFT JOIN $DataPublic.currencydata D ON D.Id=S.Currency
																WHERE (S.Estate=3 OR S.Estate=0) AND YEAR(S.Date)='$year'
																UNION ALL 
																SELECT SUM(S.Amount*D.Rate) AS Amount
																FROM $DataSub.cw2_gyssksheet S 
																LEFT JOIN $DataPublic.currencydata D ON D.Id=S.Currency
																WHERE (S.Estate=3 OR S.Estate=0) AND YEAR(S.Date)='$year'
																UNION ALL
																SELECT  SUM(F.declarationCharge) AS Amount 
																FROM $DataIn.ch4_freight_declaration F
																WHERE (F.Estate=3 OR F.Estate=0) AND YEAR(F.Date)='$year')A ", $link_id));
				$skAmount = number_format($gysskResult["Amount"]);
				
				$taxResult = mysql_fetch_array(mysql_query("SELECT SUM(M.Taxamount) AS Amount 
																FROM $DataIn.cw14_mdtaxmain M   
																WHERE YEAR(M.taxdate)='$year'", $link_id));
				$taxAmount = number_format($taxResult["Amount"]);					
				$mdAmount = number_format($taxResult["Amount"] - $gysskResult["Amount"]);                  
/*
			$dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1250",
			             "DateIcon"=>array("Type"=>"1","Frame"=>"70,10,20,20"),
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"退税","Align"=>"L"),
			             "Col_B"=>array("Title"=>"¥$taxAmount","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$skAmount","Align"=>"R")
			             );	
*/
			 
/*
              $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1250",
			             "RowSet"=>array("Separator"=>"2","Height"=>"25"),
			             "Col_C"=>array("Title"=>"¥$mdAmount","Color"=>"#00A945","Align"=>"R","Margin"=>"0,-10,0,0")
			             );
*/
				$keySum = "sum";
	            $keyTax = "tax";
	            
	            $colorSum = "#C8DFED";
	            $colorTax = "#82E0AA";
	            
	            $taxValue = $taxResult["Amount"] - $gysskResult["Amount"];
	            $percentValue = $gysskResult["Amount"]>0?abs($taxValue) / $gysskResult["Amount"] * 100:0;
	            $chartData = array(
	            	$keySum => 100 - $percentValue,
	            	$keyTax => $percentValue,
	            );
	            
	            $chartColor = array(
	            	$keySum => $colorSum,
	            	$keyTax => $colorTax,
	            );
		        
		        $chartTitle = array(
	            	"Title" => array(
	            		"Text" => "退税",
	            		"Color" => $colorNameTextTitle,
	            	)
	            );
	            
	            $chart = array(
	            	"Title"	=> $chartTitle,
	            	"Data"	=> array(
	            		array(
	            			$keyChartId => $keyTax,
	            			$keyColor	=> $colorTax,
	            			$keyValue	=> $percentValue,
	            		),
	            		array(
	            			$keyChartId => $keySum,
	            			$keyColor	=> $colorSum,
	            			$keyValue	=> 100 - $percentValue,
	            		),
	            	),
	            	"Desc"	=> array(),
	            );
				
				$col2Tag = array(
					"Title" => $thisYear,
					"Color" => $colorNameTextTag,
					"BGColor" => $colorNameBGPositive,
				);			
	
				$list = array(
		        	"Col1"	=> array(
		        		"Text" => "¥".$mdAmount,
		        		"Color" => $colorNameTextPos,
		        	), 
		        	"Col2"	=> array(
		        		"Text" => "¥".$skAmount,
		        		"Color" => $colorNameText,
		        		"Tag" => $col2Tag,
		        	),
				);
	
				$layout = array(
					"Col1" => array(
						"Frame" => "125, 25, 170, 15",
						"Align"	=> "R",
					),
					"Col2" => array(
						"Frame" => "125, 50, 170, 15",
						"Align"	=> "R",
					),
				);
				
				//"Id"=>"1250",
				$dataArray[] = array(
					"View"		=> "DonutChart",
					"ModuleId"	=> "1250",
					"RowSet"	=> array(
						"Separator" => "0.5",
						"Height" => 85,
					),
					"ChartData"	=> $chart,
					"ListData"	=> $list,
					"Layout"	=> $layout,
				);
		 }		 
		
		$NextPage++; 
		if (count($dataArray) > 0)  {
			$jsonArray = array(
				"Page" => $NextPage,
				"GroupName" => "",
				"Data" => $dataArray,
			); 
			$dataArray = array();
			 break;
		}
		
	case 5:	     
		//采购
		if (false) {//(in_array("129", $itemArray)) {
			$month = date("Y-m");
			$Result129 = mysql_fetch_array(mysql_query("SELECT SUM(S.Amount*D.Rate) AS Amount,SUM(IF(S.Estate=3,S.Amount*D.Rate,0)) AS NoPay   
															FROM $DataIn.cw1_fkoutsheet S
															LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
															LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency
															WHERE (S.Estate=3 OR S.Estate=0) AND S.Month='$month' ", $link_id));
			$cgAmount = number_format($Result129["Amount"]);
			$NoPayAmount = number_format($Result129["NoPay"]);     
/*
			$dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"129",
			             "DateIcon"=>array("Type"=>"2","Frame"=>"70,10,20,20"),
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"采购","Align"=>"L"),
			             "Col_B"=>array("Title"=>"¥$NoPayAmount","Color"=>"#FF0000","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$cgAmount","Align"=>"R")
			             );
*/
			             
			//年度统计
			$year = date("Y");
			$yearResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Amount*D.Rate) AS Amount,SUM(IF(S.Estate=3,S.Amount*D.Rate,0)) AS NoPay   
															FROM $DataIn.cw1_fkoutsheet S
															LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
															LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency
															WHERE (S.Estate=3 OR S.Estate=0) AND LEFT(S.Month,4)='$year' ", $link_id));
			$yearAmount = number_format($yearResult["Amount"]);
			$yearNoPay = number_format($yearResult["NoPay"]);     
/*
			$dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"129",
			             "DateIcon"=>array("Type"=>"1","Frame"=>"70,0,20,20"),
			             "RowSet"=>array("Separator"=>"2","Height"=>"25"),
			             "Col_B"=>array("Title"=>"¥$yearNoPay","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-10,0,0"),
			             "Col_C"=>array("Title"=>"¥$yearAmount","Align"=>"R","Margin"=>"0,-10,0,0")
			             );
*/
		
		}
      
        if (in_array("220",$itemArray)) {
			//备品转入
			$month = date("Y-m");
			$Result2200 = mysql_fetch_array(mysql_query("SELECT  SUM(B.Qty) as Qty,SUM(D.Price*B.Qty*C.Rate) AS Amount
															FROM $DataIn.ck7_bprk B 
															LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
															LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
															LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
															LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
															WHERE  DATE_FORMAT(B.Date,'%Y-%m')='$month' ", $link_id));
			$monthSumQty = number_format($Result2200["Qty"]); 	
			$monthSumAmount = number_format($Result2200["Amount"]);
/*
           $dataArray[]=array(
	            "View"=>"List",
	             "Id"=>"220",
	             "onTap"=>array("Title"=>"备品","Value"=>"1","Tag"=>"StuffExt","Args"=>""),
	             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
	             "DateIcon"=>array("Type"=>"2","Frame"=>"70,10,20,20"),
	             "Col_A"=>array("Title"=>"备品","Align"=>"L"),
	             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#0000FF","Align"=>"R"),
	             "Col_C"=>array("Title"=>"¥$SumTotal","Align"=>"R")
	          );
*/

			include "../../desk/subtask/subtask-220.php";
			$SumQty = number_format($Result220["Qty"]); 	
			$SumTotal = number_format($Result220["Amount"]);
/*
           $dataArray[]=array(
	            "View"=>"List",
	             "Id"=>"220",
	             "RowSet"=>array("Separator"=>"0.5","Height"=>"25"),
	              "DateIcon"=>array("Type"=>"1","Frame"=>"70,0,20,20"),
	             "Col_A"=>array("Title"=>" ","Align"=>"L"),
	             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#0000FF","Align"=>"R","Margin"=>"0,-10,0,0"),
	             "Col_C"=>array("Title"=>"¥$SumTotal","Align"=>"R","Margin"=>"0,-10,0,0")
	          );
*/
			$col1Tag = array(
				"Title" => $thisMonth,
				"Color" => $colorNameTextTag,
				"BGColor" => $colorNameBGPositive,
			);
			
			$col3Tag = array(
				"Title" => $thisYear,
				"Color" => $colorNameTextTag,
				"BGColor" => $colorNameBGPositive,
			);
		
			$list = array(
				"Title"	=> array(
	        		"Text" => "备品",
				),
	        	"Col1"	=> array(
	        		"Text" => $monthSumQty,
	        		"Color" => $colorNameTextNormal,
	        		"Tag" => $col1Tag,
	        	), 
	        	"Col2"	=> array(
	        		"Text" => "¥".$monthSumAmount,
	        		"Color" => $colorNameText,
	        	), 
	        	"Col3"	=> array(
	        		"Text" => $SumQty,
	        		"Color" => $colorNameTextNormal,
	        		"Tag" => $col3Tag,
	        	), 
	        	"Col4"	=> array(
	        		"Text" => "¥".$SumTotal,
	        		"Color" => $colorNameText,
	        	), 
			);
			
			$layout = array(
	        	"Title" => array(
	        		"Frame" => "0, 0, 105, 85",
	        		"Align"	=> "M",
	        	),
	        	"Col1" => array(
	        		"Frame" => "125, 25, 75, 15",
	        		"Align"	=> "R",
	        	),
	        	"Col2" => array(
	        		"Frame" => "210, 25, 85, 15",
	        		"Align"	=> "R",
	        	),
	        	"Col3" => array(
	        		"Frame" => "125, 50, 75, 15",
	        		"Align"	=> "R",
	        	),
	        	"Col4" => array(
	        		"Frame" => "210, 50, 85, 15",
	        		"Align"	=> "R",
	        	),
			);
			
		    //"Id"=>"220",
			//"onTap"=>array("Title"=>"备品","Value"=>"1","Tag"=>"StuffExt","Args"=>""),
			$dataArray[] = array(
				"View"		=> "List",
				"ModuleId"	=> "220",
				"onTap"		=> array(
					"Title" => "备品",
					"Value" => "1",
					"Tag" => "StuffExt",
					"Args"=>""
				),
				"RowSet"	=> array(
					"Separator" => "2",
					"Height" => 85,
				),
	            "ChartData"	=> array(),
	            "ListData"	=> $list,
	            "Layout"	=> $layout,
			);
		}	

		if (in_array("110",$itemArray)) {
			//配件报废
			$month = date("Y-m");
			$Result1100 = mysql_fetch_array(mysql_query("SELECT  SUM(F.Qty) as Qty,SUM(D.Price*F.Qty*C.Rate) AS Amount
															FROM $DataIn.ck8_bfsheet F
															LEFT JOIN $DataIn.stuffdata D ON F.StuffId=D.StuffId 
															LEFT JOIN $DataIn.bps B ON B.StuffId = D.StuffId 
															LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=B.CompanyId
															LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
															WHERE  DATE_FORMAT(F.Date,'%Y-%m')='$month' ", $link_id));
			$monthQty = number_format($Result1100["Qty"]); 	
			$monthAmount = number_format($Result1100["Amount"]);
			/*
			$dataArray[]=array(
				"View"=>"List",
				"Id"=>"1018",
				"onTap"=>array("Title"=>"报废","Value"=>"1","Tag"=>"StuffExt","Args"=>""),
				"RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
				"DateIcon"=>array("Type"=>"2","Frame"=>"70,10,20,20"),
				"Col_A"=>array("Title"=>"报废","Align"=>"L"),
				"Col_B"=>array("Title"=>"$SumQty","Color"=>"#0000FF","Align"=>"R"),
				"Col_C"=>array("Title"=>"¥$SumTotal","Align"=>"R")
			);
			*/
		
			include "../../desk/subtask/subtask-110.php";
			$SumQty = number_format($Result110["Qty"]); 	
			$SumTotal = number_format($Result110["Amount"]);
			/*
			$dataArray[]=array(
				"View"=>"List",
				"Id"=>"1018",
				"RowSet"=>array("Separator"=>"2","Height"=>"25"),
				"DateIcon"=>array("Type"=>"1","Frame"=>"70,0,20,20"),
				"Col_A"=>array("Title"=>" ","Align"=>"L"),
				"Col_B"=>array("Title"=>"$SumQty","Color"=>"#0000FF","Align"=>"R","Margin"=>"0,-10,0,0"),
				"Col_C"=>array("Title"=>"¥$SumTotal","Align"=>"R","Margin"=>"0,-10,0,0")
			);
			*/
			$col1Tag = array(
				"Title" => $thisMonth,
				"Color" => $colorNameTextTag,
				"BGColor" => $colorNameBGPositive,
			);
			
			$col3Tag = array(
				"Title" => $thisYear,
				"Color" => $colorNameTextTag,
				"BGColor" => $colorNameBGPositive,
			);
		
			$list = array(
				"Title"	=> array(
	        		"Text" => "报废",
				),
	        	"Col1"	=> array(
	        		"Text" => $monthQty,
	        		"Color" => $colorNameTextNormal,
	        		"Tag" => $col1Tag,
	        	), 
	        	"Col2"	=> array(
	        		"Text" => "¥".$monthAmount,
	        		"Color" => $colorNameText,
	        	), 
	        	"Col3"	=> array(
	        		"Text" => $SumQty,
	        		"Color" => $colorNameTextNormal,
	        		"Tag" => $col3Tag,
	        	), 
	        	"Col4"	=> array(
	        		"Text" => "¥".$SumTotal,
	        		"Color" => $colorNameText,
	        	), 
			);
			
			$layout = array(
	        	"Title" => array(
	        		"Frame" => "0, 0, 105, 85",
	        		"Align"	=> "M",
	        	),
	        	"Col1" => array(
	        		"Frame" => "125, 25, 75, 15",
	        		"Align"	=> "R",
	        	),
	        	"Col2" => array(
	        		"Frame" => "210, 25, 85, 15",
	        		"Align"	=> "R",
	        	),
	        	"Col3" => array(
	        		"Frame" => "125, 50, 75, 15",
	        		"Align"	=> "R",
	        	),
	        	"Col4" => array(
	        		"Frame" => "210, 50, 85, 15",
	        		"Align"	=> "R",
	        	),
			);
			
		    //"Id"=>"1018",
			//"onTap"=>array("Title"=>"报废","Value"=>"1","Tag"=>"StuffExt","Args"=>""),
			$dataArray[] = array(
				"View"		=> "List",
				"ModuleId"	=> "1018",
				"onTap"		=> array(
					"Title" => "报废",
					"Value" => "1",
					"Tag" => "StuffExt",
					"Args"=>""
				),
				"RowSet"	=> array(
					"Separator" => "2",
					"Height" => 85,
				),
	            "ChartData"	=> array(),
	            "ListData"	=> $list,
	            "Layout"	=> $layout,
			);
		}
			
		if (count($dataArray) > 0) {
			$jsonArray = array( 
				"Page" => "",
				"GroupName" => "",
				"Data" => $dataArray
			);   
		} 
		
		break;
}
      
    // $jsonArray[]=array( "GroupName"=>"","Data"=>$dataArray); 
?>