<?php   
/*
 * 皮套专用 zhongxq-2015-10-13
 */

include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$dataArray=explode("|",$args);
$mStockId=$dataArray[0];
$RowId=$dataArray[1];
$FlowSign=$dataArray[2];
if ($FlowSign==1){
    $margin_left="60px";
	include "../admin/semifinished_scsheet_ajax.php";
	echo "<br><div style='height:50px;'>&nbsp;</div>";
}

echo"<table  cellspacing='1' border='1' align='left' style='table-layout:fixed;word-break:break-all; word-wrap:break-word;margin:2px 5px 10px 60px;' ><tr bgcolor='#CCCCCC'>
         <td width='20'  align='center'>&nbsp;</td>
         <td width='20'  align='center'>&nbsp;</td>
		 <td width='30'  align='center'>NO.</td>
		 <td width='100'  align='center'>采购流水号</td>
         <td width='40'  align='center'>配件ID</td>
		 <td width='380' align='center'>配件名称</td>
		 <td width='55' align='center'>历史订单</td>
         <td width='30'  align='center'>单位</td>
         <td width='60' align='center'>单价</td>
         <td width='150'  align='center'>刀模编号</td>
         <td width='50'  align='center'>刀模图档</td>
		 <td width='60' align='center'>片数/码</td>
		 <td width='60'  align='center'>需求数量</td>
		 <td width='60' align='center'>已登记数</td>
		 <td width='60' align='center'>登记时间</td>
		 <td width='60' align='center'>备料数量</td>
		 <td width='60' align='center'>备料时间</td></tr>";
		 
    //从配件表和配件关系表中提取配件数据	  
    $StuffSql="SELECT (G.AddQty+G.FactualQty) AS mOrderQty,
	           A.Id,A.Relation,A.mStuffId,A.StockId,A.StuffId,A.OrderQty,A.Relation,
	           D.StuffCname,D.Picture,D.Gfile,D.Gstate,D.Gremark,D.TypeId,D.SendFloor,
	           U.Name AS Unit,IF(T.mainType IN (2,3),1,0) AS scSign,
	           M.TypeColor,M.SortId,G.blSign,CG.Price   
			   FROM  $DataIn.cg1_stocksheet G 
			   LEFT JOIN $DataIn.cg1_semifinished A  ON G.StockId=A.mStockId 
			   LEFT JOIN $DataIn.cg1_stocksheet CG ON CG.StockId = A.StockId
               LEFT JOIN $DataIn.stuffdata D  ON D.StuffId=A.StuffId 
               LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit 
		       LEFT JOIN $DataIn.stufftype T ON T.TypeId=D.TypeId
		       LEFT JOIN $DataIn.stuffmaintype M ON M.Id=T.mainType 
               WHERE G.StockId='$mStockId' ORDER BY SortId";

			$StuffResult = mysql_query($StuffSql,$link_id);
			$k=1;$tId=1;
			//echo $StuffSql;
			if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
			     $d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
			     $dt=anmaIn("download/cut_data/",$SinkOrder,$motherSTR);
			     $dw=anmaIn("download/cut_drawing/",$SinkOrder,$motherSTR);
			     $today=date("Y-m-d");
				do{	
					$n=$m;
					$PandsId=$StuffMyrow["Id"];
					$mStuffId=$StuffMyrow["mStuffId"];
					$StuffId=$StuffMyrow["StuffId"];
					$StockId=$StuffMyrow["StockId"];
					$StuffCname=$StuffMyrow["StuffCname"];
					$TypeId=$StuffMyrow["TypeId"];
					$mainType=$StuffMyrow["mainType"];
					$Price=$StuffMyrow["Price"];
					$blSign=$StuffMyrow["blSign"];
                    $Unit=$StuffMyrow["Unit"]==""?"&nbsp;":$StuffMyrow["Unit"];
                    
                    $TypeColor=$StuffMyrow["TypeColor"];

					$Relation=$StuffMyrow["Relation"];
					$Price=$StuffMyrow["Price"];
					//检查是否有图片
					$Picture=$StuffMyrow["Picture"];
					include "../model/subprogram/stuffimg_model.php";
					include "../model/subprogram/stuff_Property.php";	//属性显示

					$SendFloor=$StuffMyrow["SendFloor"];
					include "../model/subprogram/stuff_GetFloor.php";
					$FloorName=$FloorName=""?"&nbsp":$FloorName;
					$OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=$thisId' target='_blank'>查看</a>";
					
					
			        //刀模图档
				    $CutDrawing=""; $CutStr = "";
	                $showStr="&nbsp;";$showTable="";
	                
	                $scSign=$StuffMyrow["scSign"];
	               if ($scSign==0){
				    include "slice_cutdie_show.php";
				    
				    $CheckSemiSql=mysql_query("SELECT A.Id FROM $DataIn.cg1_semifinished A  WHERE A.mStockId='$StockId' LIMIT 1",$link_id);  
				    if($CheckSemiRow=mysql_fetch_array($CheckSemiSql)){
				        //显示或隐藏bom
				        $ajaxFile="semifinishedbom_ajax";
                        $ajaxDir="admin";
				
				        if (in_array($APP_CONFIG['PT_CUT_PROPERTY'],$StuffPropertys)){
					        $ajaxFile="slicebom_ajax";
					        $ajaxDir="pt";
				        }
				        
				        $ShowId=getRandIndex();
				        $ShowBomImageId= "Bom_StuffImage_" . $ShowId;
				        $ShowBomTableId= "Bom_StuffTable_" . $ShowId;
				        $ShowBomDivId  = "Bom_StuffDiv_" . $ShowId;
				        
				        $showStr = "<img onClick='ShowDropTable($ShowBomTableId,$ShowBomImageId,$ShowBomDivId,\"$ajaxFile\",\"$StockId|$ShowId\",\"$ajaxDir\");'  src='../images/showtable.gif' 
					title='显示或隐藏原材料' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='$ShowBomImageId'>";
					    $showTable = "<tr id='$ShowBomTableId' style='display:none'><td colspan='31'><div id='$ShowBomDivId' width='$subTableWidth'></div></td></tr>";   
				    }
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
                
					echo "<tr bgcolor=$TypeColor>";
					echo "<td id='$TableCellId' bgcolor='$lockcolor' align='center' height='21' width='20' $OnclickStr >$lock</td>";
					echo"<td  align='center' >$showStr</td>";
					echo"<td  align='center'>$k</td>";
					echo"<td  align='center'>$StockId</td>";
					echo"<td  align='center'>$StuffId</td>";
					echo"<td>$StuffCname</td>";
					echo"<td align='center'>$OrderQtyInfo</td>";
					echo"<td align='center'>$Unit</td>";
					echo"<td align='center'>$Price</td>";
					echo"<td> $CutStr</td>";
					echo"<td align='center'>$CutDrawing</td>";
					
					    
				    $sRelation=explode("/",$Relation);
				     if (count($sRelation)>1){
						$pcsQty=floor($sRelation[1]/$sRelation[0]);//片数/码
					}
					else{
						  $pcsQty=$sRelation[0];
					}
                    $mOrderQty=round($StuffMyrow['mOrderQty']);
					$OrderQty=$StuffMyrow['OrderQty'];
					
                 $llDate="&nbsp;";$klDate="&nbsp;";$klQty="&nbsp;";
                 
                
                 if ($blSign==1){   
                    //检查是否已备料
                 
	                $llSql=mysql_query("SELECT SUM(S.Qty) AS llQty,Max(S.Date) AS Date 
	                FROM  $DataIn.ck5_llsheet S 
	                LEFT JOIN $DataIn.yw1_scsheet A ON A.sPOrderId=S.sPOrderId  
	                WHERE  S.StockId='$StockId' AND S.StuffId='$StuffId'  AND A.mStockId='$mStockId'",$link_id);
	  
	                if($llRows=mysql_fetch_array($llSql)){
	                   $llQty = $llRows["llQty"];
	                   $llDate=$llQty==$OrderQty?$llRows["Date"]:"<div style='color:#FFAA00' title='已备数量:" .$llRows["llQty"] . "'>".$llRows["Date"]. "</div>"; 
	                   
	                   if ($llQty==$OrderQty) $llQty="<div style='color:#009900'>" .$llQty . "</div>";
	                }
	              }
	                $klSql=mysql_query("SELECT Max(K.Date) AS Date,SUM(Qty) AS klQty FROM $DataIn.sc1_cjtj  K  WHERE  K.StockId='$StockId' ",$link_id);
	                if($klRows=mysql_fetch_array($klSql)){
	                        $klQty=$klRows["klQty"];
	                        $klDate=$klQty>=$pcsQty?$klRows["Date"]:"<div style='color:#FFAA00'>".$klRows["Date"]. "</div>";
	                }
	
                    echo"<td align='center'>$pcsQty</td>";
					echo"<td align='center'>$OrderQty</td>";
					
					echo"<td align='center'>$klQty</td>";
					echo"<td align='center'>$klDate</td>";
					echo"<td align='center'>$llQty</td>";
					echo"<td align='center'>$llDate</td>";
					echo"</tr>";
					echo $showTable;
					$k++;
					} while ($StuffMyrow = mysql_fetch_array($StuffResult));
		}//if($StuffMyrow=mysql_fetch_array($StuffResult)) {//如果设定了产品配件关系
   else{
	   echo"<tr><td height='30' colspan='9' >没有设置原材料配件资料,请检查.</td></tr>";
   }
echo"</table>";
?>          

