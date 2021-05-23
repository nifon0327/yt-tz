<?php   
          //************************************************************************************************最后一个配件入库时间(物料周期)
          //下单后到最后一次备料
          $Temp_today=date("Y-m-d");
          $Temp_time=date("Y-m-d H:i:s");
           $Temp_yestoday=date("Y-m-d",strtotime('-1 day'));
           $rk_dateArray=array();
           $Cg_cycle="&nbsp;";
           $Cg_title="&nbsp;";
           $default_bldays=2;
           $default_scdays=2;
           $temp_k=0; $k=0;$temp_count=0;

            //************************************************************************************************ 需备料数
           $CheckblQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS TotalblQty,COUNT(*) AS blNum
				FROM $DataIn.cg1_stocksheet G
				INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND G.blsign =1 and G.level=1",$link_id));
			$TotalblQty=$CheckblQty["TotalblQty"];
            $TotalblNum=$CheckblQty["blNum"];
           //已备料数
            $CheckllQty=mysql_fetch_array(mysql_query("SELECT SUM(K.Qty) AS TotalllQty
				FROM	$DataIn.cg1_stocksheet G 							
				LEFT JOIN   $DataIn.ck5_llsheet K	ON K.StockId = G.StockId 
				INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
                INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
				WHERE G.POrderId='$POrderId' AND G.blsign =1 and G.level=1
                ",$link_id));
             $blSign=1;
             $TotalllQty=$CheckllQty["TotalllQty"];
             if($TotalblQty>$TotalllQty){//领料未完毕
                 $blSign=0;
				}
          //需采购配件的数量
          $CgNum_Result=mysql_fetch_array(mysql_query("SELECT COUNT(*) AS CgNum
           FROM $DataIn.cg1_stocksheet G
           INNER JOIN $DataIn.stuffdata D ON D.StuffId = G.StuffId
           INNER JOIN $DataIn.stufftype ST ON ST.TypeId = D.TypeId
           WHERE 1 AND G.blsign =1 and G.level=1 and (G.FactualQty>0 OR G.AddQty>0) and  G.POrderId='$POrderId'",$link_id));
           $CgNum=$CgNum_Result["CgNum"];//>0有采购，=0 使用库存
          //未备料完的订单检查库存是否足够,足够的话记住可备料的时间点。不够的话，备料时间为0，物料时间为下单到现在
            $Temp_Sign=0;
            if($blSign==0){
                  $Temp_num=0;$llSign=0;
                  $CheckResult=mysql_query("SELECT G.OrderQty,K.tStockQty,SUM(IFNULL(L.Qty,0)) AS llQty
				                 FROM $DataIn.cg1_stocksheet G
                                 INNER JOIN $DataIn.ck9_stocksheet K ON K.StuffId=G.StuffId
                                 LEFT JOIN $DataIn.ck5_llsheet L ON L.StockId=G.StockId
				                 INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				                 INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				                WHERE G.POrderId='$POrderId' AND G.blsign =1 and G.level=1 GROUP BY G.StockId",$link_id);
	                     if($CheckRow=mysql_fetch_array($CheckResult)){
                                do{
                                      $OrderQty=$CheckRow["OrderQty"];
                                      $tStockQty=$CheckRow["tStockQty"];
                                      $llQty=$CheckRow["llQty"];
                                      if($tStockQty<=0)$llSign++;
                                      if($tStockQty>=$OrderQty-$llQty)$Temp_num++;
                                     }while($CheckRow=mysql_fetch_array($CheckResult));
                               }
                     if($TotalblNum==$Temp_num)$Temp_Sign=1;
                 }
 //echo $Temp_Sign;
 //************************************************************************************************
           $wl_cycle="&nbsp;";
           $bl_cycle="&nbsp;";
           $bl_cycleIpad = "";
           $noblState=0;
           $wl_dateArray=array();$k=0;
           $wl_dateResult=mysql_query("SELECT K.Date 
           FROM $DataIn.cg1_stocksheet S
           LEFT JOIN $DataIn.ck5_llsheet  K ON K.StockId=S.StockId
           INNER JOIN $DataIn.stuffdata D ON D.StuffId = S.StuffId
           INNER JOIN $DataIn.stufftype ST ON ST.TypeId = D.TypeId
           WHERE 1  AND S.blsign =1 and S.level=1  AND S.POrderId =  '$POrderId'  
           GROUP BY K.Date   ORDER BY K.Date  DESC",$link_id);
          if($wl_Row=mysql_fetch_array($wl_dateResult)){
	            do{    
	                    if($wl_Row["Date"]!=""){
	                          $wl_dateArray[$k]=substr($wl_Row["Date"],0,10);
	                          $k++;
	                        }
	                }while($wl_Row=mysql_fetch_array($wl_dateResult));
               }
            //************************************************************************************************插入可备料点时间入表ck_bldate
          $ck_ableResult=mysql_query("SELECT * FROM $DataIn.ck_bldate WHERE POrderId='$POrderId'",$link_id);
          if($ck_albeRow=mysql_fetch_array($ck_ableResult)){
                $ableDate=$ck_albeRow["ableDate"];
                if($Temp_Sign==0 && $blSign==0){//一旦发现配件库存被其他单挪用，则更新表中的ableDate 表示可备料时间点失去，为不可备料
                         $UpdateSql="UPDATE  $DataIn.ck_bldate  SET  ableDate='0000-00-00',unableDate='$ableDate'  WHERE POrderId='$POrderId'";
                          $UpdateResult=mysql_query($UpdateSql);
                         }
                  if($Temp_Sign==1 && $blSign==0 && $ableDate=="0000-00-00"){//库存足够，没备完，而且可备料点为0
                         $UpdateSql="UPDATE  $DataIn.ck_bldate  SET  ableDate='$Temp_today'  WHERE POrderId='$POrderId'";
                          $UpdateResult=mysql_query($UpdateSql);
                         }
                 }
           else{
                  if($Temp_Sign==1){
                       $In_bldateSql="INSERT INTO $DataIn.ck_bldate(Id, POrderId, ableDate, unableDate, Estate, Locks) VALUES(NULL,'$POrderId','$Temp_yestoday','0000-00-00',1,0)";
                       $In_Result=mysql_query($In_bldateSql);
                        }
                     if($blSign==1 && $wl_dateArray[0]!=""){
                           $Temp_yestoday=date("Y-m-d",strtotime($wl_dateArray[0]));
                           $In_bldateSql="INSERT INTO $DataIn.ck_bldate(Id, POrderId, ableDate, unableDate, Estate, Locks) VALUES(NULL,'$POrderId','$Temp_yestoday','0000-00-00',1,0)";
                          $In_Result=mysql_query($In_bldateSql);
                          }
                     $ableDate="0000-00-00";
                }
           //*****************************************************************************************全部备料才开始生产(备料周期)      
             if($ableDate!="0000-00-00" && $ableDate!=""){//说明可备料或者是已备料
                          $wl_cycle=ceil((strtotime($ableDate)-strtotime($OrderDate))/3600/24);
                          if($blSign==1)$bl_cycle=ceil((strtotime($wl_dateArray[0])-strtotime($ableDate))/3600/24);
                          else $bl_cycle=ceil((strtotime($Temp_today)-strtotime($ableDate))/3600/24);
                          $iPhone_bldays=$bl_cycle;//iPhone专用
                          $bl_cycleIpad = $bl_cycle;//iPad专用
                          if($bl_cycle>=2)$bl_cycle="<span class='redB'>$bl_cycle</span>"."days";
                          else $bl_cycle=$bl_cycle."days";
                    }
             else{
                    $wl_cycle=ceil((strtotime($Temp_today)-strtotime($OrderDate))/3600/24);
                     $noblState=1;
                    $bl_cycle="&nbsp;";
                    }
          //**************************************************************************************（开发周期）
      $kf_cycle=0;
       $unLock_Result=mysql_fetch_array(mysql_query("SELECT Date FROM $DataIn.yw2_orderunlock WHERE POrderId='$POrderId' AND Type=2",$link_id));
      $unLock_Date=$unLock_Result["Date"];
      if($unLock_Date!=""){
             $kf_cycle=ceil((strtotime($unLock_Date)-strtotime($OrderDate))/3600/24);//开发周期为下订单到解锁的那天
     }
           $wl_cycle=$wl_cycle-$kf_cycle;
           $iPhone_wldays=$wl_cycle;
           $ipad_wlCycle = $wl_cycle."days";
          if($wl_cycle>=25)$wl_cycle="<span class='redB'>$wl_cycle</span>"."days";
          else $wl_cycle=$wl_cycle."days";
     
            //************************************************************************************************加工周期(待出周期)
              $sc_cycle="&nbsp;";
              $sctj_date="&nbsp;";
              $sc_Sign=0;
              if($k>0 && $blSign==1){
                   //工序总数
				$CheckgxQty=mysql_fetch_array(mysql_query("SELECT SUM(G.OrderQty) AS gxQty 
				FROM $DataIn.cg1_stocksheet G
				INNER JOIN $DataIn.stuffdata D ON D.StuffId=G.StuffId
				INNER JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId 
				WHERE G.POrderId='$POrderId' AND T.mainType=3",$link_id));
				$gxQty=$CheckgxQty["gxQty"];
				//已完成的工序数量
				$CheckscQty=mysql_fetch_array(mysql_query("SELECT SUM(C.Qty) AS scQty,Max(C.Date) AS Date FROM $DataIn.sc1_cjtj C WHERE C.POrderId='$POrderId'",$link_id));
				$scQty=$CheckscQty["scQty"];
				$MaxScDate=$CheckscQty["Date"];
				
				if($gxQty==$scQty)$sc_Sign=1;
				
				if ($gxQty==0) $MaxScDate=$wl_dateArray[0];
				$sctj_date=$MaxScDate==""?0:ceil((strtotime($Temp_today)-strtotime($MaxScDate))/3600/24);        
                 $iPhone_schours =$sc_Sign==1?ceil((strtotime($MaxScDate)-strtotime($wl_dateArray[0]))/3600):0;
				if ($scQty>0)  {
				       $iPhone_scqtys=$scQty;
				       $iPhone_sccolors=$gxQty==$scQty?1:2;
				       $iPhone_schours=$iPhone_schours==0?1:$iPhone_schours;
				  }
				 
				


                 $ship_dateResult=mysql_fetch_array(mysql_query("SELECT  M.Date FROM $DataIn.ch1_shipsheet S  
                                             LEFT JOIN $DataIn.ch1_shipmain M ON M.Id=S.Mid
                                             WHERE S.POrderId='$POrderId'",$link_id));
                  $ship_date=$ship_dateResult["Date"];
                  if($ship_date!=""){//已出货订单
                         //echo $wl_dateArray[0];
                          $sctj_date=ceil((strtotime($MaxScDate)-strtotime($wl_dateArray[0]))/3600/24);
                          $ipad_sctjDate = $sctj_date;
                          $sc_cycle=ceil((strtotime($ship_date)-strtotime($wl_dateArray[0]))/3600/24);//已出货加工周期为出货时间－最后备料时间。
                          $iPhone_shipcolors=1;
                           }
                   else{
	                       $sc_cycle=ceil((strtotime($Temp_today)-strtotime($wl_dateArray[0]))/3600/24);// 未出货加工周期为当前天－最后备料时间。
                          }
                          
                          $iPhone_scdays=$sc_cycle;//iPhone专用
                         
                         if(($sctj_date==0) ||($sctj_date!=0 &&$sc_Sign==0)){
                              if($sc_cycle>=2)$sc_cycle="<span class='redB'>$sc_cycle</span>"."days";
                              else $sc_cycle=$sc_cycle."days";
                              $ipad_sctjDate = "";
                              $sctj_date="&nbsp;";
                             }
                       else{
                                $sc_cycle=$sc_cycle-$sctj_date;
                               if($sc_cycle>=2)$sc_cycle="<span class='redB'>$sc_cycle</span>"."days";
                                else $sc_cycle=$sc_cycle."days";
                              if($sctj_date>=5)$sctj_date="<span class='redB'>$sctj_date</span>"."days";
                                  else $sctj_date=$sctj_date."days";
                             }
                 }
                 if ($blSign==1) $theDefaultColor="#D3E9D3";//#E9FFF5
 //************************************************************************************************
?>