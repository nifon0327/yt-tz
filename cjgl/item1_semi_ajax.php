<?php   
//电信-zxq 2012-08-01
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
$TableId="ListTB".$RowId;
$subTableWidth=1350;
//来自于生产登记页面


echo"<table id='$TableId'  cellspacing='0' border='0' align='center' bgcolor='#FFFFFF'>
    <tr bgcolor='#D9D9D9'>
	<td width='35' height='20' class='A1111'>序号</td>
	<td width='70' align='center' class='A1101'>采购日期</td>
	<td width='90' align='center' class='A1101'>待购流水号</td>
	<td width='280' align='center' class='A1101'>配件名称</td>
    <td width='40' align='center' class='A1101'>QC图</td>
	<td width='45' align='center' class='A1101'>历史<br>订单</td>
	<td width='30' align='center' class='A1101'>单位</td>
	<td width='50' align='center' class='A1101'>订单<br>数量</td>
	<td width='50' align='center' class='A1101'>已用<br>库存</td>
	<td width='55' align='center' class='A1101'>需购<br>数量</td>
	<td width='50' align='center' class='A1101'>增购<br>数量</td>
    <td width='50' align='center' class='A1101'>收货<br>数量</td>
    <td width='50' align='center' class='A1101'>在库</td>
	<td width='50' align='center' class='A1101'>已备<br>料数</td>
	<td width='50' align='center' class='A1101'>已生<br>产数</td>
	<td width='50' align='center' class='A1101'>采购</td>
	<td width='55' align='center' class='A1101'>存储<br>位置</td>
	</tr>";
$ordercolor=3;
$sListResult = mysql_query("SELECT S.Id,S.Mid,S.StockId,S.POrderId,S.StuffId,S.Price,A.OrderQty,S.StockQty,
                    S.AddQty,S.FactualQty,S.CompanyId,S.BuyerId,S.DeliveryDate,
                    M.Date,D.StuffCname,D.Picture,D.Gfile,D.Gstate,D.TypeId,D.DevelopState,
                    B.Name,C.Forshort,C.Currency,MP.Name AS Position,
                    ST.mainType,MT.TypeColor,MT.TitleName,MT.blSign,U.Name AS UnitName,
                    K.tStockQty,MT.blSign
FROM  $DataIn.cg1_semifinished   A 
LEFT JOIN $DataIn.cg1_stocksheet S  ON S.StockId   = A.StockId
LEFT JOIN $DataIn.cg1_stockmain  M  ON M.Id        = S.Mid 
LEFT JOIN $DataIn.stuffdata      D  ON D.StuffId   = S.StuffId 
LEFT JOIN $DataIn.stufftype      ST ON ST.TypeId   = D.TypeId
LEFT JOIN $DataIn.stuffunit      U  ON U.Id        = D.Unit 
LEFT JOIN $DataIn.stuffmaintype  MT ON MT.Id       = ST.mainType
LEFT JOIN $DataIn.base_mposition MP ON MP.Id       = D.SendFloor  
LEFT JOIN $DataIn.staffmain      B  ON B.Number    = S.BuyerId 
LEFT JOIN $DataIn.trade_object   C  ON C.CompanyId = S.CompanyId 
LEFT JOIN $DataIn.ck9_stocksheet K  ON K.StuffId   = D.StuffId 
WHERE  A.mStockId='$mStockId'",$link_id);
$i=1; //,K.tStockQty 

$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
if($StockRows = mysql_fetch_array($sListResult)) {
	do{
/////////////////////////////
			//颜色	0绿色	1白色	2黄色	3绿色
			//初始化
			$rkQty=0;		$thQty=0;		$bcQty=0;		$llQty=0;	$scQty="-";
			$OnclickStr="";
			$Mid=$StockRows["Mid"];
			$thisId=$StockRows["Id"];
			$StockId=$StockRows["StockId"];
			$StuffCname=$StockRows["StuffCname"];
			$Position=$StockRows["Position"]==""?"未设置":$StockRows["Position"];
			$Price=$StockRows["Price"];
			$Forshort=$StockRows["Forshort"];
			$Buyer=$StockRows["Name"];
			$BuyerId=$StockRows["BuyerId"];
			$OrderQty=$StockRows["OrderQty"];
			$StockQty=$StockRows["StockQty"];
			$FactualQty=$StockRows["FactualQty"];
			$AddQty=$StockRows["AddQty"];
			$Date=$StockRows["Date"];
			$UnitName=$StockRows["UnitName"]==""?"&nbsp;":$StockRows["UnitName"];
			$DeliveryDate=$StockRows["DeliveryDate"];		
			$StuffId=$StockRows["StuffId"];
			$Picture=$StockRows["Picture"];
			$TypeId=$StockRows["TypeId"];
			$mainType=$StockRows["mainType"];
			$TypeColor=$StockRows["TypeColor"];
			$Currency=$StockRows["Currency"];
            $tStockQty=$StockRows["tStockQty"];
            
            $blSign=$StockRows["blSign"];
		   if($blSign==1){
		       if($FactualQty==0 && $AddQty==0){
				  $Date="使用库存";
				  $TempColor = 3;
				  $FactualQty="-";$AddQty="-";$rkQty="-";$Mantissa="-";  
		       }
		       else{
		           if ($Mid==0){ //未下采购单
			           $TempColor = 1;
			           $Date="未下采购单";
			           $rkQty="-";$Mantissa="-";
		           }
		           else{
					  //收货情况	
					  $TempColor=3;			
					  $rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet where StockId='$StockId' order by StockId",$link_id);
					  $rkQty=mysql_result($rkTemp,0,"Qty");
					  $Mantissa=$FactualQty+$AddQty-$rkQty;
		           }
		       }
		    }  
            
                        
            $llQty=0;	
            $scQty="-";           
			//检查是否有图片
			include "../model/subprogram/stuffimg_model.php";
            include"../model/subprogram/stuff_Property.php";//配件属性
			if($mainType==3){
				//生产数量
				$Date = "生产项目";
				$scResult=mysql_fetch_array(mysql_query("SELECT ifnull(SUM(S.Qty),0) AS scQty
					FROM $DataIn.sc1_cjtj S
					WHERE S.StockId='$StockId' ",$link_id)); 
				$scQty=$scResult["scQty"];
				$TempColor=$OrderQty==$scQty?3:2;	
			 }			


                   
			//备领料情况
		  $llQty=0;$llBgColor="";$llEstate="";
		  if($mainType<=1) {	
			 $checkllQty=mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE  StockId='$StockId'",$link_id);
		     $llQty=mysql_result($checkllQty,0,"llQty");
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
			 $llEstate=mysql_result($checkllQty,0,"llEstate");
			 $llEstate=$llEstate>0?"★":"";
		    }
			///////////////////////////////////////////
            //库存数量    
            if($mainType<=1) {    
                if ($tStockQty>=$OrderQty-$llQty){
                    $tStockQty="<div style='color:#009900;font-weight: bold;'>$tStockQty</div>";
                }else{
                    $tStockQty="<div style='color:#FF6633;font-weight: bold;'>$tStockQty</div>";
                }
            }else{
                 $tStockQty="-"; 
            }  
            $llQty=$llQty==0?"-":$llQty;
            //配件QC检验标准图
            $QCImage="";
            include "../model/subprogram/stuffimg_qcfile.php";
            $QCImage=$QCImage==""?"&nbsp;":$QCImage;
            //历史订单
			$OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>view</a>";
		    
		    
		     //采单颜色标记
		    switch($TempColor){
				case 1://白色
					$Sbgcolor="#FFFFFF";
					break;
				case 2://黄色
					$Sbgcolor="#FFCC00";
					break;
				case 3://绿色
					$Sbgcolor="#339900";
					break;
			} 
		    
		    $showStr="&nbsp;"; 
		    $showTable="";
		    $CheckSemiSql=mysql_query("SELECT A.Id FROM $DataIn.cg1_semifinished A  WHERE A.mStockId='$StockId' LIMIT 1",$link_id);  
		    if($CheckSemiRow=mysql_fetch_array($CheckSemiSql)){
		        //显示或隐藏bom
		        $ajaxFile="semifinishedbom_ajax";
		        $ajaxDir="admin";
		        
		        if (in_array($APP_CONFIG['PT_CUT_PROPERTY'],$StuffPropertys)){
			        $ajaxFile="slicebom_ajax";
			        $ajaxDir="pt";
		        }
		        
		        $ShowId=$RowId . "_" . $i;
		        $ShowBomImageId= "Bom_StuffImage_" . $ShowId;
		        $ShowBomTableId= "Bom_StuffTable_" . $ShowId;
		        $ShowBomDivId  = "Bom_StuffDiv_" . $ShowId;
		        
		        $showStr = "<img onClick='ShowDropTable($ShowBomTableId,$ShowBomImageId,$ShowBomDivId,\"$ajaxFile\",\"$StockId|$ShowId\",\"$ajaxDir\");'  src='../images/showtable.gif' 
			title='显示或隐藏原材料' width='13' height='13' style='CURSOR: pointer' bgcolor='$theDefaultColor' name='$ShowBomImageId'>";
			    $showTable = "<tr id='$ShowBomTableId' style='display:none'><td colspan='31'><div id='$ShowBomDivId' width='$subTableWidth'></div></td></tr>";   
		    }
		    
		    
			echo"<tr bgcolor='$TypeColor'>
			<td bgcolor='$Sbgcolor' align='center' height='20' class='A0111'>$showStr $i</td>";
			echo"<td  align='center' class='A0101'>$Date</td>";
			echo"<td  align='center' class='A0101'>$StockId</td>";//配件采购流水号
			echo"<td $ChangeStuff class='A0101'>$StuffCname</td>";//配件名称
            echo"<td class='A0101' align='center'>$QCImage</td>";//QC图
			echo"<td class='A0101' align='center'>$OrderQtyInfo</td>";//历史订单
			echo"<td class='A0101' align='center'>$UnitName</td>";
			echo"<td align='right' class='A0101'>$OrderQty</td>";//订单需求数量
			echo"<td align='right' class='A0101'>$StockQty</td>";//使用库存数
			echo"<td align='right' class='A0101'>$FactualQty</td>";//采购数量
			echo"<td align='right' class='A0101'>$AddQty</td>";//增购数量
            echo"<td align='right' class='A0101'>$rkQty</td>";//收货数量
            echo"<td align='right' class='A0101'>$tStockQty</td>";//在库
			echo"<td align='right' class='A0101' $llBgColor> $llEstate $llQty</td>";//领料数
			echo"<td align='right' class='A0101'>$scQty</td>";//生产数量
			echo"<td  align='center' class='A0101'>$Buyer</td>";//采购员
			echo"<td align='center' class='A0101'>$Position</td>";//仓库位置
			echo"</tr>";
			
			 echo $showTable;		
			$i++;
/////////////////////////////////////
			}while ($StockRows = mysql_fetch_array($sListResult));	
		}
echo"</table>";

?>