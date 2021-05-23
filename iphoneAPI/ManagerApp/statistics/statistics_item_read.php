<?php 
//统计数据
       $ReadAccessSign=2;
       include "user_access.php";  //用户权限
      
       $dataArray=array(); 
       $rowHeight=45;
  switch($NextPage){
    case 1:  
        if (in_array("120",$itemArray)){
            //现金结存
            include "../../desk/subtask/subtask-120.php";
            $SumTotalValue=number_format($SumTotal);
            $otherSumValue=number_format($SumAmount1)  . "/$" . number_format($SumAmount2) ."/€" . number_format($SumAmount5) ."/HK$" . number_format($SumAmount3) . "/NT$" .number_format($SumAmount4);
            $ItemName="现金结存";
             $dataArray[]=array(
	            "View"=>"List",
	            "Id"=>"120",
	             "RowSet"=>array("Separator"=>"0","Height"=>"30"),
	            "Col_A"=>array("Title"=>"$ItemName","Align"=>"L"),
	            "Col_C"=>array("Title"=>"¥$SumTotalValue","Align"=>"R")
	          );
	           $dataArray[]=array(
	            "View"=>"List",
	            "Id"=>"120",
	             "RowSet"=>array("Separator"=>"0.5","Height"=>"20"),
	            "Col_A"=>array("Title"=>"¥$otherSumValue","FontSize"=>"12","Color"=>"#CCCCCC","Align"=>"R","Margin"=>"0,-10,0,0")
	          );
        }
      
         if (in_array("121",$itemArray) || in_array("227",$itemArray)){
         //审核通过未结付总额
               if (in_array("121",$itemArray)){
                    include "../../desk/subtask/subtask-121.php";//$noPayRMB
               }
               //未付货款
	          $Result1183=mysql_fetch_array(mysql_query("
							SELECT SUM(S.Amount*D.Rate) AS NoPay 
							FROM $DataIn.cw1_fkoutsheet S
							LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
							LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency
					        WHERE S.Estate=3 ",$link_id));
		     $FK_NoPay=$Result1183["NoPay"]==""?0:$Result1183["NoPay"];
                //已付订金
                $djResult1183=mysql_fetch_array(mysql_query("SELECT SUM(S.Amount*C.Rate) AS djAmount   
	FROM $DataIn.cw2_fkdjsheet S 
	LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId
    LEFT JOIN $DataPublic.currencydata C ON C.Id=P.Currency
	 WHERE S.Did='0' and S.Estate=0 AND S.CompanyId AND EXISTS(select F.CompanyId FROM $DataIn.cw1_fkoutsheet F WHERE  F.Estate=3 AND F.CompanyId=S.CompanyId ) ",$link_id));
	            $dj_Payed=$djResult1183["djAmount"]==""?0:$djResult1183["djAmount"];
				$FK_NoPay-=$dj_Payed;
				
		  		if (in_array("121",$itemArray)){					        
					$SumTotalValue=number_format($noPayRMB-$FK_NoPay);
					 $dataArray[]=array(
			            "View"=>"List",
			            "Id"=>"121",
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"其他未付","Align"=>"L"),
			             "Col_C"=>array("Title"=>"¥$SumTotalValue","Align"=>"R")
			          );
				}
				 if (in_array("227",$itemArray)){
				    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"11830",
			             "onTap"=>array("Title"=>"未付","Value"=>"1","Tag"=>"multi","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"未付","Align"=>"L"),
			             "Col_C"=>array("Title"=>"¥".number_format($FK_NoPay),"Align"=>"R")
			          );
				 }
	   }


         if (in_array("122",$itemArray)){
            //未收客户货款总额
            include "../../desk/subtask/subtask-122.php";
            $SumTotalValue=number_format($GatheringSUM);
            //$dataArray1[] = array( "未收","¥$SumTotalValue","Black","1","122","multi","");//detail
		    $dataArray[]=array(
	            "View"=>"List",
	             "Id"=>"122",
	             "onTap"=>array("Title"=>"未收","Value"=>"1","Tag"=>"multi","Args"=>""),
	             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
	             "Col_A"=>array("Title"=>"未收","Align"=>"L"),
	             "Col_C"=>array("Title"=>"¥$SumTotalValue","Align"=>"R")
	          );
            
           if (in_array("120",$itemArray)){
                 //$jyAmount=$jy<0?"(¥" . number_format(abs($jy)) . ")":"¥".number_format($jy);
                 $jyColor=$jy>0?"#000000":"#FF0000";
                 $jyAmount="¥".number_format($jy);
                 $dataArray[]=array(
	            "View"=>"List",
	             "Id"=>"1221",
	             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
	             "Col_A"=>array("Title"=>"桌面结余","Align"=>"L"),
	             "Col_C"=>array("Title"=>"$jyAmount","Color"=>"$jyColor","Align"=>"R")
	          );
           }
        }
        
         if (in_array("125",$itemArray)){
                //损益表
                  // include "../../desk/subtask/subtask-125_sub.php";
                   if ($jyAmount=="")  include "../../desk/subtask/subtask-122.php";
                   include "../../desk/subtask/subtask-125.php";
                   $jyb=$jy-$lastMonthAmount;
                   $jyColor=$jyb>0?"#000000":"#FF0000";
                   //$jyb=$jyb<0?"(¥" . number_format(abs($jyb)) . ")":"¥".number_format($jyb);
                   $jyb="¥".number_format($jyb);
                   require(dirname(__FILE__)."/../deskpath.php");
                   // $jyb=number_format($Sum_A);
                   //$dataArray1[] = array( "损益表","¥$jyb","Red","0","125","","");
                   $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"125",
			             "RowSet"=>array("Separator"=>"2","Height"=>"$rowHeight"),
			              "onTap"=>array("Title"=>"损益表","Value"=>"1","Tag"=>"multi","Args"=>""),
			             "Col_A"=>array("Title"=>"损益表","Align"=>"L"),
			             "Col_C"=>array("Title"=>"$jyb","Color"=>"$jyColor","Align"=>"R")
			          );
           }
        
     $NextPage++; 
      if (count($dataArray)>0)  {
           $jsonArray[]=array("Page"=>"$NextPage","GroupName"=>"","Data"=>$dataArray); 
           $dataArray=array();
           break;
      }
 
     case 2:         
           if (in_array("107",$itemArray)){
               //在库
               $NoCompanySTR="AND P.CompanyId!='2166' ";
                $tStockResult = mysql_fetch_array(mysql_query("
						SELECT SUM(K.tStockQty) AS tStockQty,SUM(K.tStockQty*D.Price*C.Rate) AS Amount 
						FROM $DataIn.ck9_stocksheet K
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
						LEFT JOIN $DataIn.stuffmaintype MT On T.mainType = MT.Id
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataIn.currencydata C ON C.Id = P.Currency
						WHERE  D.Estate>0 AND K.tStockQty>0  AND MT.blSign=1 $NoCompanySTR",$link_id));//AND D.Estate>0 ,
				
			   $SumQty=number_format($tStockResult["tStockQty"]); 
			   $SumTotal=number_format($tStockResult["Amount"]);
			   
			  			          
			 //三个月以上未下采单
			$QtyResult= mysql_fetch_array(mysql_query("SELECT SUM(A.tStockQty) AS YearQty,SUM(A.tStockQty*D.Price*C.Rate) AS YearAmount
			FROM (
					SELECT S.StuffId,B.CompanyId,K.tStockQty,MAX(IFNULL(YM.OrderDate,M.Date)) AS DTime 
					FROM $DataIn.ck9_stocksheet K
					LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
					LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
					LEFT JOIN $DataIn.stuffmaintype MT On T.mainType = MT.Id
					LEFT JOIN $DataIn.bps B ON B.StuffId=K.StuffId   
					LEFT JOIN $DataIn.cg1_stocksheet S ON S.StuffId=K.StuffId
					LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
					LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId
			        LEFT JOIN $DataIn.yw1_ordermain YM ON Y.OrderNumber=YM.OrderNumber    
					WHERE  D.Estate>0 AND K.tStockQty>0  AND MT.blSign=1  GROUP BY K.StuffId 
			)A 
			LEFT JOIN $DataIn.stuffdata D ON D.StuffId = A.StuffId
			LEFT JOIN $DataIn.trade_object P ON P.CompanyId=A.CompanyId 
			LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency
			WHERE  TIMESTAMPDIFF(MONTH,A.DTime,Now())>=3 $NoCompanySTR",$link_id));//AND D.Estate>0  
			
			$SumQty_12=$QtyResult["YearQty"]>0?number_format($QtyResult["YearQty"]):""; 
			$SumTotal_12=$QtyResult["YearQty"]>0?number_format($QtyResult["YearAmount"]):"";
			
			$SepValue=$SumQty_12!=""?0:0.5;
			 $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"107",
			             "onTap"=>array("Title"=>"在库","Value"=>"1","Tag"=>"ext","Args"=>""),
			             "RowSet"=>array("Separator"=>"$SepValue","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"在库","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#0000FF","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$SumTotal","Align"=>"R")
			          );
			          
            //有订单需求的库存
            $lastYear=date("Y")-1;
           $oStockResult = mysql_fetch_array(mysql_query("SELECT SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty,X.OrderQty)) AS OrderQty,
						SUM(IF(X.OrderQty>K.tStockQty,K.tStockQty*D.Price*C.Rate,X.OrderQty*D.Price*C.Rate)) AS OrderAmount  
						FROM (
						SELECT A.StuffId,SUM(IFNULL(A.OrderQty,0)) AS OrderQty FROM(
						            SELECT K.StuffId,SUM(IFNULL(G.OrderQty,0)) AS OrderQty   
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN $DataIn.stuffmaintype MT On T.mainType = MT.Id
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.POrderId>0  AND  G.ywOrderDTime>'$lastYear-01-01' 
												WHERE  D.Estate>0 AND K.tStockQty>0  AND MT.blSign=1 Group by K.StuffId  
								   UNION ALL 
						                        SELECT K.StuffId,SUM(IFNULL(R.Qty*-1,0)) AS OrderQty  
												FROM $DataIn.ck9_stocksheet K
												LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
												LEFT JOIN $DataIn.stufftype T ON T.TypeId = D.TypeId 
												LEFT JOIN $DataIn.stuffmaintype MT On T.mainType = MT.Id
						                        LEFT JOIN $DataIn.cg1_stocksheet G ON G.StuffId=K.StuffId AND G.POrderId>0   AND  G.ywOrderDTime>'$lastYear-01-01'  
						                        LEFT JOIN $DataIn.ck5_llsheet R ON R.StockId=G.StockId 
												WHERE  D.Estate>0 AND K.tStockQty>0  AND MT.blSign=1 Group by K.StuffId)A GROUP BY A.StuffId 
						)X 
						LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=X.StuffId  
						LEFT JOIN $DataIn.stuffdata D ON D.StuffId = K.StuffId
						LEFT JOIN $DataIn.bps B ON B.StuffId=D.StuffId 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=B.CompanyId 
						LEFT JOIN $DataPublic.currencydata C ON C.Id = P.Currency 
						WHERE 1  $NoCompanySTR ",$link_id));
            
            $oStockQty=$oStockResult["OrderQty"]; 
			$oAmount=$oStockResult["OrderAmount"]; 
			if ($oStockQty>0){
			     $oStockQty=number_format($oStockQty); 
			     $oAmount=number_format($oAmount); 
				$dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"107",
			             "RowSet"=>array("Separator"=>"0","Height"=>"22"),
			             "Col_B"=>array("Title"=>"$oStockQty","Color"=>"#00A945","Align"=>"R","Margin"=>"0,-13,0,0"),
			             "Col_C"=>array("Title"=>"¥$oAmount","Color"=>"#000000","Margin"=>"0,-13,0,0","Align"=>"R")
			          );
			} 
			
			 if ($SumQty_12!=""){
				$dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"107",
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"22"),
			             "Col_B"=>array("Title"=>"$SumQty_12","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-13,0,0",
			                                       "DateIcon"=>array("Type"=>"4","Title"=>"3m")),
			             "Col_C"=>array("Title"=>"¥$SumTotal_12","Color"=>"#FF0000","Margin"=>"0,-13,0,0","Align"=>"R")
			          );
			}	   					
           }
          
           if (in_array("213",$itemArray)){
                    //已备料
                    /*
	                $Result213=mysql_fetch_array(mysql_query("SELECT SUM(B.Qty) AS blQty,SUM(Amount) AS Amount  
                        FROM (SELECT A.Qty,(A.Qty*A.Price*Rate) AS Amount,A.POrderId,IFNULL(L.Qty,0) AS scedQty,sum(IF(D.TypeId<>7100,G.OrderQty,0)) AS scQty
                               FROM (
									SELECT 
									S.POrderId,S.ProductId,S.Qty,S.Price,R.Rate,SUM(G.OrderQty) AS blQty,IFNULL(SUM(L.Qty),0) AS llQty,
									SUM(if(K.tStockQty>=(G.OrderQty-IFNULL(L.Qty,0)),(G.OrderQty-IFNULL(L.Qty,0)),0)) as K1,
									SUM(G.OrderQty-IFNULL(L.Qty,0)) AS K2  
									FROM $DataIn.yw1_ordermain M
									LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
									LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
									LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
									LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
									LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
								    LEFT JOIN $DataIn.trade_object CD ON M.CompanyId=CD.CompanyId
	                                LEFT JOIN $DataPublic.currencydata R ON R.Id=CD.Currency
									LEFT JOIN $DataIn.yw2_orderexpress E ON E.POrderId=S.POrderId AND E.Type=2 
									LEFT JOIN (
												 SELECT L.StockId,SUM(L.Qty) AS Qty 
												 FROM $DataIn.yw1_ordersheet S 
												 LEFT JOIN $DataIn.cg1_stocksheet G ON S.POrderId=G.POrderId
												 LEFT JOIN $DataIn.ck5_llsheet L ON G.StockId=L.StockId 
												 WHERE 1  AND S.scFrom>0 AND S.Estate=1 GROUP BY L.StockId
											 ) L ON L.StockId=G.StockId
									WHERE 1 AND S.scFrom>0 AND S.Estate=1 AND ST.mainType<2 AND E.POrderId IS NULL   GROUP BY S.POrderId 
									) A 
                                 LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=A.POrderId 
                                 LEFT JOIN $DataIn.cg1_lockstock GL ON G.StockId=GL.StockId  AND GL.Locks=0 
                                  LEFT JOIN (
									 SELECT S.POrderId,SUM(L.Qty) AS Qty 
									 FROM $DataIn.yw1_ordersheet S 
									 LEFT JOIN $DataIn.sc1_cjtj L ON S.POrderId=L.POrderId
									 WHERE  S.scFrom>0 AND S.Estate=1 and L.TypeId<>7100 GROUP BY S.POrderId 
						         ) L ON A.POrderId=L.POrderId 
                                 LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId  
                                 LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
								WHERE A.K1>=A.K2 AND A.blQty=A.llQty AND ST.mainType=3  AND GL.StockId IS NULL  GROUP BY A.POrderId 
                      )B  ",$link_id));//AND L.Estate=0 
                      */
                     $Result213=mysql_fetch_array(mysql_query(" SELECT SUM(B.Qty) AS blQty,SUM(B.Amount) AS Amount  FROM (
           SELECT A.POrderId,A.Qty,(A.Qty*A.Price*A.Rate) AS Amount 
                FROM (	
					  SELECT S0.POrderId,S0.Qty,S0.Price,S0.Rate,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty 
					    FROM (      
						     SELECT S.POrderId,S.Qty,S.Price,R.Rate,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
									FROM $DataIn.yw1_ordermain M
									LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
									LEFT JOIN $DataIn.trade_object O ON O.CompanyId=M.CompanyId
									LEFT JOIN $DataPublic.currencydata R ON R.Id=O.Currency
									LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
									LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
									LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
									LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId  
									WHERE 1 AND S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  AND NOT EXISTS(SELECT T.StuffId FROM $DataIn.stuffproperty T WHERE T.StuffId=G.StuffId AND T.Property='8')
									GROUP BY G.StockId 
						   )S0 GROUP BY S0.POrderId 
				   )A WHERE A.blQty=A.llQty  AND EXISTS (
								   SELECT ST.mainType 
								   FROM $DataIn.cg1_stocksheet G 
								   LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
								   LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
								   WHERE G.POrderId=A.POrderId AND ST.mainType=3
								 )
     )B  
                      ",$link_id));
                    $SumQty=number_format($Result213["blQty"]); 
			        $SumTotal=number_format($Result213["Amount"]);
                    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"213",
			             "onTap"=>array("Title"=>"待组装","Value"=>"1","Tag"=>"Production","Args"=>""),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"待组装","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#0000FF","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$SumTotal","Align"=>"R")
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
             $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"216",
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "onTap"=>array("Title"=>"待出","Value"=>"1","Tag"=>"OrderExt","Args"=>""),//List0
			             "Col_A"=>array("Title"=>"待出","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$waitQtyValue","Color"=>"#0000FF","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$waitAmountValue","Align"=>"R")
			             );
			             
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
	         $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"2160",
			             "RowSet"=>array("Separator"=>"2","Height"=>"22"),
			             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-13,0,0",
			                                         "DateIcon"=>array("Type"=>"4","Title"=>"5d")),
			             "Col_C"=>array("Title"=>"¥$SumTotal","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-13,0,0")
			          );

        }
    $NextPage++; 
      if (count($dataArray)>0)  {
           $jsonArray[]=array("Page"=>"$NextPage","GroupName"=>"","Data"=>$dataArray); 
           $dataArray=array();
           break;
      }
     case 3: 
         $orderExtTag=versionToNumber($AppVersion)>=277?"OrderExt2":"OrderExt";//Created by 2014/08/29    
         if (in_array("123",$itemArray)){
            //未出货订单总额
            include "../../desk/subtask/subtask-123.php";
            $SumTotalValue=number_format($noshipAmount);
            $GrossProfit=number_format($GrossProfit); 
            $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"110",
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "onTap"=>array("Title"=>"未出","Value"=>"1","Tag"=>"OrderOut2","Args"=>""),
			             "Col_A"=>array("Title"=>"未出","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$noshipQty1","Color"=>"#0000FF","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$SumTotalValue","Align"=>"R","AboveTitle"=>"$GrossProfitPcnt%(¥$GrossProfit)","AboveColor"=>"#00A945")
			          );

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
	        $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"110",
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"20"),
			             "Col_A"=>array("Title"=>"$WaitQty","Align"=>"L","Color"=>"#00A945","Margin"=>"40,-15,0,0"),
			             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-15,0,0"),
			             "Col_C"=>array("Title"=>"¥$SumTotal","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-15,0,0")
			          );
		         
            
              //本月下单金额
            $month=date("Y-m");
              $InResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Price*S.Qty*D.Rate) AS Amount
                   FROM $DataIn.yw1_ordermain M
                   LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
                  LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
				  LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
                   WHERE DATE_FORMAT(M.OrderDate,'%Y-%m')='$month'",$link_id));
                   $inQtyValue=number_format($InResult["Qty"]);
		           $inAmountValue=number_format(sprintf("%.0f",$InResult["Amount"]));
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
		      //全年下单金额
		      $year=date("Y");
              $yearResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty,SUM(S.Price*S.Qty*D.Rate) AS Amount
                   FROM $DataIn.yw1_ordermain M
                   LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber
                  LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
				  LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
                   WHERE DATE_FORMAT(M.OrderDate,'%Y')='$year'",$link_id));
                   $yearQty=number_format($yearResult["Qty"]);
		            $yearAmount=number_format(sprintf("%.0f",$yearResult["Amount"]));

			 $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1232",
			              "DateIcon"=>array("Type"=>"1","Frame"=>"70,0,20,20"),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"25"),
			             "Col_B"=>array("Title"=>"$yearQty","Color"=>"#0000FF","Align"=>"R","Margin"=>"0,-10,0,0"),
			             "Col_C"=>array("Title"=>"¥$yearAmount","Align"=>"R","Margin"=>"0,-10,0,0")
			          );

  
            //本月出货总额
            $month=date("Y-m");			
             $ShipResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty  
                            FROM $DataIn.ch1_shipmain M
                            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
                            WHERE  M.Estate='0' AND DATE_FORMAT(M.Date,'%Y-%m')='$month' AND (S.Type=1 OR S.Type=3)",$link_id));

                            $shipQtyValue=number_format(sprintf("%.0f",$ShipResult["Qty"]));		
                            			
            $ShipResult=mysql_fetch_array(mysql_query("SELECT SUM( S.Price*S.Qty*M.Sign*D.Rate) AS Amount 
                            FROM $DataIn.ch1_shipmain M
                            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
                            LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
                            LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
                            WHERE  M.Estate='0' AND DATE_FORMAT(M.Date,'%Y-%m')='$month' ",$link_id));
                            $shipAmountValue=number_format(sprintf("%.0f",$ShipResult["Amount"]));  
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
             
             //本年出货总额
             $year=date("Y");			
             $ShipResult=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty) AS Qty  
                            FROM $DataIn.ch1_shipmain M
                            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
                            WHERE  M.Estate='0' AND YEAR(M.Date)='$year' AND (S.Type=1 OR S.Type=3)",$link_id));

                            $yearQty=number_format(sprintf("%.0f",$ShipResult["Qty"]));		
                            			
            $ShipResult=mysql_fetch_array(mysql_query("SELECT SUM( S.Price*S.Qty*M.Sign*D.Rate) AS Amount 
                            FROM $DataIn.ch1_shipmain M
                            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid = M.Id
                            LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
                            LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency
                            WHERE  M.Estate='0' AND YEAR(M.Date)='$year' ",$link_id));
                            $yearAmount=number_format(sprintf("%.0f",$ShipResult["Amount"]));
                            
              $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1232",
			              "DateIcon"=>array("Type"=>"1","Frame"=>"70,0,20,20"),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"25"),
			             "Col_B"=>array("Title"=>"$yearQty","Color"=>"#0000FF","Align"=>"R","Margin"=>"0,-10,0,0"),
			             "Col_C"=>array("Title"=>"¥$yearAmount","Align"=>"R","Margin"=>"0,-10,0,0")
			          );
       }

        
        if (in_array("198",$itemArray)){
            //本月报关总额
            include "../../desk/subtask/subtask-198.php";
            $SumTotalValue=number_format($BgAmount);
            $BgPre=$BgPre==""?0:$BgPre;

            $dataArray[]=array(
		            "View"=>"List",
		             "Id"=>"198",
		              "DateIcon"=>array("Type"=>"2","Frame"=>"165,10,20,20"),
		             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
		             "onTap"=>array("Title"=>"报关","Value"=>"1","Tag"=>"OrderExt","Args"=>""),//"Tag"=>"shiped"
		             "Col_A"=>array("Title"=>"报关","Align"=>"L"),
		             "Col_C"=>array("Title"=>"¥$SumTotalValue","Align"=>"R","AboveTitle"=>"($BgPre%)")
		             );

			//全年报关
			 $year=date("Y");	
			 $totalMyResult=mysql_fetch_array(mysql_query("SELECT  SUM(S.Qty*S.Price*D.Rate) AS Amount 
				FROM $DataIn.ch1_shipmain M 
				LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
				LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId 
			    LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
				LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
				WHERE 1 AND M.Estate='0'  AND (S.Type=1 OR S.Type=3) AND YEAR(M.Date)='$year'   AND P.ProductId>0 ",$link_id));
			$yearTotal=$totalMyResult["Amount"];

			$yearTotalSql=mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Price*D.Rate) AS Amount 
				FROM $DataIn.ch1_shipmain M 
				LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
				LEFT JOIN $DataIn.productdata P ON P.ProductId=S.ProductId  
			    LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
			    LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
				LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
				WHERE  M.Estate='0' AND (S.Type=1 OR S.Type=3) AND P.ProductId>0 AND T.Type=1 AND YEAR(M.Date)='$year' ",$link_id));
			   $yearAmount=number_format($yearTotalSql["Amount"]);
			   $yearBgPre=$yearTotal==0?0:sprintf("%.0f",($yearTotalSql["Amount"]*100/$yearTotal));
			     
			    $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"198",
			              "DateIcon"=>array("Type"=>"1","Frame"=>"165,10,20,20"),
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"35"),
			             "Col_C"=>array("Title"=>"¥$yearAmount","Align"=>"R","AboveTitle"=>"($yearBgPre%)")
			          );
        }
        
     //退税
     if (in_array("125",$itemArray)){
			$gysskResult=mysql_fetch_array(mysql_query("SELECT SUM(IFNULL(A.Amount,0)) AS Amount FROM(
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
				                WHERE (F.Estate=3 OR F.Estate=0) AND YEAR(F.Date)='$year')A " ,$link_id));
			$skAmount=number_format($gysskResult["Amount"]);	 
			
			$taxResult=mysql_fetch_array(mysql_query("SELECT SUM(M.Taxamount) AS Amount 
                   FROM $DataIn.cw14_mdtaxmain M   WHERE YEAR(M.taxdate)='$year'" ,$link_id));
              $taxAmount=number_format($taxResult["Amount"]);	
              
           $mdAmount=number_format($taxResult["Amount"]-$gysskResult["Amount"]);                  
			$dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1250",
			             "DateIcon"=>array("Type"=>"1","Frame"=>"70,10,20,20"),
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"退税","Align"=>"L"),
			             "Col_B"=>array("Title"=>"¥$taxAmount","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$skAmount","Align"=>"R")
			             );	
			 
              $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1250",
			             "RowSet"=>array("Separator"=>"2","Height"=>"25"),
			             "Col_C"=>array("Title"=>"¥$mdAmount","Color"=>"#00A945","Align"=>"R","Margin"=>"0,-10,0,0")
			             );
		 }
         $NextPage++; 
	      if (count($dataArray)>0)  {
	           $jsonArray[]=array("Page"=>"$NextPage","GroupName"=>"","Data"=>$dataArray); 
	           $dataArray=array();
	           break;
	      }

		case 4:	     
      //采购
      if (in_array("129",$itemArray)){
           $month=date("Y-m");
           $Result129=mysql_fetch_array(mysql_query("
							SELECT SUM(S.Amount*D.Rate) AS Amount,SUM(IF(S.Estate=3,S.Amount*D.Rate,0)) AS NoPay   
							FROM $DataIn.cw1_fkoutsheet S
							LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
							LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency
					        WHERE (S.Estate=3 OR S.Estate=0) AND S.Month='$month' ",$link_id));
			$cgAmount=number_format($Result129["Amount"]);
			$NoPayAmount  =number_format($Result129["NoPay"]);     
			$dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"129",
			             "DateIcon"=>array("Type"=>"2","Frame"=>"70,10,20,20"),
			             "RowSet"=>array("Separator"=>"0","Height"=>"$rowHeight"),
			             "Col_A"=>array("Title"=>"采购","Align"=>"L"),
			             "Col_B"=>array("Title"=>"¥$NoPayAmount","Color"=>"#FF0000","Align"=>"R"),
			             "Col_C"=>array("Title"=>"¥$cgAmount","Align"=>"R")
			             );
			             
			 //年度统计
			 $year=date("Y");
			 $yearResult=mysql_fetch_array(mysql_query("
							SELECT SUM(S.Amount*D.Rate) AS Amount,SUM(IF(S.Estate=3,S.Amount*D.Rate,0)) AS NoPay   
							FROM $DataIn.cw1_fkoutsheet S
							LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId 
							LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency
					        WHERE (S.Estate=3 OR S.Estate=0) AND LEFT(S.Month,4)='$year' ",$link_id));
			$yearAmount=number_format($yearResult["Amount"]);
			$yearNoPay =number_format($yearResult["NoPay"]);     
			$dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"129",
			             "DateIcon"=>array("Type"=>"1","Frame"=>"70,0,20,20"),
			             "RowSet"=>array("Separator"=>"2","Height"=>"25"),
			             "Col_B"=>array("Title"=>"¥$yearNoPay","Color"=>"#FF0000","Align"=>"R","Margin"=>"0,-10,0,0"),
			             "Col_C"=>array("Title"=>"¥$yearAmount","Align"=>"R","Margin"=>"0,-10,0,0")
			             );

      }
      
        if (in_array("220",$itemArray)){
                //备品转入
                $month=date("Y-m");
                $Result2200=mysql_fetch_array(mysql_query("SELECT  SUM(B.Qty) as Qty,SUM(D.Price*B.Qty*C.Rate) AS Amount
							FROM $DataIn.ck7_bprk B 
							LEFT JOIN $DataIn.stuffdata D ON B.StuffId=D.StuffId
							LEFT JOIN $DataIn.bps F ON F.StuffId = D.StuffId 
							LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=F.CompanyId
							LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
							 WHERE  DATE_FORMAT(B.Date,'%Y-%m')='$month' ",$link_id));
                   $SumQty=number_format($Result2200["Qty"]); 	
				   $SumTotal=number_format($Result2200["Amount"]);
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

                   include "../../desk/subtask/subtask-220.php";
                   $SumQty=number_format($Result220["Qty"]); 	
				   $SumTotal=number_format($Result220["Amount"]);
                   $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"220",
			             "RowSet"=>array("Separator"=>"0.5","Height"=>"25"),
			              "DateIcon"=>array("Type"=>"1","Frame"=>"70,0,20,20"),
			             "Col_A"=>array("Title"=>" ","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#0000FF","Align"=>"R","Margin"=>"0,-10,0,0"),
			             "Col_C"=>array("Title"=>"¥$SumTotal","Align"=>"R","Margin"=>"0,-10,0,0")
			          );
           }

  if (in_array("110",$itemArray)){
                //配件报废
                $month=date("Y-m");
                $Result1100=mysql_fetch_array(mysql_query("SELECT  SUM(F.Qty) as Qty,SUM(D.Price*F.Qty*C.Rate) AS Amount
							FROM $DataIn.ck8_bfsheet F
							LEFT JOIN $DataIn.stuffdata D ON F.StuffId=D.StuffId 
							LEFT JOIN $DataIn.bps B ON B.StuffId = D.StuffId 
							LEFT JOIN  $DataIn.trade_object P ON P.CompanyId=B.CompanyId
							LEFT JOIN  $DataPublic.currencydata C ON C.Id=P.Currency
							 WHERE  DATE_FORMAT(F.Date,'%Y-%m')='$month' ",$link_id));
                   $SumQty=number_format($Result1100["Qty"]); 	
				   $SumTotal=number_format($Result1100["Amount"]);
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

                   include "../../desk/subtask/subtask-110.php";
                   $SumQty=number_format($Result110["Qty"]); 	
				   $SumTotal=number_format($Result110["Amount"]);
                   $dataArray[]=array(
			            "View"=>"List",
			             "Id"=>"1018",
			             "RowSet"=>array("Separator"=>"2","Height"=>"25"),
			             "DateIcon"=>array("Type"=>"1","Frame"=>"70,0,20,20"),
			             "Col_A"=>array("Title"=>" ","Align"=>"L"),
			             "Col_B"=>array("Title"=>"$SumQty","Color"=>"#0000FF","Align"=>"R","Margin"=>"0,-10,0,0"),
			             "Col_C"=>array("Title"=>"¥$SumTotal","Align"=>"R","Margin"=>"0,-10,0,0")
			          );
           }
       if (count($dataArray)>0){
		       $NextPage="END";
		       $jsonArray[]=array( "Page"=>"$NextPage","GroupName"=>"","Data"=>$dataArray);   
		  } 
          break;
   }
      
    // $jsonArray[]=array( "GroupName"=>"","Data"=>$dataArray); 
?>