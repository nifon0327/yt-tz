<?php 
        //2、"审核":  
    $modelArray=array();
    
    $userIdResult=mysql_query("SELECT Id FROM  $DataSub.usertable WHERE Number='$LoginNumber' AND Estate=1 LIMIT 1",$link_id);
    if  ($userIdRow = mysql_fetch_array($userIdResult)){
            $userId=$userIdRow["Id"];
            
           $dModelId="";
            $dModuleIdResult=mysql_query("SELECT dModuleId FROM  $DataPublic.modulenexus WHERE  ModuleId='1044'",$link_id);
            while ($dModuleIdRow = mysql_fetch_array($dModuleIdResult)){
               $ModuleId=$dModuleIdRow["dModuleId"];
               $dModelId.=$dModelId==""?$ModuleId:",$ModuleId";
            }
            
            $rMenuResult = mysql_query("SELECT A.ModuleId,A.Action   
                    FROM $DataSub.upopedom A 
                    LEFT JOIN $DataPublic.funmodule B ON B.ModuleId=A.ModuleId 
                    LEFT JOIN  $DataPublic.modulenexus C ON C.dModuleId=A.ModuleId  
                    WHERE A.Action>0 AND B.TypeId=5 AND A.UserId='$userId' AND B.Estate=1 AND (C.ModuleId IN ($dModelId) OR  A.ModuleId=1347 OR  A.ModuleId=1009) ORDER BY B.OrderId",$link_id);
        while ($rMenuRow = mysql_fetch_array($rMenuResult)){
                    $modelArray[]=$rMenuRow["ModuleId"];
            }
          $badgenums202=0; 
            if (in_array("1347",$modelArray) || in_array("1245",$modelArray)){
                $banchIdSql=mysql_fetch_array(mysql_query("SELECT M.BranchId,M.GroupId FROM $DataPublic.staffmain M WHERE M.Number='$LoginNumber' LIMIT 1",$link_id));
				$BanchId= $banchIdSql["BranchId"];
				$GroupId=$banchIdSql["GroupId"];
				$CheckManager=mysql_query("SELECT B.Manager  FROM $DataIn.branchmanager B  WHERE B.Manager='$LoginNumber' ",$link_id);
				if (mysql_num_rows($CheckManager)>0){
				     $SearchRows=" AND M.BranchId='$BanchId'";
				}
				else{
				     $SearchRows=" AND M.GroupId='$GroupId'";
				}                    

                if ($LoginNumber=="10868" || $LoginNumber=="10006" || $LoginNumber=="10001" || $LoginNumber=="10341") {
                     $SearchRows="";
				 }
				 $SearchRows.=" AND M.cSign=3";
				 /*
				 if ($LoginUserId=="10019"){
					  $SearchRows=" AND M.cSign=3 ";
				 }   
				 */           
                $timeOut=getWorkLimitedTime(0,1347,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,J.OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts FROM $DataPublic.kqqjsheet J  
                                         LEFT JOIN $DataPublic.staffmain M ON M.Number=J.Number 
                WHERE J.Estate=1  AND M.Estate=1 $SearchRows";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];      
                $badgenums202+=$unCounts;
            }
            
          if (in_array("1107",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1107,$DataSub,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts FROM $DataSub.hzqksheet WHERE Estate=2";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                $badgenums202+=$unCounts;
            }
            
                  
         if (in_array("1301",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1301,$DataSub,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts FROM $DataSub.cw2_gyssksheet WHERE Estate=2";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                $badgenums202+=$unCounts;
            }

           if (in_array("1048",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1048,$DataSub,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts FROM $DataSub.cw2_fkdjsheet WHERE Estate=2";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                $badgenums202+=$unCounts;
            }
            
            if (in_array("1046",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1046,$DataSub,$link_id);
                 $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,A.opDateTime,NOW())>='$timeOut[0]',1,0)) AS unCounts 
                FROM (
                         SELECT MAX(G.Date) AS opDateTime FROM  $DataSub.cg1_stocksheet S
						LEFT JOIN  $DataSub.stuffdata A ON A.StuffId=S.StuffId
						LEFT JOIN  $DataSub.stufftype T ON A.TypeId=T.TypeId
						LEFT JOIN  $DataSub.cg1_stocksheet_log G  ON G.StockId=S.StockId AND G.opcode=3
						WHERE S.Estate=1 AND (S.FactualQty+S.AddQty)>0  AND  T.mainType<2 GROUP BY S.StockId 
						)A";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                $badgenums202+=$unCounts;
            }

            if (in_array("1463",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1463,$DataSub,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,M.OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts FROM $DataSub.ck2_thsheet S LEFT JOIN $DataSub.ck2_thmain M ON M.Id=S.Mid WHERE  S.Estate=2";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums202+=$unCounts;
             }
             
            if (in_array("1135",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1135,$DataSub,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,S.OPdatetime,NOW())>='$timeOut[0]',1,0))  AS unCounts 
                FROM $DataSub.ck8_bfsheet S WHERE  S.Estate>0";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums202+=$unCounts;
             }

              if (in_array("1269",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1269,$DataSub,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,A.opDateTime,NOW())>='$timeOut[0]',1,0)) AS unCounts
                 FROM (
                        SELECT MAX(G.Date) AS opDateTime FROM  $DataSub.cg1_stocksheet S
					                 LEFT JOIN  $DataSub.cg1_stocksheet_log G  ON G.StockId=S.StockId AND G.Opcode=4
					                 WHERE  S.Estate=4 
					)A ";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                $badgenums202+=$unCounts;
            }
           /*
             if (in_array("1213",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1213,$DataSub,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,S.OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts FROM $DataSub.productdata P LEFT JOIN $DataSub.productstandimg S ON S.ProductId=P.ProductId  WHERE P.TestStandard=2 and P.Estate>0";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                $badgenums202+=$unCounts;
             }
             */
              $jsonArray[] = array("Id"=>"206", "Name"=>"审核","Count"=>"$badgenums202","ColorSign"=>"1"); 
     }
      
      $itemArray=array();
      $TResult02 = mysql_query("SELECT A.ItemId
				FROM $DataPublic.tasklistdata A
				LEFT JOIN $DataIn.taskuserdata B ON B.ItemId=A.ItemId
				WHERE  A.Estate=1  AND B.UserId='$LoginNumber'",$link_id);
	 while($TRow02 = mysql_fetch_array($TResult02)){
	          $itemArray[]=$TRow02["ItemId"];
	    }
	    
	   if (in_array("173",$itemArray)){
                //4、新订单
                $checkSql="SELECT SUM(G.AddQty+G.FactualQty) AS Qty,SUM(IF(S.Date=CURDATE(),1,0)) AS CurDateSign  
                                FROM $DataSub.yw1_ordersheet S  
                                LEFT JOIN $DataIn.cg1_stocksheet G ON G.StockId=S.OrderNumber 
                                WHERE  DATE_FORMAT(S.Date,'%Y-%m' )=DATE_FORMAT(CURDATE(),'%Y-%m' ) ";//S.Date=CURDATE() 
                        $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
                        $badgenums209=$checkRow["Qty"]==""?0:round($checkRow["Qty"]/1000); 
                        $ColorSign=$checkRow["CurDateSign"]>0?2:4;
                        $jsonArray[] = array("Id"=>"209", "Name"=>"新订单","Count"=>"$badgenums209","ColorSign"=>"$ColorSign");
	    }
 
	     if (in_array("101",$itemArray)){
       //6、未出
          		  $checkSql="SELECT SUM(G.AddQty+G.FactualQty) AS wcQty,SUM(IFNULL(A.chQty,0)) AS chQty  
			       FROM  $DataSub.yw1_ordersheet S 
			       LEFT JOIN  $DataIn.cg1_stocksheet G  ON S.OrderNumber=G.StockId  
			       LEFT JOIN $DataSub.productdata P ON  P.ProductId=S.ProductId  
			       LEFT JOIN (
			                 SELECT C.POrderId,SUM(C.Qty) as chQty FROM $DataSub.ch1_shipsheet C 
			                 LEFT JOIN $DataSub.yw1_ordersheet Y ON C.POrderId=Y.POrderId 
			                 WHERE Y.Estate>0 GROUP BY Y.POrderId
			       )A ON A.POrderId=S.POrderId 
			     WHERE   S.Estate>0 and P.TypeId>0 AND YEARWEEK(G.DeliveryDate,1)<YEARWEEK(CURDATE(),1)";

		            $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
		            $wcQty=$checkRow["wcQty"]==""?0:$checkRow["wcQty"]; 
		            $chQty=$checkRow["chQty"]==""?0:$checkRow["chQty"]; 
		            $wcQty-=$chQty;
		            
		            $badgenums210=$wcQty>0?round($wcQty/1000):0;  
		            $jsonArray[] = array("Id"=>"210", "Name"=>"未出","Count"=>"$badgenums210","ColorSign"=>"2");
		            
		            $scResult=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS scQty  FROM $DataSub.sc1_cjtj WHERE  Date=CURDATE()  AND TypeId='7090'",$link_id)); 
                   $badgenums208=$scResult["scQty"]==""?0:round($scResult["scQty"]/1000); 
                   $jsonArray[] = array("Id"=>"208", "Name"=>"47生产","Count"=>"$badgenums208","ColorSign"=>"4");
	    }
	    
	     if (in_array("104",$itemArray)){
           //5、已出
             $curMonth=date("Y-m");
	        $checkSql="SELECT SUM(IFNULL(A.Qty,0)) AS Qty
  FROM (
		        SELECT SUM(S.Qty) AS Qty 
		        FROM $DataSub.ch1_shipmain M 
		        LEFT JOIN $DataSub.ch1_shipsheet S ON S.Mid=M.Id 
		        WHERE M.Estate=0 and DATE_FORMAT(M.Date,'%Y-%m')=DATE_FORMAT(CURDATE(),'%Y-%m') 
   UNION ALL
		   SELECT SUM(G.AddQty+G.FactualQty) AS Qty 
		   FROM  $DataSub.yw1_ordersheet S 
		   LEFT JOIN $DataIn.cg1_stocksheet G ON S.OrderNumber=G.StockId  
		   WHERE S.Estate>1)A ";
            $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
            $badgenums209=$checkRow["Qty"]==""?0:round($checkRow["Qty"]/1000);  
            $jsonArray[] = array("Id"=>"211", "Name"=>"已出","Count"=>"$badgenums209","ColorSign"=>"4");
	    }

	      //BOM采购
        if (in_array("1009",$modelArray)){
	        $checkSql="SELECT sum((A.Qty-A.rkQty)*S.Price*D.Rate) AS Amount 
					     FROM (
									SELECT B.StockId,B.Qty,SUM(IFNULL(B.rkQty,0)+IFNULL(B.SendQty,0)) as rkQty
									   FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty,0 AS SendQty  
									          FROM $DataSub.cg1_stocksheet S 
									          LEFT JOIN $DataSub.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataSub.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid>0 AND  S.rkSign>0 GROUP BY S.StockId
									   )B  GROUP BY B.StockId  
									)A 
						LEFT JOIN $DataSub.cg1_stocksheet S ON S.StockId=A.StockId   
						LEFT JOIN $DataSub.cg1_stockmain M ON M.Id=S.Mid  
						LEFT JOIN $DataSub.providerdata P ON P.CompanyId=M.CompanyId  
						LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency  
						WHERE A.Qty>A.rkQty AND YEARWEEK(S.DeliveryDate,1)<YEARWEEK(CURDATE(),1) AND S.DeliveryDate<>'0000-00-00' ";
            $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
            $badgenums207=$checkRow["Amount"]==""?0:round($checkRow["Amount"]/10000);  
            $jsonArray[] = array("Id"=>"207", "Name"=>"皮套采购","Count"=>"$badgenums207","ColorSign"=>"1");
	    }
	    
	           
      

   ?>