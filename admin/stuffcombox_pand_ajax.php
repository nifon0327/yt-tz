
<?php   
//传入参数：$ProductId 、$StuffId、$OrderQty 电信---yang 20120801

include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../basic/config.inc";
include "../model/modelfunction.php";
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");

$Operator=$Login_P_Number;
$dataArray=explode("|",$args);
$mStockId=$dataArray[0];
$mStuffId=$dataArray[1];	
//echo $mStuffId;		
include "stuffcombox_pand_updaterk.php";
	
echo"<table  cellspacing='1' border='1' align='left' style='margin-left:103px;margin-top:10px' ><tr bgcolor='#75AC4A'>
<td width='30'  align='center'>序号</td>
<td width='90' align='center'>采购流水号</td>
<td width='40' align='center'>配件ID</td>
<td width='330' align='center'>配件名称</td>				
<td width='40' align='center'>图档</td>
<td width='40' align='center'>QC图</td>
<td width='50' align='center'>品检报告</td>
<td width='55' align='center'>历史订单</td>	
<td width='40' align='center'>单位</td>
<td width='55' align='center'>订单数量</td>
<td width='55' align='center'>已用库存</td>
<td width='55' align='center'>需购数量</td>
<td width='55' align='center'>增购数量</td>
<td width='55' align='center'>在库</td>
 <td width='55' align='center'>采购</td>
<td width='125' align='center'>供应商</td>
<td width='55' align='center'>收货数量</td>
<td width='55' align='center'>欠数</td>
<td width='60' align='center'>已备料数</td>
<td width='90' align='center'>交货期</td></tr>";

$ComboxResult=mysql_query("SELECT S.Id,S.mStockId,S.StockId,S.POrderId,S.StuffId,S.OrderQty,S.StockQty,S.AddQty,S.FactualQty,G.CompanyId,G.BuyerId,G.DeliveryDate,
A.StuffCname,A.Picture,A.Gfile,A.Gstate,A.TypeId,B.Name,C.Forshort,C.Currency,U.Name AS UnitName,K.tStockQty
FROM  $DataIn.cg1_stuffcombox   S  
LEFT JOIN $DataIn.cg1_stocksheet   G ON G.StockId = S.mStockId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
LEFT JOIN $DataIn.stuffunit U ON U.Id=A.Unit 
LEFT JOIN $DataIn.staffmain B ON B.Number=G.BuyerId
LEFT JOIN $DataIn.trade_object C ON C.CompanyId=G.CompanyId 
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=A.StuffId 
WHERE  S.mStuffId='$mStuffId' AND S.mStockId=$mStockId",$link_id);
$i=1;
$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);
if ($ComboxRow = mysql_fetch_array($ComboxResult)) {
	do{
		$rkQty=0;		
		$llQty=0;
		$thisId=$ComboxRow["Id"];
		$StockId=$ComboxRow["StockId"];
		$StuffCname=$ComboxRow["StuffCname"];
		$Price=$ComboxRow["Price"];
		$Forshort=$ComboxRow["Forshort"];
		$Buyer=$ComboxRow["Name"];
		$UnitName=$ComboxRow["UnitName"]==""?"&nbsp;":$ComboxRow["UnitName"];
		$BuyerId=$ComboxRow["BuyerId"];
		$OrderQty=$ComboxRow["OrderQty"];
		$StockQty=$ComboxRow["StockQty"];
		$AddQty=$ComboxRow["AddQty"];
		$FactualQty=$ComboxRow["FactualQty"];
		$DeliveryDate=$ComboxRow["DeliveryDate"];		
		$StuffId=$ComboxRow["StuffId"];
		$Picture=$ComboxRow["Picture"];
		$Currency=$ComboxRow["Currency"];
		$Gfile=$ComboxRow["Gfile"];
		$Gstate=$ComboxRow["Gstate"];  //状态
        $tStockQty=$ComboxRow["tStockQty"];  
     	
		include "../model/subprogram/stuffimg_Gfile.php";	//图档显示	
		//检查是否有图片
		include "../model/subprogram/stuffimg_model.php";
        include"../model/subprogram/stuff_Property.php";//配件属性   
        //配件QC检验标准图
        include "../model/subprogram/stuffimg_qcfile.php";
                     
        //配件品检报告qualityReport
        include "../model/subprogram/stuff_get_qualityreport.php";
            
		//收货情况				
		$rkTemp=mysql_query("SELECT ifnull(SUM(Qty),0) AS Qty FROM $DataIn.ck1_rksheet where StockId='$StockId' order by StockId",$link_id);
		$rkQty=mysql_result($rkTemp,0,"Qty");
		$Mantissa=$AddQty+$FactualQty-$rkQty;
		$rkQty=$FactualQty==0?"-":$rkQty;
		$Mantissa=$FactualQty==0?"-":$Mantissa;
		
		//可更新交期,如果当前浏览者的ID与采购的ID一致，则可以更新交期
		if($DeliveryDate=="0000-00-00"){
		        $DeliveryDate="-";
		 }
		 else{
		     $DateShow_Style=1;
		     include "../model/subprogram/CG_DeliveryDate.php";
		 }
         $blDateResult=mysql_fetch_array(mysql_query("SELECT S.created,M.Name
         FROM $DataIn.ck5_llsheet S 
         LEFT JOIN $DataPublic.staffmain M ON M.Number=S.Operator
         WHERE S.StockId='$StockId' ORDER BY S.Date Limit 1",$link_id));
         $blDate=substr($blDateResult["Date"],0,16);
         $blName=$blDateResult["Name"];
		 $checkllQty=mysql_fetch_array(mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE StockId='$StockId' ",$link_id));
	     $llQty=$checkllQty["llQty"];
		 $llQty=$llQty==""?0:$llQty;
		 if($llQty>$OrderQty){//领料总数大于订单数,提示出错
			    $llBgColor=" style='color:#FF0000;font-weight: bold;' title='备料时间:$blDate,备料人:$blName'";
			    }
		else{
			if($llQty==$OrderQty){//刚好全领，绿色
			  $llBgColor=" style='color:#009900;font-weight: bold;'  title='备料时间:$blDate,备料人:$blName'";
		     }
			else{				//未领完，黄色
				    $llBgColor=" style='color:#FF6633;font-weight: bold;'";
			  }
		  }
            
	    echo"<tr bgcolor=#EAEAEA>";
	    echo"<td align='center'>$i</td>";	 
	    echo"<td align='center'>$StockId</td>";	
	     echo"<td align='center'>$StuffId</td>";	 
        echo"<td>$StuffCname</td>";	 
	    echo"<td align='center'>$Gfile</td>";	 
	    echo"<td align='center'>$QCImage</td>";	 
        echo"<td  align='center'>$qualityReport</td>";//品检报告
        $OrderQtyInfo="<a href='../public/cg_historyorder.php?StuffId=$StuffId&Id=$thisId' target='_blank'>查看</a>";
		echo"<td  align='center'>$OrderQtyInfo</td>";//历史订单分析
			
		echo"<td  align='center'>$UnitName</td>";//单位
		echo"<td align='right'>$OrderQty</td>";//订单需求数量
		echo"<td align='right'>$StockQty</td>";//使用库存数
		echo"<td align='right'>$FactualQty</td>";//采购数量
		echo"<td align='right'>$AddQty</td>";//增购数量
        echo"<td align='right'>$tStockQty</td>";//在库
        echo"<td  align='center'>$Buyer</td>";//采购员
	    echo"<td >$Forshort</td>";//供应商
		echo"<td align='right'>$rkQty</td>";//收货进度
		echo"<td><div align='right' style='color:#FF6600;font-weight: bold;'>$Mantissa</div></td>";
		echo"<td align='right'  $llBgColor> $llEstate $llQty $blorder</td>";//领料数
		echo"<td align='center'>$DeliveryDate</td>";//供应商交货期
		echo"</tr>";
		$i++;
 	   }while ($ComboxRow = mysql_fetch_array($ComboxResult));
 	   
	}else{
		$addcomboxBtn=in_array($Login_GroupId,$APP_CONFIG['IT_DEVELOP_GROUPID'])?"<input type='button' name='addcomboxBtn' value='新增关系' onclick='addStuffComBox(this,$mStockId)'/>":"&nbsp;";
		
		echo "<tr><td colspan='18'>$addcomboxBtn</td></tr>";
	}
echo"</table>";
?>