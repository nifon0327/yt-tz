<?php 
  //读取通知信息数
   //1、"通知":  
        $recordSql=mysql_query("SELECT ReadTime FROM  $DataPublic.app_readrecord WHERE Number='$LoginNumber' AND Item='Msg' LIMIT 1",$link_id);
        if ($recordRow = mysql_fetch_array($recordSql))
        {
            $readTime=$recordRow["ReadTime"];
        }
        else{
        $readTime=date("Y-m-d") . " 00:00:00"; 
        }

        $mySql = "select SUM(A.nums) AS nums FROM (
                                select  count(*) as nums FROM $DataPublic.msg1_bulletin WHERE SMSTime>'$readTime' 
                            UNION ALL 
                                select count(*) as nums  FROM $DataPublic.msg3_notice   WHERE SMSTime>'$readTime' 
                            )A ";
    $badgeResult=mysql_query($mySql,$link_id);
    $badgenums=mysql_result($badgeResult,0,"nums");
    $jsonArray[] = array("Id"=>"101", "Name"=>"通知","Count"=>"$badgenums","ColorSign"=>"1");
    
      //2、"审核":  
    $badgenums2=0;
    $modelArray=array();
    $userIdResult=mysql_query("SELECT Id FROM  $DataIn.usertable WHERE Number='$LoginNumber' AND Estate=1 LIMIT 1",$link_id);
    if  ($userIdRow = mysql_fetch_array($userIdResult)){
            $userId=$userIdRow["Id"];
            
            // $dModelId="1057";
            $dModuleIdResult=mysql_query("SELECT dModuleId FROM  $DataPublic.modulenexus WHERE  ModuleId='1044'",$link_id);
            while ($dModuleIdRow = mysql_fetch_array($dModuleIdResult)){
               $ModuleId=$dModuleIdRow["dModuleId"];
               $dModelId.=$dModelId==""?$ModuleId:",$ModuleId";
            }
            
            $rMenuResult = mysql_query("SELECT A.ModuleId,A.Action   
                    FROM $DataIn.upopedom A 
                    LEFT JOIN $DataPublic.funmodule B ON B.ModuleId=A.ModuleId 
                    LEFT JOIN  $DataPublic.modulenexus C ON C.dModuleId=A.ModuleId  
                    WHERE A.Action>0 AND B.TypeId=5 AND A.UserId='$userId' AND B.Estate=1 AND (C.ModuleId IN ($dModelId) OR  A.ModuleId=1347  OR  A.ModuleId=1245) ORDER BY B.OrderId",$link_id);
        while ($rMenuRow = mysql_fetch_array($rMenuResult)){
                    $modelArray[]=$rMenuRow["ModuleId"];
            }
            
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
				 $SearchRows.=" AND M.cSign=7";
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
                $badgenums2+=$unCounts;
            }

           if (in_array("1107",$modelArray)){
               $timeOut=getWorkLimitedTime(0,1107,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts FROM $DataIn.hzqksheet WHERE Estate=2";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                $badgenums2+=$unCounts;
            }
            
             if (in_array("1301",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1301,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts FROM $DataIn.cw2_gyssksheet WHERE Estate=2";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                $badgenums2+=$unCounts;
            }

          if (in_array("1048",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1048,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts FROM $DataIn.cw2_fkdjsheet WHERE Estate=2";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                $badgenums2+=$unCounts;
            }
 
            if (in_array("1046",$modelArray)){
              $timeOut=getWorkLimitedTime(0,1046,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,A.opDateTime,NOW())>='$timeOut[0]',1,0)) AS unCounts 
                FROM (
                         SELECT MAX(G.Date) AS opDateTime FROM  $DataIn.cg1_stocksheet S
						LEFT JOIN  $DataIn.stuffdata A ON A.StuffId=S.StuffId
						LEFT JOIN  $DataIn.stufftype T ON A.TypeId=T.TypeId
						LEFT JOIN  $DataIn.cg1_stocksheet_log G  ON G.StockId=S.StockId AND G.opcode=3
						WHERE S.Estate=1 AND (S.FactualQty+S.AddQty)>0  AND  T.mainType<2  GROUP BY S.StockId  
						)A";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                $badgenums2+=$unCounts;
            }

           if (in_array("1463",$modelArray)){
               $timeOut=getWorkLimitedTime(0,1463,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,M.OPdatetime,NOW())>='$timeOut[0]',1,0))  AS unCounts FROM $DataIn.ck2_thsheet S  LEFT JOIN ck2_thmain M ON M.Id=S.Mid WHERE  S.Estate=2";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }
             
             if (in_array("1135",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1135,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,S.OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts FROM $DataIn.ck8_bfsheet S WHERE  S.Estate=1 OR S.Estate=2";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }

            if (in_array("1269",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1269,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,A.opDateTime,NOW())>='$timeOut[0]',1,0)) AS unCounts
                 FROM (
                        SELECT MAX(G.Date) AS opDateTime FROM  $DataIn.cg1_stocksheet S
					                 LEFT JOIN  $DataIn.cg1_stocksheet_log G  ON G.StockId=S.StockId AND G.Opcode=4
					                 WHERE  S.Estate=4  GROUP BY S.StockId  
					)A ";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                $badgenums2+=$unCounts;
            }
            
            if (in_array("1371",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1371,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,S.OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts FROM $DataIn.cw4_otherin S WHERE  S.Estate=1";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }
             
              if (in_array("1524",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1524,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,E.OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts
                 FROM $DataIn.yw2_orderexpress  E
                 LEFT JOIN $DataIn.yw1_ordersheet S  ON S.POrderId=E.POrderId
                  WHERE   S.Estate>0 AND E.Estate=1 AND E.Type=2 ";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }

             if (in_array("1525",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1525,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,L.OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts
                 FROM $DataIn.cg1_lockstock  L 
                  LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=L.StockId
                  WHERE   S.Mid=0 and (S.FactualQty>0 OR S.AddQty>0)  AND L.Estate=1  AND L.Locks=0 ";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }
			 
			
			 /*
			 
			  //快递
			 if (in_array("1108",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1108,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,L.Date,NOW())>='$timeOut[0]',1,0)) AS unCounts
                 FROM $DataIn.ch9_expsheet  L 
                  WHERE   L.Estate=2 AND L.Locks=0 ";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }

	 //杂费
			 if (in_array("1051",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1051,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,L.Date,NOW())>='$timeOut[0]',1,0)) AS unCounts
                 FROM $DataIn.ch3_forward  L 
                  WHERE   L.Estate=2 AND L.Locks=0 ";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }
			 */
			  //免抵退
			  /*
			 if (in_array("1051",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1051,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,L.TaxDate,NOW())>='$timeOut[0]',1,0)) AS unCounts
                 FROM $DataIn.cw14_mdtaxmain  L 
                  WHERE   L.Estate=2 ";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }
			 */
			   //备品转入
			   /*
			 if (in_array("1620",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1620,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,L.Date,NOW())>='$timeOut[0]',1,0)) AS unCounts
                 FROM $DataIn.ck7_bprk  L 
                  WHERE   L.Estate=1 and  L.Locks=0 ";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }
			 */
			   //扣款
			 if (in_array("1359",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1359,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,L.OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts
                 FROM $DataIn.cw15_gyskkmain  L 
                  WHERE    L.Estate=1  and L.Locks=0 ";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }
			 
			    //保险款
				/*
			 if (in_array("1161",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1161,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,L.Date,NOW())>='$timeOut[0]',1,0)) AS unCounts
                 FROM $DataIn.sbpaysheet  L 
                  WHERE    L.Estate=2  and L.Locks=0 ";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }
			 */
			    //离职补助款
			 if (in_array("1598",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1598,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,L.OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts
                 FROM $DataIn.staff_outsubsidysheet  L 
                  WHERE    L.Estate=2 and L.Locks=0 ";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }
			   //车辆费用
			 if (in_array("1595",$modelArray)){
                $timeOut=getWorkLimitedTime(0,1595,$DataIn,$link_id);
                $unAuditSql="SELECT SUM(IF(TIMESTAMPDIFF(HOUR,L.OPdatetime,NOW())>='$timeOut[0]',1,0)) AS unCounts
                 FROM $DataIn.carfee  L 
                  WHERE    L.Estate=2 and L.Locks=0 ";
                $unAuditRow=mysql_fetch_array(mysql_query($unAuditSql,$link_id));
                $unCounts=$unAuditRow["unCounts"]==""?0:$unAuditRow["unCounts"];
                 $badgenums2+=$unCounts;
             }




             $jsonArray[] = array("Id"=>"106", "Name"=>"审核","Count"=>"$badgenums2","ColorSign"=>"1"); 
     }
     

 //3、"个人助理"
      $curDate=date("Y-m-d");
      $sMonth=date("Y-m",strtotime("$curDate  -3   month"));
      $unSignRow=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS unCounts  FROM
       (
             SELECT S.Month FROM  $DataIn.cwxzsheet S 
                 WHERE S.Number='$LoginNumber' AND S.Estate=0 AND S.Month>='$sMonth' 
                 AND S.Month NOT IN (SELECT SignMonth FROM $DataPublic.wage_list_sign WHERE Number='$LoginNumber')
       UNION ALL 
	          SELECT S.Month FROM  $DataOut.cwxzsheet S 
	             WHERE S.Number='$LoginNumber' AND S.Estate=0 AND S.Month>='$sMonth'
                 AND S.Month NOT IN (SELECT SignMonth FROM $DataPublic.wage_list_sign WHERE Number='$LoginNumber')
      ) A",$link_id));

      $badgenums3=$unSignRow["unCounts"]==""?0:$unSignRow["unCounts"];
      $jsonArray[] = array("Id"=>"113", "Name"=>"个人助理","Count"=>"$badgenums3","ColorSign"=>"1");  
      
    $ReadAccessSign=3;
     include "user_access.php";  //用户权限
     
     //显示现金结余
      if (in_array("120",$itemArray)){
            include "../../desk/subtask/subtask-120.php";
            include "../../desk/subtask/subtask-121.php";
            include "../../desk/subtask/subtask-122.php";
            $ColorSign=$jy>0?3:1;
            $jyAmount=number_format(abs($jy/10000));
           $jsonArray[] = array("Id"=>"105", "Name"=>"统计","Count"=>"0","Text"=>"$jyAmount","ColorSign"=>"$ColorSign");
     }
        
  	     $badgenums4=0;$badgenums5=0;
	    if (in_array("173",$itemArray)){
                //4、新订单
                $checkSql="SELECT SUM(S.Price*S.Qty*D.Rate) AS Amount,SUM(IF(M.OrderDate=CURDATE(),1,0)) AS CurDateSign  
                        FROM $DataIn.yw1_ordersheet S 
                        LEFT JOIN $DataIn.yw1_ordermain M ON M.OrderNumber=S.OrderNumber
                        LEFT JOIN $DataIn.trade_object C ON C.CompanyId = M.CompanyId
					    LEFT JOIN $DataPublic.currencydata D ON D.Id = C.Currency 
                        WHERE  DATE_FORMAT( M.OrderDate,'%Y-%m' )=DATE_FORMAT(CURDATE(),'%Y-%m' ) ";//M.OrderDate=CURDATE()
                        $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
                        $badgenums4=$checkRow["Amount"]==""?0:round($checkRow["Amount"]/10000); 
                        $ColorSign=$checkRow["CurDateSign"]>0?1:3;
                        $badgenums4=$badgenums4>100?round($badgenums4/100) . "M":$badgenums4; 
                        $jsonArray[] = array("Id"=>"109", "Name"=>"新单","Count"=>"0","Text"=>"$badgenums4","ColorSign"=>"$ColorSign");
	    }

	    if (in_array("104",$itemArray)){
           //5、已出
             $curMonth=date("Y-m");
	        $checkSql="SELECT SUM(S.Qty*S.Price*D.Rate*M.Sign) AS Amount  
	            FROM $DataIn.ch1_shipmain M 
	            LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
	            LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId 
	            LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency 
	            WHERE M.Estate='0' and DATE_FORMAT( M.Date,'%Y-%m' )=DATE_FORMAT(CURDATE(),'%Y-%m' ) ";
            $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
            $badgenums5=$checkRow["Amount"]==""?0:round($checkRow["Amount"]/10000);   
            $jsonArray[] = array("Id"=>"111", "Name"=>"已出","Count"=>"$badgenums5","ColorSign"=>"3");
	    }
	    
	     if (in_array("101",$itemArray)){
          //6、未出
          if (versionToNumber($AppVersion)<322){
		        $checkSql="SELECT SUM(S.Qty*S.Price*D.Rate) AS Amount 
									FROM $DataIn.yw1_ordersheet S
									LEFT JOIN $DataIn.yw1_ordermain M  ON S.OrderNumber=M.OrderNumber 
									LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
									LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency  WHERE S.Estate>0";
	            $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
	            $badgenums6=$checkRow["Amount"]==""?0:round($checkRow["Amount"]/1000000);  
	            $badgenums6=$badgenums6>0?$badgenums6 . "M":"";
            }
            else{
	           $badgenums6="";
            }
            
               //逾期未出     
         $OverResult = mysql_fetch_array(mysql_query("SELECT SUM(S.Qty*S.Price*D.Rate) AS Amount 
									FROM $DataIn.yw1_ordersheet S 
									LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
									LEFT JOIN $DataIn.yw1_ordermain M ON S.OrderNumber=M.OrderNumber 
									LEFT JOIN $DataIn.trade_object C ON C.CompanyId=M.CompanyId
									LEFT JOIN $DataPublic.currencydata D ON D.Id=C.Currency
									WHERE  S.Estate=1  AND PI.Leadtime IS NOT NULL  AND YEARWEEK(PI.Leadtime,1)<YEARWEEK(CURDATE(),1)",$link_id));
		 $badgenums60=$OverResult["Amount"]==""?0:round($OverResult["Amount"]/1000000);
		 $badgenums60=$badgenums60>0?$badgenums60 . "M":"";
            //$jsonArray[] = array("Id"=>"110", "Name"=>"未出","Count"=>"0","Text"=>"$badgenums6","ColorSign"=>"3");
             $jsonArray[] = array("Id"=>"110", "Name"=>"未出","Text"=>"$badgenums60","ColorSign"=>"1","LeftBadge"=>"$badgenums6");
	    }
	    
	     if (in_array("213",$itemArray)){
                    //生产逾期
					$checkSql="SELECT SUM(IF(YEARWEEK(substring(B.Leadtime,1,10),1)<YEARWEEK(CURDATE(),1),B.Qty-B.scQty,0)) AS OverQty 
               FROM (
					 SELECT A.POrderId,A.Qty,IFNULL(L.Qty,0) AS scQty   ,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime 
						       FROM (
										  SELECT S0.POrderId,S0.Qty,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty  
										   FROM (      
											           SELECT S.POrderId,S.Qty,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
														FROM $DataIn.yw1_ordermain M
														LEFT JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
														LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
														LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
														LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
														LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
														LEFT JOIN $DataIn.stuffproperty T  ON T.StuffId=G.StuffId AND  T.Property='8'
				                        WHERE 1     AND S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  AND T.StuffId is NULL 
														
																												GROUP BY G.StockId 
										 )S0 
										 GROUP BY S0.POrderId 
						 )A 
						LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
						LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
						LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
 LEFT JOIN (
									 SELECT S.POrderId,SUM(L.Qty) AS Qty 
									 FROM $DataIn.yw1_ordersheet S 
									 LEFT JOIN $DataIn.sc1_cjtj L ON S.POrderId=L.POrderId
									 WHERE  S.scFrom>0 AND S.Estate=1 and L.TypeId=7100 GROUP BY S.POrderId 
						         ) L ON A.POrderId=L.POrderId  
						WHERE A.blQty=A.llQty  AND EXISTS (
						      SELECT ST.mainType 
						       FROM $DataIn.cg1_stocksheet G 
						       LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						       LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						       WHERE G.POrderId=A.POrderId AND ST.mainType=3)
						GROUP BY A.POrderId 
                      )B ";
	    	         /*
					 $checkSql="
	                    SELECT SUM(IF(YEARWEEK(substring(B.Leadtime,1,10),1)<YEARWEEK(CURDATE(),1),B.Qty-B.scQty,0)) AS OverQty 
                        FROM (
                              SELECT A.POrderId,S.Qty,PI.Leadtime,IFNULL(L.Qty,0) AS scQty 
                               FROM (
									SELECT S1.* FROM (
										          SELECT S0.POrderId,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty  FROM (      
										             SELECT 
																S.POrderId,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
										                        FROM $DataIn.yw1_ordermain M
																LEFT JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
																LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
										                        LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
																LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
										                        LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
										                        LEFT JOIN  $DataIn.stuffproperty T ON T.StuffId=G.StuffId AND T.Property='8'
										                        WHERE 1 AND S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  AND T.StuffId IS NULL 
										                      GROUP BY G.StockId 
										               )S0 GROUP BY S0.POrderId 
										     )S1 WHERE S1.blQty=S1.llQty 
									) A 
								 LEFT JOIN  $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId  
                                 LEFT JOIN  $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
                                 LEFT JOIN $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId 
                                  LEFT JOIN (
									 SELECT S.POrderId,SUM(L.Qty) AS Qty 
									 FROM $DataIn.yw1_ordersheet S 
									 LEFT JOIN $DataIn.sc1_cjtj L ON S.POrderId=L.POrderId
									 WHERE  S.scFrom>0 AND S.Estate=1 and L.TypeId=7100 GROUP BY S.POrderId 
						         ) L ON A.POrderId=L.POrderId 
                                 LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId  
                                 LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
								WHERE   ST.mainType=3   GROUP BY A.POrderId 
                      )B";//AND L.Estate=0 */
                      //echo $checkSql;
                     $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
                     $badgenums7=$checkRow["OverQty"]==""?0:round($checkRow["OverQty"]/1000); 
                     /*
					 SELECT SUM(B.Qty) AS blQty,SUM(B.ScedQty) AS ScedQty 
               FROM (
					 SELECT A.POrderId,A.Qty,SUM(L.Qty) AS ScedQty,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime    
						       FROM (
										  SELECT S0.POrderId,S0.Qty,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty  
										   FROM (      
											           SELECT S.POrderId,S.Qty,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
														FROM $DataIn.yw1_ordermain M
														LEFT JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
														LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
														LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
														LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
														LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
														LEFT JOIN $DataIn.stuffproperty T  ON T.StuffId=G.StuffId AND  T.Property='8'
				                        WHERE 1     AND S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  AND T.StuffId is NULL 
														
																												GROUP BY G.StockId 
										 )S0 
										LEFT JOIN  d7.sc1_mission SC on SC.POrderId=S0.POrderId
										where SC.Id is not null
										 GROUP BY S0.POrderId 
						 )A 
						LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
						LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
						LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
						LEFT JOIN $DataIn.sc1_cjtj L ON L.POrderId=S.POrderId AND L.TypeId='7100'  
						WHERE A.blQty=A.llQty  AND EXISTS (
						      SELECT ST.mainType 
						       FROM $DataIn.cg1_stocksheet G 
						       LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						       LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						       WHERE G.POrderId=A.POrderId AND ST.mainType=3)
						GROUP BY A.POrderId 
                      )B 
					 */
                     $Result213=mysql_fetch_array(mysql_query("SELECT SUM(B.Qty) AS blQty,SUM(B.ScedQty) AS ScedQty  ,SUM(IF(YEARWEEK(substring(B.Leadtime,1,10),1)<YEARWEEK(CURDATE(),1),B.Qty,0)) AS OverQty 
               FROM (
					 SELECT A.POrderId,A.Qty,SUM(L.Qty) AS ScedQty   ,IFNULL(PI.Leadtime,PL.Leadtime) AS Leadtime 
						       FROM (
										  SELECT S0.POrderId,S0.Qty,SUM(S0.OrderQty) AS blQty,SUM(S0.llQty) AS llQty  
										   FROM (      
											           SELECT S.POrderId,S.Qty,G.StockId,G.OrderQty,IFNULL(SUM(L.Qty),0) AS llQty  
														FROM $DataIn.yw1_ordermain M
														LEFT JOIN  $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber
														LEFT JOIN  $DataIn.cg1_stocksheet G ON G.POrderId=S.POrderId
														LEFT JOIN  $DataIn.stuffdata D ON D.StuffId=G.StuffId 
														LEFT JOIN  $DataIn.stufftype ST ON ST.TypeId=D.TypeId
														LEFT JOIN  $DataIn.ck5_llsheet L ON L.StockId=G.StockId 
														LEFT JOIN $DataIn.stuffproperty T  ON T.StuffId=G.StuffId AND  T.Property='8'
				                        WHERE 1     AND S.scFrom>0 AND S.Estate=1 AND ST.mainType<2  AND T.StuffId is NULL 
														
																												GROUP BY G.StockId 
										 )S0 
										 GROUP BY S0.POrderId 
						 )A 
						LEFT JOIN $DataIn.yw1_ordersheet S ON S.POrderId=A.POrderId 
						LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id  
						LEFT JOIN $DataIn.yw3_pileadtime PL ON PL.POrderId=S.POrderId  
						LEFT JOIN $DataIn.sc1_cjtj L ON L.POrderId=S.POrderId AND L.TypeId='7100'  
						WHERE A.blQty=A.llQty  AND EXISTS (
						      SELECT ST.mainType 
						       FROM $DataIn.cg1_stocksheet G 
						       LEFT JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId 
						       LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=D.TypeId
						       WHERE G.POrderId=A.POrderId AND ST.mainType=3)
						GROUP BY A.POrderId 
                      )B ",$link_id));
                      $iPhone_C213=$Result213["blQty"];
                     $noScQty=round(($iPhone_C213-$Result213["ScedQty"])/1000);
                     $noScQty=$noScQty>0?$noScQty . "k":"";	
					 
					// $badgenums7=$Result213["OverQty"]==""?0:round($Result213["OverQty"]/1000); 
                     $jsonArray[] = array("Id"=>"108", "Name"=>"包装","Count"=>"$badgenums7","ColorSign"=>"2","LeftBadge"=>"$noScQty"); 
        }
        
        if (in_array("106", $itemArray)) {
             $checkRow=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums  FROM $DataIn.productdata  WHERE Estate=1",$link_id));
             $badgenums=$checkRow["Nums"]==""?0:$checkRow["Nums"];
             $jsonArray[] = array("Id"=>"124", "Name"=>"产品列表","Count"=>"$badgenums","ColorSign"=>"3"); 
		 }	
        
		//1077
		 if (in_array("1077",$modelArray) || in_array("1077",$itemArray)) {
             $checkRow=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums  FROM $DataIn.stuffdata  WHERE Estate>0",$link_id));
             $badgenums=$checkRow["Nums"]==""?0:$checkRow["Nums"];
             $jsonArray[] = array("Id"=>"128", "Name"=>"配件列表","Count"=>"$badgenums","ColorSign"=>"3"); 
		 }	
        
		        
        if (in_array("106", $itemArray) || $LoginNumber==11965) {
             $checkRow=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums  FROM $DataSub.productdata  WHERE Estate=1",$link_id));
             $badgenums=$checkRow["Nums"]==""?0:$checkRow["Nums"];
             $jsonArray[] = array("Id"=>"224", "Name"=>"产品列表","Count"=>"$badgenums","ColorSign"=>"3"); 
		 }	
        
		//1077
		 if (in_array("108",$itemArray) || $LoginNumber==11965) {
             $checkRow=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS Nums  FROM $DataSub.stuffdata  WHERE Estate>0",$link_id));
             $badgenums=$checkRow["Nums"]==""?0:$checkRow["Nums"];
             $jsonArray[] = array("Id"=>"228", "Name"=>"配件列表","Count"=>"$badgenums","ColorSign"=>"3"); 
		 }	
        

        if (in_array("128",$itemArray) || in_array("101",$itemArray)){
             //6、业务处理 
	        $checkSql="SELECT SUM(S.Qty) AS OverQty 
					FROM $DataIn.yw1_ordersheet S 
					LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id 
					WHERE S.Estate>0  AND YEARWEEK(substring(PI.Leadtime,1,10),1)<YEARWEEK(CURDATE(),1) AND Year(substring(PI.Leadtime,1,10))>0  ";
            $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
            $badgenums=$checkRow["OverQty"]==""?0:round($checkRow["OverQty"]/1000);  
            $jsonArray[] = array("Id"=>"118", "Name"=>"业务","Count"=>"$badgenums","ColorSign"=>"2");
            
            //7、开发处理 
            $dpArray=array(); $dpArray[501]=0;$dpArray[502]=0;$dpArray[503]=0;$dpArray[102]=0;
            $checkResult=mysql_query("SELECT A.GroupId,Count(*) AS overCount
					FROM  $DataIn.stuffdevelop A
					LEFT JOIN  $DataIn.stuffdata S  ON S.StuffId=A.StuffId 
					WHERE  A.Estate=1 AND S.DevelopState=1 AND YEARWEEK(A.Targetdate,1) < YEARWEEK(CURDATE(),1) GROUP BY A.GroupId ",$link_id);
		      if($checkRow = mysql_fetch_array($checkResult)){
		         do{
				       $dGroupId=$checkRow["GroupId"];
				       $dpArray[$dGroupId]=$checkRow["overCount"];
			       }while($checkRow = mysql_fetch_array($checkResult));
			        $developText=$dpArray[501] . "/" . $dpArray[502] . "/" . $dpArray[503] . "/" . $dpArray[102];
		     }
		     else{
			      $developText=" ";
		     }

             $jsonArray[] = array("Id"=>"120", "Name"=>"开发","Count"=>"0","Text"=>"$developText","ColorSign"=>"0");
        }
        
        //BOM采购
        if (in_array("1006",$modelArray)){
	        $checkSql="SELECT SUM(IF( YEARWEEK(S.DeliveryDate,1)<YEARWEEK(CURDATE(),1),(A.Qty-A.rkQty)*S.Price*D.Rate,0)) AS OverAmount, 
	         SUM(IF( YEARWEEK(S.DeliveryDate,1)=YEARWEEK(CURDATE(),1),(A.Qty-A.rkQty)*S.Price*D.Rate,0)) AS Amount 
					     FROM (
									    SELECT S.StockId,(S.FactualQty+S.AddQty) AS Qty,SUM(IFNULL(R.Qty,0)) AS rkQty  
									          FROM $DataIn.cg1_stocksheet S 
									          LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid 
									          LEFT JOIN $DataIn.ck1_rksheet R ON R.StockId=S.StockId
									         WHERE  S.Mid>0 AND  S.rkSign>0 GROUP BY S.StockId
									)A 
						LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=A.StockId   
						LEFT JOIN $DataIn.cg1_stockmain M ON M.Id=S.Mid  
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=M.CompanyId  
						LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency  
						WHERE A.Qty>A.rkQty AND YEARWEEK(S.DeliveryDate,1)<=YEARWEEK(CURDATE(),1) AND S.DeliveryDate<>'0000-00-00' ";
            $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
            $badgenums107=$checkRow["OverAmount"]==""?0:round($checkRow["OverAmount"]/10000);  
            $cgTotalAmount=$checkRow["Amount"]==""?0:round($checkRow["Amount"]/10000);  
            /*
              $checkSql="SELECT SUM((S.FactualQty+S.AddQty)*S.Price*D.Rate) AS Amount 
						FROM $DataIn.cg1_stocksheet S 
						LEFT JOIN $DataIn.trade_object P ON P.CompanyId=S.CompanyId  
						LEFT JOIN $DataPublic.currencydata D ON D.Id=P.Currency  
						WHERE S.rkSign>0 AND YEARWEEK(S.DeliveryDate,1) = YEARWEEK(CURDATE(),1)";
            $checkRow=mysql_fetch_array(mysql_query($checkSql,$link_id));
            $cgTotalAmount=$checkRow["Amount"]==""?0:round($checkRow["Amount"]/10000);  
            */
            
            $jsonArray[] = array("Id"=>"107", "Name"=>"采购","Count"=>"$badgenums107","ColorSign"=>"1","LeftBadge"=>"$cgTotalAmount");
	    }
	    
	       //差最后一个配件
	   if (in_array("1013",$modelArray) || in_array("1006",$modelArray)){
		          $LastQty=0;
		          $FromPageName="sh";
			     $LastBlResult=mysql_query("SELECT S.StuffId,S.StockId,S.Qty  FROM $DataIn.gys_shsheet S  
			     WHERE  S.Estate=1  AND S.SendSign=0",$link_id);
			     while ($LastBlRow = mysql_fetch_array($LastBlResult)){
			            $StuffId=$LastBlRow["StuffId"];
			            $StockId=$LastBlRow["StockId"];
			            $POrderId=substr($StockId,0,12);
			            $Qty=$LastBlRow["Qty"];
			            include "../../model/subprogram/stuff_blcheck.php";
			            if ($LastBlSign==1) $LastQty+=$Qty;
			       }
			       
			       $LastBlResult=mysql_query("SELECT S.StuffId,S.StockId,S.Qty  FROM $DataIn.gys_shsheet S  
			       LEFT JOIN $DataIn.cg1_lockstock GL ON S.StockId=GL.StockId  AND GL.Locks=0 
			      WHERE  S.Estate=2  AND S.SendSign=0  AND NOT EXISTS(SELECT L.Id FROM $DataIn.qc_mission L WHERE L.Sid=S.Id )",$link_id);
			     while ($LastBlRow = mysql_fetch_array($LastBlResult)){
			            $StuffId=$LastBlRow["StuffId"];
			            $StockId=$LastBlRow["StockId"];
			            $POrderId=substr($StockId,0,12);
			            $Qty=$LastBlRow["Qty"];
			            include "../../model/subprogram/stuff_blcheck.php";
			            if ($LastBlSign==1) $LastQty+=$Qty;
			       }

			    $LastQty=round($LastQty/1000);
			     $badgenums133=$LastQty>0?$LastQty. "k":0;
			     
			     $jsonArray[] = array("Id"=>"133", "Name"=>"仓库","Count"=>"$badgenums133","ColorSign"=>"4");
    }
    
    //开发
    if (in_array("128",$itemArray) || in_array("101",$itemArray)){
          /*
            $developResult =mysql_query("SELECT T.DevelopNumber,SUM(IF(A.Type=0,1,0)) AS Counts,SUM(IF(YEARWEEK(A.Targetdate,1)<YEARWEEK(CURDATE(),1),1,0)) AS OverCounts 
	                 FROM stuffdevelop  A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
                     INNER JOIN  stufftype T  ON T.TypeId=S.TypeId 
					 WHERE A.Estate>0   AND S.DevelopState=1  GROUP BY T.DevelopNumber ",$link_id);
			 */
			 $developResult =mysql_query("SELECT A.DevelopNumber,SUM(IFNULL(A.Counts,0)) AS Counts,SUM(IFNULL(A.OverCounts,0)) AS OverCounts 
FROM (
 SELECT T.DevelopNumber,SUM(IF(A.Type=0,1,0)) AS Counts,SUM(IF(YEARWEEK(A.Targetdate,1)<YEARWEEK(CURDATE(),1),1,0)) AS OverCounts 
	                 FROM stuffdevelop  A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId 
                     INNER JOIN  stufftype T  ON T.TypeId=S.TypeId 
					 WHERE A.Estate>0   AND S.DevelopState=1  GROUP BY T.DevelopNumber
UNION ALL 
SELECT A.ProjectsNumber AS DevelopNumber,SUM(IF(A.Type=0,1,0)) AS Counts,SUM(IF(YEARWEEK(A.Targetdate,1)<YEARWEEK(CURDATE(),1),1,0)) AS OverCounts 
	                 FROM stuffdevelop  A
                     INNER JOIN  stuffdata S  ON S.StuffId=A.StuffId  
					 WHERE A.Estate>0  AND S.DevelopState=1  AND A.ProjectsNumber>0 GROUP BY  A.ProjectsNumber
)A GROUP BY A.DevelopNumber",$link_id);
		   while ($developRow = mysql_fetch_array($developResult)){
			            $DevelopNumber=$developRow["DevelopNumber"];
			            $Counts=$developRow["Counts"]==0?"":$developRow["Counts"];
			            $OverCounts=$developRow["OverCounts"]==0?"":$developRow["OverCounts"];
			            
			             $jsonArray[] = array("Id"=>"$DevelopNumber", "Name"=>"开发","Count"=>"$OverCounts","ColorSign"=>"1","LeftBadge"=>"$Counts");
	     }

    }
       //已分开读取
	     // include "badge_pt_read.php";
      
       //$badgenums2+=$badgenums202;
      
      
?>