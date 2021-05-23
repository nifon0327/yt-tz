
<?php   
//传入参数：$StuffId、$OrderQty 电信---yang 20120801

include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";


header("Content-Type: text/html; charset=utf-8;");
header("expires:mon,26jul199705:00:00gmt;");
header("cache-control:no-cache,must-revalidate;");
header("pragma:no-cache;");
$dataArray=explode("|",$args);
//echo $args;
$StockIdList="";
$OrderQtyList="";
$tableWidth=810;
if (strlen($dataArray[0])>10){
	$mStockId=$dataArray[0];
	$tableWidth=1160;
	$CgDateList="<td width='80' align='center'>采购日期</td>";
	$StockIdList="<td width='100' align='center'>采购流水号</td>";
	$OrderQtyList="<td width='60' align='center'>需求数量</td>";
	$tStockQtyList="<td width='60' align='center'>在库</td>";
	$FactualQtyList="<td width='60' align='center'>需购数量</td>";
	$rkQtyList="<td width='60' align='center'>入库数量</td>";
	$MantissaQtyList="<td width='60' align='center'>欠数</td>";
	$blQtyList="<td width='60' align='center'>已备料数</td>";
	$deliveryWeekList="<td width='60' align='center'>交期</td>";
	$cutNumList ="<td width='60'  align='center'>刀模编号</td>";
	$cutDrawingList = "<td width='60'  align='center'>刀模图档</td>";
	$colspan=22;
}
else{
    $mStuffId=$dataArray[0];
    $colspan=15; 
	$cutNumList ="<td width='60'  align='center'>刀模编号</td>";
	$cutDrawingList = "<td width='60'  align='center'>刀模图档</td>";
}


$tempk  = $dataArray[1];
$FlowSign=$dataArray[2];
if ($FlowSign==1){
    $margin_left="60px";
	include "../admin/semifinished_scsheet_ajax.php";
	echo "<br><div style='height:50px;'>&nbsp;</div>";
	include "../admin/yw_stockadd_m_ajax.php";  //需求单异动增加未审核的配件
}
//echo $mStockId;
$TableId="ListSubTB2".$RowId;
$tId=1;
echo"<table  cellspacing='1' border='1'  align='left' style='margin-left:60px;'><tr bgcolor='#CCCCCC'>
		 <td width='20'  align='center'>&nbsp;</td>
		 <td width='20'  align='center'>&nbsp;</td>
		 <td width='25'  align='center'>NO.</td>
		 $CgDateList
		 $StockIdList
         <td width='50'  align='center'>配件ID</td>
		 <td width='350' align='center'>配件名称</td>
		 <td width='40'  align='center'>图档</td>
		 <td width='50'  align='center'>历史订单</td>
         <td width='40'  align='center'>单位</td>
         <td width='60'  align='center'>单价</td>
         $cutNumList
         $cutDrawingList
         <td width='60'  align='center'>对应关系</td>
         $OrderQtyList
         $tStockQtyList
         $blQtyList
         $deliveryWeekList
		 <td width='60'  align='center'>采购</td>
		 <td width='80' align='center'>供应商</td>
		 <td width='70' align='center'>存放楼层</td>
		</tr>";
        //从配件表和配件关系表中提取配件数据	  
        if ($mStockId>0){
	        $StuffSql="SELECT A.Id,A.Relation,A.StockId,A.OrderQty,S.FactualQty,S.AddQty,
	        S.Mid,GM.Date AS cgDate,A.mStuffId,A.StuffId,D.StuffCname,S.DeliveryWeek,T.mainType,
	        D.Picture,D.Gfile,D.Gstate,S.Price AS Price, D.Gremark,D.TypeId,D.SendFloor,
	        U.Name AS Unit,T.mainType,M.TypeColor,M.blSign,M.SortId,SM.Name,O.Forshort,K.tStockQty     
			FROM  $DataIn.cg1_semifinished A  
			LEFT JOIN $DataIn.cg1_stocksheet S  ON S.StockId=A.StockId  
			LEFT JOIN $DataIn.cg1_stockmain GM ON GM.Id = S.Mid
            LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.StuffId 
            LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
	        LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
	        LEFT JOIN $DataIn.stuffmaintype M ON M.Id=T.mainType
	        LEFT JOIN $DataIn.staffmain SM ON SM.Number=S.BuyerId
			LEFT JOIN $DataIn.trade_object O ON O.CompanyId=S.CompanyId
			LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId = D.StuffId
            WHERE A.mStockId='$mStockId' GROUP BY S.StockId 
	        ORDER BY SortId,Id";
         }
        else{
		    $StuffSql="SELECT A.Id,A.Relation,A.StuffId,D.StuffCname,D.Picture,D.Gfile,D.Gstate,
            D.Price,D.Gremark,D.TypeId,D.SendFloor,U.Name AS Unit,T.mainType,M.TypeColor,
            M.blSign,M.SortId,SM.Name,O.Forshort,T.mainType,A.mStuffId   
			FROM  $DataIn.semifinished_bom A  
            LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.StuffId 
            LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
	        LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
	        LEFT JOIN $DataIn.stuffmaintype M ON M.Id=T.mainType
	        LEFT JOIN $DataIn.bps B  ON B.StuffId=A.StuffId 
	        LEFT JOIN $DataIn.staffmain SM ON SM.Number=B.BuyerId
			LEFT JOIN $DataIn.trade_object O ON O.CompanyId=B.CompanyId
            WHERE A.mStuffId='$mStuffId' AND A.StuffId>0 
	        ORDER BY SortId,Id";
	      }
			$StuffResult = mysql_query($StuffSql,$link_id);
			$k=1;
			//echo $StuffSql;
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
			    $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			     $dt=anmaIn("download/cut_data/",$SinkOrder,$motherSTR);
			     $dw=anmaIn("download/cut_drawing/",$SinkOrder,$motherSTR);
				do{	
					$n=$m;
					$PandsId=$StuffMyrow["Id"];
					$StuffId=$StuffMyrow["StuffId"];
					$mStuffId=$StuffMyrow["mStuffId"];
					$StuffCname=$StuffMyrow["StuffCname"];
					$TypeId=$StuffMyrow["TypeId"];
					$Price=$StuffMyrow["Price"];
                    $Unit=$StuffMyrow["Unit"]==""?"&nbsp;":$StuffMyrow["Unit"];
                    $mainType=$StuffMyrow["mainType"];
					$Relation=$StuffMyrow["Relation"];
		            $blSign=$StuffMyrow["blSign"];
				    $Name=$StuffMyrow["Name"];
				    $Forshort=$StuffMyrow["Forshort"];
                    $TypeColor=$StuffMyrow["TypeColor"];
					$Picture=$StuffMyrow["Picture"];
					$Gfile=$StuffMyrow["Gfile"];
					$Gstate=$StuffMyrow["Gstate"]; 
					$Gremark=$StuffMyrow["Gremark"];
					include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
					//检查是否有图片
					include "../model/subprogram/stuffimg_model.php";
					include "../model/subprogram/stuff_Property.php";	//属性显示
					$SendFloor=$StuffMyrow["SendFloor"];
					include "../model/subprogram/stuff_GetFloor.php";
					$FloorName=$FloorName=""?"&nbsp;":$FloorName;
					$OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=$thisId' target='_blank'>查看</a>";
					
					
						
				    $showProcess="&nbsp;";$ProcessTable="";
				    if ($mStockId>0){
				        $StockId=$StuffMyrow["StockId"];
				        $OrderQty=$StuffMyrow["OrderQty"];
				        $tStockQty=$StuffMyrow["tStockQty"];
				        $FactualQty=$StuffMyrow["FactualQty"];
				        $AddQty=$StuffMyrow["AddQty"];
				        $sPOrderId=$StuffMyrow["sPOrderId"];
				        $cgMid=$StuffMyrow["Mid"];
				        $cgDate =$StuffMyrow["cgDate"];
				        $DeliveryWeek =$StuffMyrow["DeliveryWeek"];
				        //echo $StockId .'/' . $DeliveryWeek;
				        $cgQty = $FactualQty + $AddQty;
					    $CheckProcessSql="SELECT A.Id FROM $DataIn.cg1_semifinished A  
					    WHERE  A.mStockId='$StockId' LIMIT 1";
					    $CheckProcessResult=mysql_query($CheckProcessSql,$link_id);
			            if($CheckProcessRow=mysql_fetch_array($CheckProcessResult)){
			                $ListId=getRandIndex();
			               
			                $semibomTableId = "semibomTable_$ListId";
			                $showtableId = "showtable_$ListId";
			                $semibomDivId = "semibomDiv_$ListId";
			              
	                        $showProcess="<img onClick='ShowDropTable($semibomTableId,$showtableId,$semibomDivId,\"semifinishedbom_ajax\",\"$StockId|$ListId\",\"admin\");'  src='../images/showtable.gif'  title='显示或隐藏多级BOM资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='$showtableId'>";
						    $ProcessTable="<tr id='$semibomTableId' style='display:none;background:#83b6b9;'><td colspan='$colspan'><div id='$semibomDivId' width='650'></div></td></tr>"; 
						       
			             }
			             
			             $MantissaColor ="";
			             
			             if($blSign==1){
			                     if($FactualQty==0 && $AddQty==0){
									  $cgDate="使用库存";
									  $rkQty="-";$Mantissa="-";  
									  $DeliveryWeek ="-";
						           }
						           else{
							           if ($cgMid==0){ //未下采购单
							             	
								           $cgDate="未下采购单";
								           $rkQty="-";$Mantissa="-";
							           }
							           else{
								         
										  //收货情况				
										  $rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty 
										  FROM $DataIn.ck1_rksheet where StockId='$StockId' 
										  order by StockId",$link_id);
										  $rkQty=mysql_result($rkTemp,0,"Qty");
										  $Mantissa = $cgQty - $rkQty;
										  $rkQty=$rkQty==0?"-":$rkQty;
										  
										  if($Mantissa>0){
											  $MantissaColor = "style='color:#FF6600;font-weight: bold;'";
										  }else{
											  $Mantissa="-";
										  }
							           } 
							           if($DeliveryWeek>0){
											  include "../model/subprogram/deliveryweek_toweek.php";
									   }  
						            }    
			                }else{
				                $Mantissa ="-";
				                $DeliveryWeek ="-";
				                $rkQty ="-";
				                $tStockQty ="-";
				                switch($mainType){
					                case "2":
					                   $cgDate = "统计项目";
					                break;
					                case "3":
					                   $cgDate = "生产项目";
					                break;
				                }
			                } 
			                
			                
			                
			                		//备领料情况
					  $llQty="-";$llBgColor="";$llEstate="";    $blorder="";
					  if($blSign==1) {	
					 
			             
						 $checkllQty=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet 
						 WHERE StockId='$StockId'",$link_id));
					     $llQty=$checkllQty["llQty"];
						 $llQty=$llQty==""?0:$llQty;
						 if($llQty>$OrderQty){//领料总数大于订单数,提示出错
							    $llBgColor=" style='color:#FF0000;font-weight: bold;'";
						}
						else{
							if($llQty==$OrderQty){//刚好全领，绿色
							    $llBgColor=" style='color:#009900;font-weight: bold;'";
							}
							else{				//未领完，黄色
								$llBgColor=" style='color:#FF6633;font-weight: bold;'";
							}
						}
						 $llEstate=$checkllQty["llEstate"];
						 $llEstate=$llEstate>0?"★":"";
					  }
			                 
			           
			           $CheckProcessSql=mysql_query("SELECT A.Id FROM $DataIn.process_bom A  
			                WHERE   A.StuffId='$StuffId'  LIMIT 1",$link_id);
                       if($CheckProcessRow=mysql_fetch_array($CheckProcessSql)){
                              $toDate=date("Y-m-d");
                              $showProcess="<img onClick='ShowDropTable(ProcessTable_$mStockId$tId,showtable_$mStockId$tId,ProcessDiv_$mStockId$tId,\"processbom_ajax\",\"$StuffId|$StockId|$OrderQty\",\"admin\");'  src='../images/showtable.gif' title='显示或隐藏加工工序资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='showtable_$mStockId$tId'>";
			                  $ProcessTable="<tr id='ProcessTable_$mStockId$tId' style='display:none;'><td colspan='$colspan'><div id='ProcessDiv_$mStockId$tId' width='720'></div></td></tr>"; 
                              $tId++;
                              
                            }
			           
				          if($ComboxMainSign==1){
				                   $ListId=getRandIndex();      
				                   $showProcess="<img onClick='ShowDropTable(ShowTable$ListId,ShowGif$ListId,ShowDiv$ListId,\"stuffcombox_pand_ajax\",\"$StockId|$StuffId\",\"admin\");' name='ShowGif$ListId' src='../images/showtable.gif' 
							title='显示或隐藏子配件资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' >";
							       $ProcessTable="<tr id='ShowTable$ListId' style='display:none'><td colspan='25'><div id='ShowDiv$ListId' width='720'></div></td></tr>";
				             }
			                     
				    }
				    else{
				         
					    $CheckProcessSql="SELECT A.Id FROM $DataIn.semifinished_bom A  WHERE  A.mStuffId='$StuffId' LIMIT 1";
					    $CheckProcessResult=mysql_query($CheckProcessSql,$link_id);
			            if($CheckProcessRow=mysql_fetch_array($CheckProcessResult)){
			                $ListId=getRandIndex();
	                        $showProcess="<img onClick='ShowDropTable(semibomTable_$ListId,showtable_$ListId,semibomDiv_$ListId,\"semifinishedbom_ajax\",\"$StuffId|$ListId\",\"admin\");'  src='../images/showtable.gif'  title='显示或隐藏多级BOM资料.' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='showtable_$ListId'>";
						    $ProcessTable="<tr id='semibomTable_$ListId' style='display:none;background:#83b6b9;'><td colspan='$colspan'><div id='semibomDiv_$ListId' width='650'></div></td></tr>"; 
						    $tId++;
			             }
			           
				    }
		    $CutDrawing="";
            $CutStr = "";
            if($blSign==1) {
               //显示刀模图片
               include "../pt/slice_cutdie_show.php";
            } 
			$lockcolor=''; $lockState=1;
			$TableCellId=$TableId . '_' . $k;
			$lock="<div title='采购未锁定' > <img src='../images/unlock.png' width='15' height='15'> </div>";
			$CheckSignSql=mysql_query("SELECT Id,Remark FROM $DataIn.cg1_lockstock WHERE StockId ='$StockId' AND Locks=0 LIMIT 1",$link_id);
			if($CheckSignRow=mysql_fetch_array($CheckSignSql)){
			   $lockRemark=$CheckSignRow["Remark"];
				$lock="<div style='background-color:#FF0000' title='原因:$lockRemark'> <img src='../images/lock.png' width='15' height='15'></div>";
				$lockState=0;
			  }
              $OnclickStr="onclick='updateLock(\"$TableCellId\",$StockId,$lockState)' style='CURSOR: pointer;'"; 
				  
				    				     
               if ($DeliveryWeek=='0') $DeliveryWeek='-';
				//配件名称
				echo "<tr bgcolor='$TypeColor'>";
				echo "<td id='$TableCellId' bgcolor='$lockcolor' align='center' height='21' width='20' $OnclickStr >$lock</td>";
				echo"<td  align='center' height='21' >$showProcess</td>";
				echo"<td  align='center'>$k</td>";
				
				if ($mStockId!=""){
				  echo"<td  align='center'>$cgDate</td>";
				  echo"<td  align='center'>$StockId</td>";
				}
				echo"<td  align='center'>$StuffId</td>";
			    
			
				echo"<td>$StuffCname</td>";
				echo"<td align='center'>$Gfile</td>";
				echo"<td align='center'>$OrderQtyInfo</td>";
				echo"<td align='center'>$Unit</td>";
				echo"<td align='center'>$Price</td>";
			
				
				echo"<td align='right'>$CutStr</td>";
				echo"<td align='right'>$CutDrawing</td>";
			
				echo"<td align='center'>$Relation</td>";
				if ($mStockId>0){
					echo"<td align='right'>$OrderQty</td>";
					echo"<td align='right'>$tStockQty</td>";
					echo"<td align='right' $llBgColor>$llEstate $llQty</td>";
					echo"<td align='center'>$DeliveryWeek</td>";
				}
				
				echo"<td align='center'>&nbsp;$Name</td>";
			    echo"<td align='center'>&nbsp;$Forshort</td>";
			    echo"<td align='center'>&nbsp;$FloorName</td>";
				echo"</tr>";
				
				echo $ProcessTable;
				$k++;
			} while ($StuffMyrow = mysql_fetch_array($StuffResult));
		}
   else{
	   echo"<tr><td height='30' colspan='$colspan' >没有设置原材料配件资料,请检查.</td></tr>";
   }
echo"</table>";

if($mStockId>0){
    $checkLevelResult = mysql_fetch_array(mysql_query("SELECT Level FROM $DataIn.cg1_stocksheet WHERE StockId = $mStockId",$link_id));
	$thisLevel = $checkLevelResult["Level"];
	if($thisLevel==1)include("semifinished_order_ajaxm.php"); //原材料明细
}

if ($FlowSign==1){
        echo"<table  width='$subTableWidth' cellspacing='1' border='0' align='left' style='margin:20px 0px 20px 60px;'><tr bgcolor='#FFFFFF'>";
		echo "<td><img  src='../public/bomflow/semi_orderflow.php?POrderId=$POrderId&mStockId=$mStockId' onload='imgAutoSize(this)'/><td>";
		echo"</tr></table>";	
}
?>